<?php

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'Controller.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'SessionHelper.php';

class PostController extends Controller {
    
    private $jobModel;
    private $userModel;
    private $activityLogModel;

    public function __construct() {
        // Check if user is logged in and is a client
        if (!SessionHelper::isLoggedIn() || SessionHelper::get('account_type') !== 'client') {
            $this->redirect('auth/login');
        }
        
        $this->jobModel = $this->model('Job');
        $this->userModel = $this->model('User');
        $this->activityLogModel = $this->model('ActivityLog');
    }

    public function index() {
        $this->create();
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processCreate();
            return;
        }
        
        $data = [
            'user' => SessionHelper::getUserData(),
            'page_title' => 'Create New Job Post',
            'categories' => $this->getCategories()
        ];
        
        $this->view('client/post', $data);
    }

    private function processCreate() {
        $errors = [];
        $data = [];
        
        $userId = SessionHelper::getUserId();
        
        // Validate input
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $budgetType = trim($_POST['budget_type'] ?? 'fixed');
        $minBudget = floatval($_POST['min_budget'] ?? 0);
        $maxBudget = floatval($_POST['max_budget'] ?? 0);
        $fixedBudget = floatval($_POST['fixed_budget'] ?? 0);
        $deadline = trim($_POST['deadline'] ?? '');
        $requirements = $_POST['requirements'] ?? [];
        $attachments = $_FILES['attachments'] ?? [];
        
        // Basic validation
        if (empty($title)) {
            $errors['title'] = 'Job title is required';
        }
        
        if (empty($description)) {
            $errors['description'] = 'Job description is required';
        }
        
        if (empty($category)) {
            $errors['category'] = 'Category is required';
        }
        
        if ($budgetType === 'fixed') {
            if ($fixedBudget <= 0) {
                $errors['fixed_budget'] = 'Budget must be greater than 0';
            }
        } else {
            if ($minBudget <= 0 || $maxBudget <= 0) {
                $errors['budget_range'] = 'Budget range must be greater than 0';
            }
            if ($minBudget > $maxBudget) {
                $errors['budget_range'] = 'Minimum budget cannot exceed maximum budget';
            }
        }
        
        if (empty($deadline)) {
            $errors['deadline'] = 'Deadline is required';
        } elseif (strtotime($deadline) < strtotime('today')) {
            $errors['deadline'] = 'Deadline cannot be in the past';
        }
        
        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
            return;
        }
        
        // Process uploaded files
        $uploadedFiles = [];
        if (!empty($attachments['name'][0])) {
            $uploadedFiles = $this->uploadAttachments($attachments);
        }
        
        // Create job data
        $jobData = [
            'client_id' => $userId,
            'title' => $title,
            'description' => $description,
            'category' => $category,
            'budget_type' => $budgetType,
            'min_budget' => $minBudget,
            'max_budget' => $maxBudget,
            'fixed_budget' => $fixedBudget,
            'deadline' => $deadline,
            'requirements' => $requirements,
            'attachments' => $uploadedFiles,
            'status' => 'active',
            'admin_status' => 'pending'
        ];
        
        // Create job
        $jobId = $this->jobModel->create($jobData);
        
        if ($jobId) {
            // Log activity
            $this->activityLogModel->log(
                $userId,
                'job_created',
                ['job_id' => $jobId, 'title' => $title]
            );
            
            $this->json([
                'success' => true,
                'message' => 'Job post created successfully! It is now pending admin approval.',
                'redirect' => BASE_URL . 'client/view-posts'
            ]);
        } else {
            $this->json([
                'success' => false,
                'message' => 'Failed to create job post. Please try again.'
            ], 500);
        }
    }

    public function edit($id) {
        $userId = SessionHelper::getUserId();
        $job = $this->jobModel->findById($id);
        
        // Check if job exists and belongs to user
        if (!$job || $job['client_id'] != $userId) {
            $this->redirect('client/view-posts');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processEdit($id);
            return;
        }
        
        $data = [
            'user' => SessionHelper::getUserData(),
            'job' => $job,
            'page_title' => 'Edit Job Post',
            'categories' => $this->getCategories()
        ];
        
        $this->view('client/edit_post', $data);
    }

    private function processEdit($id) {
        $errors = [];
        $data = [];
        
        $userId = SessionHelper::getUserId();
        $job = $this->jobModel->findById($id);
        
        // Verify ownership
        if (!$job || $job['client_id'] != $userId) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
            return;
        }
        
        // Similar validation as create
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $budgetType = trim($_POST['budget_type'] ?? 'fixed');
        $minBudget = floatval($_POST['min_budget'] ?? 0);
        $maxBudget = floatval($_POST['max_budget'] ?? 0);
        $fixedBudget = floatval($_POST['fixed_budget'] ?? 0);
        $deadline = trim($_POST['deadline'] ?? '');
        $requirements = $_POST['requirements'] ?? [];
        
        // Validation...
        
        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
            return;
        }
        
        // Update job data
        $jobData = [
            'title' => $title,
            'description' => $description,
            'category' => $category,
            'budget_type' => $budgetType,
            'min_budget' => $minBudget,
            'max_budget' => $maxBudget,
            'fixed_budget' => $fixedBudget,
            'deadline' => $deadline,
            'requirements' => $requirements,
            'status' => $job['status'] // Keep existing status
        ];
        
        $success = $this->jobModel->update($id, $jobData);
        
        if ($success) {
            // Log activity
            $this->activityLogModel->log(
                $userId,
                'job_updated',
                ['job_id' => $id, 'title' => $title]
            );
            
            $this->json([
                'success' => true,
                'message' => 'Job post updated successfully!',
                'redirect' => BASE_URL . 'client/view-posts'
            ]);
        } else {
            $this->json([
                'success' => false,
                'message' => 'Failed to update job post.'
            ], 500);
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }
        
        $userId = SessionHelper::getUserId();
        $job = $this->jobModel->findById($id);
        
        // Verify ownership
        if (!$job || $job['client_id'] != $userId) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
            return;
        }
        
        $success = $this->jobModel->delete($id);
        
        if ($success) {
            // Log activity
            $this->activityLogModel->log(
                $userId,
                'job_deleted',
                ['job_id' => $id, 'title' => $job['title']]
            );
            
            $this->json(['success' => true, 'message' => 'Job post deleted successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to delete job post'], 500);
        }
    }

    public function show($id) {
        $userId = SessionHelper::getUserId();
        $job = $this->jobModel->findById($id);
        
        // Check if job exists and belongs to user
        if (!$job || $job['client_id'] != $userId) {
            $this->redirect('client/view-posts');
            return;
        }
        
        // Get proposals for this job
        $proposalModel = $this->model('Proposal');
        $proposals = $proposalModel->findByJob($id);
        
        $data = [
            'user' => SessionHelper::getUserData(),
            'job' => $job,
            'proposals' => $proposals,
            'page_title' => $job['title']
        ];

        $this->view('client/view_post', $data);
    }

    public function updateStatus($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }
        
        $userId = SessionHelper::getUserId();
        $job = $this->jobModel->findById($id);
        $status = $_POST['status'] ?? '';
        
        // Verify ownership and valid status
        if (!$job || $job['client_id'] != $userId) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
            return;
        }
        
        $validStatuses = ['active', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            $this->json(['success' => false, 'message' => 'Invalid status'], 400);
            return;
        }
        
        $sql = "UPDATE jobs SET status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $success = $this->jobModel->update($sql, [
            ':status' => $status,
            ':id' => $id
        ]);
        
        if ($success) {
            // Log activity
            $this->activityLogModel->log(
                $userId,
                'job_status_updated',
                ['job_id' => $id, 'status' => $status]
            );
            
            $this->json([
                'success' => true,
                'message' => 'Job status updated successfully'
            ]);
        } else {
            $this->json([
                'success' => false,
                'message' => 'Failed to update job status'
            ], 500);
        }
    }

    private function getCategories() {
        return [
            'web_development' => 'Web Development',
            'mobile_development' => 'Mobile Development',
            'design' => 'Design',
            'writing' => 'Writing',
            'marketing' => 'Marketing',
            'consulting' => 'Consulting',
            'data_science' => 'Data Science',
            'devops' => 'DevOps',
            'qa' => 'Quality Assurance',
            'support' => 'Customer Support'
        ];
    }

    private function uploadAttachments($files) {
        $uploadedFiles = [];
        $uploadDir = dirname(__DIR__) . '/../public/assets/uploads/jobs/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $fileName = time() . '_' . uniqid() . '_' . basename($files['name'][$i]);
                $filePath = $uploadDir . $fileName;
                
                if (move_uploaded_file($files['tmp_name'][$i], $filePath)) {
                    $uploadedFiles[] = [
                        'name' => $files['name'][$i],
                        'path' => $fileName,
                        'size' => $files['size'][$i],
                        'type' => $files['type'][$i]
                    ];
                }
            }
        }
        
        return $uploadedFiles;
    }

    public function search() {
        $userId = SessionHelper::getUserId();
        $query = $_GET['q'] ?? '';
        $category = $_GET['category'] ?? '';
        $status = $_GET['status'] ?? '';
        
        $jobs = $this->jobModel->findByClient($userId);
        
        // Filter jobs
        $filteredJobs = array_filter($jobs, function($job) use ($query, $category, $status) {
            $matches = true;
            
            if ($query) {
                $matches = $matches && (
                    stripos($job['title'], $query) !== false ||
                    stripos($job['description'], $query) !== false
                );
            }
            
            if ($category) {
                $matches = $matches && ($job['category'] === $category);
            }
            
            if ($status && $status !== 'all') {
                $matches = $matches && ($job['status'] === $status);
            }
            
            return $matches;
        });
        
        $data = [
            'user' => SessionHelper::getUserData(),
            'jobs' => $filteredJobs,
            'search_query' => $query,
            'selected_category' => $category,
            'current_status' => $status,
            'page_title' => 'Search Results'
        ];
        
        $this->view('client/view_posts', $data);
    }
}