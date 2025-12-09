<?php

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'Controller.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'ValidationHelper.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'SessionHelper.php';

class AuthController extends Controller {

    // Rate limiting settings
    private const LOGIN_MAX_ATTEMPTS = 5;
    private const LOGIN_LOCK_SECONDS = 300; // 5 minutes

    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
        // Ensure session started (SessionHelper may handle it; otherwise start here)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Show login page or redirect if already logged in
     */
    public function index() {
        if (SessionHelper::isLoggedIn()) {
            $this->redirect('dashboard');
            return;
        }
        // Ensure a CSRF token exists for forms
        $this->ensureCsrfToken();
        $this->view('auth/login');
    }

    /**
     * Login endpoint: GET shows form, POST processes login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processLogin();
        } else {
            // GET
            if (SessionHelper::isLoggedIn()) {
                $this->redirect('dashboard');
                return;
            }
            $this->ensureCsrfToken();
            $this->view('auth/login');
        }
    }

    /**
     * Register endpoint: GET shows form, POST processes register
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processRegister();
        } else {
            if (SessionHelper::isLoggedIn()) {
                $this->redirect('dashboard');
                return;
            }
            $this->ensureCsrfToken();
            $this->view('auth/register');
        }
    }

    /**
     * Process login (POST)
     */
    private function processLogin() {
        // Check CSRF
        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'errors' => ['general' => 'Invalid CSRF token']], 400);
            return;
        }

        // Rate limiting (simple, session-based)
        $attemptsData = SessionHelper::get('login_attempts') ?? ['count' => 0, 'last_time' => 0];
        // If locked
        if (!empty($attemptsData['last_time']) && (time() - $attemptsData['last_time']) < self::LOGIN_LOCK_SECONDS && $attemptsData['count'] >= self::LOGIN_MAX_ATTEMPTS) {
            $remaining = self::LOGIN_LOCK_SECONDS - (time() - $attemptsData['last_time']);
            $this->json(['success' => false, 'errors' => ['general' => 'Too many login attempts. Try again in ' . ceil($remaining) . ' seconds.']], 429);
            return;
        }

        $errors = [];

        // Sanitize inputs
        $emailRaw = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validate email (use ValidationHelper but also sanitize)
        $emailValidation = ValidationHelper::validateEmail($emailRaw);
        if (!$emailValidation['valid']) {
            $errors['email'] = $emailValidation['error'];
        } else {
            $email = $emailValidation['value'];
        }

        // For login: only ensure password isn't empty (don't enforce strength)
        if (empty($password)) {
            $errors['password'] = 'Password is required';
        }

        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
            return;
        }

        // Fetch user by email
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            // increment attempts
            $this->incrementLoginAttempts();
            $this->json(['success' => false, 'errors' => ['general' => 'Invalid email or password']], 401);
            return;
        }

        // Verify hashed password
        if (!isset($user['password']) || !password_verify($password, $user['password'])) {
            $this->incrementLoginAttempts();
            $this->json(['success' => false, 'errors' => ['general' => 'Invalid email or password']], 401);
            return;
        }

        // Successful login: regenerate session id and set session vars
        session_regenerate_id(true);

        // Reset login attempts on success
        SessionHelper::set('login_attempts', ['count' => 0, 'last_time' => 0]);

        // Minimal, safe session storage
        SessionHelper::set('user_id', $user['id']);
        SessionHelper::set('username', $user['username']);
        SessionHelper::set('email', $user['email']);
        SessionHelper::set('first_name', $user['first_name']);
        SessionHelper::set('last_name', $user['last_name']);
        SessionHelper::set('account_type', $user['account_type']);

        // Update last login timestamp in DB (assumes model method)
        $this->userModel->updateLastLogin($user['id']);

        $this->json([
            'success' => true,
            'message' => 'Login successful',
            'redirect' => BASE_URL . 'dashboard'
        ]);
    }

    /**
     * Process registration (POST)
     */
    private function processRegister() {
        // Check CSRF
        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'errors' => ['general' => 'Invalid CSRF token']], 400);
            return;
        }

        $errors = [];
        $data = [];

        // Trim & sanitize raw inputs
        $firstNameRaw = trim($_POST['first_name'] ?? '');
        $lastNameRaw = trim($_POST['last_name'] ?? '');
        $usernameRaw = trim($_POST['username'] ?? '');
        $emailRaw = trim($_POST['email'] ?? '');
        $phoneRaw = trim($_POST['phone_number'] ?? '');
        $passwordRaw = $_POST['password'] ?? '';
        $confirmRaw = $_POST['confirm_password'] ?? '';
        $accountTypeRaw = trim($_POST['account_type'] ?? 'freelancer');

        // Validate first name
        $firstNameValidation = ValidationHelper::validateName($firstNameRaw);
        if (!$firstNameValidation['valid']) {
            $errors['first_name'] = $firstNameValidation['error'];
        } else {
            // store sanitized value
            $data['first_name'] = $firstNameValidation['value'];
        }

        // Validate last name
        $lastNameValidation = ValidationHelper::validateName($lastNameRaw);
        if (!$lastNameValidation['valid']) {
            $errors['last_name'] = $lastNameValidation['error'];
        } else {
            $data['last_name'] = $lastNameValidation['value'];
        }

        // Validate username
        $usernameValidation = ValidationHelper::validateUsername($usernameRaw);
        if (!$usernameValidation['valid']) {
            $errors['username'] = $usernameValidation['error'];
        } else {
            $usernameVal = $usernameValidation['value'];
            if ($this->userModel->usernameExists($usernameVal)) {
                $errors['username'] = 'Username already exists';
            } else {
                $data['username'] = $usernameVal;
            }
        }

        // Validate email
        $emailValidation = ValidationHelper::validateEmail($emailRaw);
        if (!$emailValidation['valid']) {
            $errors['email'] = $emailValidation['error'];
        } else {
            $emailVal = $emailValidation['value'];
            if ($this->userModel->emailExists($emailVal)) {
                $errors['email'] = 'Email already registered';
            } else {
                $data['email'] = $emailVal;
            }
        }

        // Validate phone
        $phoneValidation = ValidationHelper::validatePhone($phoneRaw);
        if (!$phoneValidation['valid']) {
            // Note: phone is optional depending on your app; adjust if required
            $errors['phone_number'] = $phoneValidation['error'];
        } else {
            $data['phone_number'] = $phoneValidation['value'];
        }

        // Validate password strength ON REGISTER (use ValidationHelper)
        $passwordValidation = ValidationHelper::validatePassword($passwordRaw);
        if (!$passwordValidation['valid']) {
            $errors['password'] = $passwordValidation['error'];
        } else {
            // We will hash before storage
            $passwordPlain = $passwordValidation['value'];
        }

        // Validate password confirmation
        $passwordMatch = ValidationHelper::validatePasswordMatch($passwordRaw, $confirmRaw);
        if (!$passwordMatch['valid']) {
            $errors['confirm_password'] = $passwordMatch['error'];
        }

        // Validate account type
        $accountTypeValidation = ValidationHelper::validateAccountType($accountTypeRaw);
        if (!$accountTypeValidation['valid']) {
            $errors['account_type'] = $accountTypeValidation['error'];
        } else {
            $data['account_type'] = $accountTypeValidation['value'];
        }

        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
            return;
        }

        // Hash password securely
        $hashed = password_hash($passwordPlain, PASSWORD_DEFAULT);
        $data['password'] = $hashed;

        // Create user
        $userId = $this->userModel->create($data);

        if ($userId) {
            // Auto login after registration
            session_regenerate_id(true);

            SessionHelper::set('user_id', $userId);
            SessionHelper::set('username', $data['username']);
            SessionHelper::set('email', $data['email']);
            SessionHelper::set('first_name', $data['first_name']);
            SessionHelper::set('last_name', $data['last_name']);
            SessionHelper::set('account_type', $data['account_type']);

            $this->json([
                'success' => true,
                'message' => 'Registration successful',
                'redirect' => BASE_URL . 'dashboard'
            ]);
        } else {
            $this->json([
                'success' => false,
                'errors' => ['general' => 'Registration failed. Please try again.']
            ], 500);
        }
    }

    /**
     * Logout user and destroy session
     */
    public function logout() {
        // Destroy SessionHelper and redirect
        SessionHelper::destroy();
        $this->redirect('auth/login');
    }

    /* ----------------- Helper methods ----------------- */

    /**
     * Ensure a CSRF token exists in session (for forms)
     */
    private function ensureCsrfToken(): void {
        $token = SessionHelper::get('csrf_token') ?? null;
        if (empty($token)) {
            $token = bin2hex(random_bytes(32));
            SessionHelper::set('csrf_token', $token);
        }
    }

    /**
     * Verify CSRF token from POST
     */
    private function verifyCsrfToken(string $token): bool {
        $sessionToken = SessionHelper::get('csrf_token') ?? '';
        // Use hash_equals for timing-attack-safe compare
        return !empty($sessionToken) && !empty($token) && hash_equals($sessionToken, $token);
    }

    /**
     * Increment login attempts in session and set lock time if necessary
     */
    private function incrementLoginAttempts(): void {
        $attempts = SessionHelper::get('login_attempts') ?? ['count' => 0, 'last_time' => 0];
        $attempts['count'] = ($attempts['count'] ?? 0) + 1;
        $attempts['last_time'] = time();
        SessionHelper::set('login_attempts', $attempts);

        if ($attempts['count'] >= self::LOGIN_MAX_ATTEMPTS) {
            // lock: keep last_time as lock start
            SessionHelper::set('login_attempts', $attempts);
        }
    }

    /**
     * Helper to send JSON response with HTTP code
     */
    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }
}
