<?php

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'Controller.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'SessionHelper.php';

class ClientController extends Controller {
    
    private $userModel;
    private $jobModel;
    private $proposalModel;
    private $savedPostModel;

    public function __construct() {
        // Check if user is logged in and is a client
        if (!SessionHelper::isLoggedIn() || SessionHelper::get('account_type') !== 'client') {
            $this->redirect('auth/login');
        }
        
        $this->userModel = $this->model('User');
        $this->jobModel = $this->model('Job');
        $this->proposalModel = $this->model('Proposal');
        $this->savedPostModel = $this->model('SavedPost');
    }

    public function index() {
        $this->dashboard();
    }

    public function dashboard() {
        $userId = SessionHelper::getUserId();
        
        // Get client stats
        $jobs = $this->jobModel->findByClient($userId);
        $totalJobs = count($jobs);
        $activeJobs = count(array_filter($jobs, function($job) {
            return $job['status'] === 'active';
        }));
        $completedJobs = count(array_filter($jobs, function($job) {
            return $job['status'] === 'completed';
        }));
        
        // Get recent proposals
        $recentProposals = [];
        foreach ($jobs as $job) {
            $proposals = $this->proposalModel->findByJob($job['id']);
            $recentProposals = array_merge($recentProposals, $proposals);
        }
        $recentProposals = array_slice($recentProposals, 0, 5);
        
        $data = [
            'user' => SessionHelper::getUserData(),
            'stats' => [
                'total_jobs' => $totalJobs,
                'active_jobs' => $activeJobs,
                'completed_jobs' => $completedJobs,
                'total_proposals' => count($recentProposals)
            ],
            'recent_jobs' => array_slice($jobs, 0, 5),
            'recent_proposals' => $recentProposals,
            'page_title' => 'Client Dashboard'
        ];
        
        $this->view('client/dashboard', $data);
    }

    public function profile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->updateProfile();
            return;
        }
        
        $data = [
            'user' => $this->userModel->findById(SessionHelper::getUserId()),
            'page_title' => 'My Profile'
        ];
        
        $this->view('client/profile', $data);
    }

    private function updateProfile() {
        $userId = SessionHelper::getUserId();
        $errors = [];
        
        // Validate input
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $phoneNumber = trim($_POST['phone_number'] ?? '');
        
        if (empty($firstName)) {
            $errors['first_name'] = 'First name is required';
        }
        
        if (empty($lastName)) {
            $errors['lastName'] = 'Last name is required';
        }
        
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
                phone_number = :phone_number, updated_at = CURRENT_TIMESTAMP";
        
        $params = [
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':phone_number' => $phoneNumber
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

    public function post() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->createPost();
            return;
        }
        
        $data = [
            'user' => SessionHelper::getUserData(),
            'page_title' => 'Create New Post'
        ];
        
        $this->view('client/post', $data);
    }

    private function createPost() {
        $errors = [];
        $data = [];
        
        $userId = SessionHelper::getUserId();
        
        // Validate input
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $budget = floatval($_POST['budget'] ?? 0);
        $deadline = trim($_POST['deadline'] ?? '');
        $requirements = $_POST['requirements'] ?? [];
        
        if (empty($title)) {
            $errors['title'] = 'Title is required';
        }
        
        if (empty($description)) {
            $errors['description'] = 'Description is required';
        }
        
        if (empty($category)) {
            $errors['category'] = 'Category is required';
        }
        
        if ($budget <= 0) {
            $errors['budget'] = 'Budget must be greater than 0';
        }
        
        if (empty($deadline)) {
            $errors['deadline'] = 'Deadline is required';
        }
        
        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
            return;
        }
        
        // Create job
        $jobData = [
            'client_id' => $userId,
            'title' => $title,
            'description' => $description,
            'category' => $category,
            'budget_type' => 'fixed',
            'fixed_budget' => $budget,
            'min_budget' => 0,
            'max_budget' => 0,
            'deadline' => $deadline,
            'requirements' => $requirements,
            'status' => 'active'
        ];
        
        $jobId = $this->jobModel->create($jobData);
        
        if ($jobId) {
            $this->json([
                'success' => true,
                'message' => 'Post created successfully. Waiting for admin approval.',
                'redirect' => BASE_URL . 'client/dashboard'
            ]);
        } else {
            $this->json([
                'success' => false,
                'message' => 'Failed to create post'
            ], 500);
        }
    }

    public function viewPosts() {
        $userId = SessionHelper::getUserId();
        $status = $_GET['status'] ?? 'all';
        
        $jobs = $this->jobModel->findByClient($userId);
        
        if ($status !== 'all') {
            $jobs = array_filter($jobs, function($job) use ($status) {
                return $job['status'] === $status;
            });
        }
        
        $data = [
            'user' => SessionHelper::getUserData(),
            'jobs' => $jobs,
            'current_status' => $status,
            'page_title' => 'My Posts'
        ];
        
        $this->view('client/view_posts', $data);
    }

    public function getPost($id) {
        $job = $this->jobModel->findById($id);
        
        if (!$job || $job['client_id'] != SessionHelper::getUserId()) {
            $this->json(['success' => false, 'message' => 'Post not found'], 404);
            return;
        }
        
        $this->json(['success' => true, 'post' => $job]);
    }

    public function updatePost($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }
        
        $job = $this->jobModel->findById($id);
        
        if (!$job || $job['client_id'] != SessionHelper::getUserId()) {
            $this->json(['success' => false, 'message' => 'Post not found'], 404);
            return;
        }
        
        $errors = [];
        $data = [];
        
        // Validate input (similar to createPost)
        // ... validation code ...
        
        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
            return;
        }
        
        $success = $this->jobModel->update($id, $data);
        
        if ($success) {
            $this->json([
                'success' => true,
                'message' => 'Post updated successfully'
            ]);
        } else {
            $this->json([
                'success' => false,
                'message' => 'Failed to update post'
            ], 500);
        }
    }

    public function deletePost($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }
        
        $job = $this->jobModel->findById($id);
        
        if (!$job || $job['client_id'] != SessionHelper::getUserId()) {
            $this->json(['success' => false, 'message' => 'Post not found'], 404);
            return;
        }
        
        $success = $this->jobModel->delete($id);
        
        if ($success) {
            $this->json(['success' => true, 'message' => 'Post deleted successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to delete post'], 500);
        }
    }

    public function savedPosts() {
        $userId = SessionHelper::getUserId();
        
        $savedPosts = $this->savedPostModel->getUserSavedPosts($userId);
        
        $data = [
            'user' => SessionHelper::getUserData(),
            'saved_posts' => $savedPosts,
            'page_title' => 'Saved Posts'
        ];
        
        $this->view('client/saved_posts', $data);
    }

    public function wall() {
        $jobs = $this->jobModel->getAllActive();
        
        $data = [
            'user' => SessionHelper::getUserData(),
            'jobs' => $jobs,
            'page_title' => 'Job Wall'
        ];
        
        $this->view('client/wall', $data);
    }
}