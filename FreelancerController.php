<?php

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'Controller.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'SessionHelper.php';

class FreelancerController extends Controller {
    
    private $userModel;
    private $jobModel;
    private $proposalModel;
    private $savedPostModel;
    private $commentModel;

    public function __construct() {
        // Check if user is logged in and is a freelancer
        if (!SessionHelper::isLoggedIn() || SessionHelper::get('account_type') !== 'freelancer') {
            $this->redirect('auth/login');
        }
        
        $this->userModel = $this->model('User');
        $this->jobModel = $this->model('Job');
        $this->proposalModel = $this->model('Proposal');
        $this->savedPostModel = $this->model('SavedPost');
        $this->commentModel = $this->model('Comment');
    }

    public function index() {
        $this->dashboard();
    }

    public function dashboard() {
        $userId = SessionHelper::getUserId();
        
        // Get freelancer stats
        $proposalStats = $this->proposalModel->getStats($userId);
        $balance = $this->getBalance($userId);
        
        // Get recent proposals
        $recentProposals = $this->proposalModel->findByFreelancer($userId, null);
        $recentProposals = array_slice($recentProposals, 0, 5);
        
        // Get recommended jobs
        $recommendedJobs = $this->jobModel->getRecentJobs(5);
        
        $data = [
            'user' => SessionHelper::getUserData(),
            'stats' => $proposalStats,
            'balance' => $balance,
            'recent_proposals' => $recentProposals,
            'recommended_jobs' => $recommendedJobs,
            'page_title' => 'Freelancer Dashboard'
        ];
        
        $this->view('freelancer/dashboard', $data);
    }

    private function getBalance($userId) {
        // This would typically come from a transactions table
        // For now, return a fixed value
        return 1500.00;
    }

    public function profile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->updateProfile();
            return;
        }
        
        $userId = SessionHelper::getUserId();
        $user = $this->userModel->findById($userId);
        
        // Get user skills (this would typically come from a separate table)
        $skills = ['PHP', 'JavaScript', 'HTML/CSS', 'Laravel', 'React'];
        
        $data = [
            'user' => $user,
            'skills' => $skills,
            'page_title' => 'My Profile'
        ];
        
        $this->view('freelancer/profile', $data);
    }

    private function updateProfile() {
        $userId = SessionHelper::getUserId();
        $errors = [];
        
        // Similar validation as client profile update
        // ... validation code ...
        
        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
            return;
        }
        
        // Update user profile
        // ... update code ...
        
        $this->json([
            'success' => true,
            'message' => 'Profile updated successfully'
        ]);
    }

    public function proposals() {
        $userId = SessionHelper::getUserId();
        $status = $_GET['status'] ?? 'all';
        
        $proposals = $this->proposalModel->findByFreelancer($userId, $status === 'all' ? null : $status);
        
        $data = [
            'user' => SessionHelper::getUserData(),
            'proposals' => $proposals,
            'current_status' => $status,
            'page_title' => 'My Proposals'
        ];
        
        $this->view('freelancer/proposals', $data);
    }

    public function submitProposal() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }
        
        $userId = SessionHelper::getUserId();
        $errors = [];
        $data = [];
        
        // Validate input
        $jobId = intval($_POST['job_id'] ?? 0);
        $coverLetter = trim($_POST['cover_letter'] ?? '');
        $bidAmount = floatval($_POST['bid_amount'] ?? 0);
        $deliveryTime = intval($_POST['delivery_time'] ?? 0);
        
        if ($jobId <= 0) {
            $errors['job_id'] = 'Invalid job';
        }
        
        if (empty($coverLetter)) {
            $errors['cover_letter'] = 'Cover letter is required';
        }
        
        if ($bidAmount <= 0) {
            $errors['bid_amount'] = 'Bid amount must be greater than 0';
        }
        
        if ($deliveryTime <= 0) {
            $errors['delivery_time'] = 'Delivery time must be greater than 0';
        }
        
        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
            return;
        }
        
        // Check if job exists and is active
        $job = $this->jobModel->findById($jobId);
        if (!$job || $job['admin_status'] !== 'approved' || $job['status'] !== 'active') {
            $this->json(['success' => false, 'message' => 'Job not available'], 404);
            return;
        }
        
        // Check if already applied
        $existingProposals = $this->proposalModel->findByFreelancer($userId);
        foreach ($existingProposals as $proposal) {
            if ($proposal['job_id'] == $jobId) {
                $this->json(['success' => false, 'message' => 'You have already applied for this job']);
                return;
            }
        }
        
        // Create proposal
        $proposalData = [
            'freelancer_id' => $userId,
            'job_id' => $jobId,
            'cover_letter' => $coverLetter,
            'bid_amount' => $bidAmount,
            'delivery_time' => $deliveryTime,
            'attachments' => [],
            'status' => 'pending'
        ];
        
        $proposalId = $this->proposalModel->create($proposalData);
        
        if ($proposalId) {
            $this->json([
                'success' => true,
                'message' => 'Proposal submitted successfully'
            ]);
        } else {
            $this->json([
                'success' => false,
                'message' => 'Failed to submit proposal'
            ], 500);
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
        
        $this->view('freelancer/saved_posts', $data);
    }

    public function savePost() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }
        
        $userId = SessionHelper::getUserId();
        $postId = intval($_POST['post_id'] ?? 0);
        $action = $_POST['action'] ?? 'save';
        
        if ($postId <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid post'], 400);
            return;
        }
        
        if ($action === 'save') {
            $success = $this->savedPostModel->save($userId, $postId);
            $message = 'Post saved successfully';
        } else {
            $success = $this->savedPostModel->unsave($userId, $postId);
            $message = 'Post removed from saved';
        }
        
        if ($success) {
            $this->json(['success' => true, 'message' => $message]);
        } else {
            $this->json(['success' => false, 'message' => 'Operation failed'], 500);
        }
    }

    public function wall() {
        $search = $_GET['q'] ?? '';
        $category = $_GET['category'] ?? '';
        $budgetMin = $_GET['budget_min'] ?? '';
        $budgetMax = $_GET['budget_max'] ?? '';
        
        if ($search) {
            $jobs = $this->jobModel->search($search, $category, $budgetMin, $budgetMax);
        } else {
            $jobs = $this->jobModel->getAllActive();
        }
        
        // Check which jobs are saved
        $userId = SessionHelper::getUserId();
        foreach ($jobs as &$job) {
            $job['is_saved'] = $this->savedPostModel->isSaved($userId, $job['id']);
        }
        
        $data = [
            'user' => SessionHelper::getUserData(),
            'jobs' => $jobs,
            'search_query' => $search,
            'selected_category' => $category,
            'budget_min' => $budgetMin,
            'budget_max' => $budgetMax,
            'page_title' => 'Job Wall'
        ];
        
        $this->view('freelancer/wall', $data);
    }

    public function addComment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }
        
        $userId = SessionHelper::getUserId();
        $postId = intval($_POST['post_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');
        $parentId = $_POST['parent_id'] ?? null;
        
        if ($postId <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid post'], 400);
            return;
        }
        
        if (empty($content)) {
            $this->json(['success' => false, 'message' => 'Comment cannot be empty'], 400);
            return;
        }
        
        $commentData = [
            'post_id' => $postId,
            'freelancer_id' => $userId,
            'content' => $content,
            'parent_id' => $parentId
        ];
        
        $commentId = $this->commentModel->create($commentData);
        
        if ($commentId) {
            $this->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'comment_id' => $commentId
            ]);
        } else {
            $this->json([
                'success' => false,
                'message' => 'Failed to add comment'
            ], 500);
        }
    }

    public function withdraw() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }
        
        $userId = SessionHelper::getUserId();
        $amount = floatval($_POST['amount'] ?? 0);
        
        if ($amount <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid amount'], 400);
            return;
        }
        
        // Check if user has sufficient balance
        $balance = $this->getBalance($userId);
        if ($amount > $balance) {
            $this->json(['success' => false, 'message' => 'Insufficient balance'], 400);
            return;
        }
        
        // Process withdrawal (this would typically involve creating a transaction record)
        // For now, just return success
        
        $this->json([
            'success' => true,
            'message' => 'Withdrawal request submitted successfully'
        ]);
    }
}