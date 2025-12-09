<?php

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'Controller.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'SessionHelper.php';

class ProfileController extends Controller {
    
    private $userModel;

    public function __construct() {
        // Check if user is logged in
        if (!SessionHelper::isLoggedIn()) {
            $this->redirect('auth/login');
        }
        
        $this->userModel = $this->model('User');
    }

    public function index() {
        $userId = SessionHelper::getUserId();
        $user = $this->userModel->findById($userId);
        
        if (!$user) {
            $this->redirect('auth/logout');
            return;
        }
        
        $data = [
            'user' => $user,
            'page_title' => 'My Profile'
        ];
        
        // Load different view based on account type
        if ($user['account_type'] === 'client') {
            $this->view('client/profile', $data);
        } elseif ($user['account_type'] === 'freelancer') {
            $this->view('freelancer/profile', $data);
        } else {
            $this->view('admin/profile', $data);
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }
        
        $userId = SessionHelper::getUserId();
        $errors = [];
        
        // Validate input
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $phoneNumber = trim($_POST['phone_number'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        
        if (empty($firstName)) {
            $errors['first_name'] = 'First name is required';
        }
        
        if (empty($lastName)) {
            $errors['last_name'] = 'Last name is required';
        }
        
        // Password update (optional)
        if (!empty($_POST['password'])) {
            if (strlen($_POST['password']) < 6) {
                $errors['password'] = 'Password must be at least 6 characters';
            }
            
            if ($_POST['password'] !== $_POST['confirm_password']) {
                $errors['confirm_password'] = 'Passwords do not match';
            }
        }
        
        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
            return;
        }
        
        // Update user
        $sql = "UPDATE users SET first_name = :first_name, last_name = :last_name, 
                phone_number = :phone_number, bio = :bio, updated_at = CURRENT_TIMESTAMP";
        
        $params = [
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':phone_number' => $phoneNumber,
            ':bio' => $bio
        ];
        
        if (!empty($_POST['password'])) {
            $sql .= ", password = :password";
            $params[':password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }
        
        $sql .= " WHERE id = :id";
        $params[':id'] = $userId;
        
        $success = $this->userModel->update($sql, $params);
        
        if ($success) {
            // Update session
            SessionHelper::set('first_name', $firstName);
            SessionHelper::set('last_name', $lastName);
            
            $this->json([
                'success' => true,
                'message' => 'Profile updated successfully'
            ]);
        } else {
            $this->json([
                'success' => false,
                'message' => 'Failed to update profile'
            ], 500);
        }
    }

    public function uploadAvatar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['avatar'])) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
            return;
        }
        
        $userId = SessionHelper::getUserId();
        $file = $_FILES['avatar'];
        
        // Validate file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            $this->json(['success' => false, 'message' => 'Invalid file type'], 400);
            return;
        }
        
        if ($file['size'] > $maxSize) {
            $this->json(['success' => false, 'message' => 'File too large'], 400);
            return;
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
        $uploadPath = dirname(__DIR__) . '/../public/assets/uploads/avatars/' . $filename;
        
        // Create directory if it doesn't exist
        $directory = dirname($uploadPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Update user avatar in database
            $avatarUrl = BASE_URL . 'assets/uploads/avatars/' . $filename;
            $sql = "UPDATE users SET avatar = :avatar WHERE id = :id";
            $success = $this->userModel->update($sql, [
                ':avatar' => $avatarUrl,
                ':id' => $userId
            ]);
            
            if ($success) {
                SessionHelper::set('avatar', $avatarUrl);
                $this->json([
                    'success' => true,
                    'message' => 'Avatar updated successfully',
                    'avatar_url' => $avatarUrl
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to update avatar'], 500);
            }
        } else {
            $this->json(['success' => false, 'message' => 'Failed to upload file'], 500);
        }
    }
}