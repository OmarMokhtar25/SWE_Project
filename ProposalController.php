<?php

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'Controller.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'SessionHelper.php';

class ProposalController extends Controller {
    
    private $proposalModel;
    private $jobModel;
    private $userModel;

    public function __construct() {
        // Check if user is logged in
        if (!SessionHelper::isLoggedIn()) {
            $this->redirect('auth/login');
        }
        
        $this->proposalModel = $this->model('Proposal');
        $this->jobModel = $this->model('Job');
        $this->userModel = $this->model('User');
    }

    public function index() {
        // Redirect based on user type
        $accountType = SessionHelper::get('account_type');
        
        if ($accountType === 'freelancer') {
            $this->freelancerProposals();
        } elseif ($accountType === 'client') {
            $this->clientProposals();
        } else {
            $this->redirect('dashboard');
        }
    }

    private function freelancerProposals() {
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

    private function clientProposals() {
        $userId = SessionHelper::getUserId();
        
        // Get client's jobs
        $jobs = $this->jobModel->findByClient($userId);
        
        // Get proposals for each job
        $allProposals = [];
        foreach ($jobs as $job) {
            $proposals = $this->proposalModel->findByJob($job['id']);
            foreach ($proposals as $proposal) {
                $proposal['job_title'] = $job['title'];
                $allProposals[] = $proposal;
            }
        }
        
        $data = [
            'user' => SessionHelper::getUserData(),
            'proposals' => $allProposals,
            'page_title' => 'Job Proposals'
        ];
        
        $this->view('client/proposals', $data);
    }

    public function submit() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }
        
        // Only freelancers can submit proposals
        if (SessionHelper::get('account_type') !== 'freelancer') {
            $this->json(['success' => false, 'message' => 'Only freelancers can submit proposals'], 403);
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

    public function accept($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }
        
        // Only job owners (clients) can accept proposals
        $proposal = $this->proposalModel->findById($id);
        if (!$proposal) {
            $this->json(['success' => false, 'message' => 'Proposal not found'], 404);
            return;
        }
        
        $job = $this->jobModel->findById($proposal['job_id']);
        if (!$job || $job['client_id'] != SessionHelper::getUserId()) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
            return;
        }
        
        $success = $this->proposalModel->acceptProposal($id);
        
        if ($success) {
            $this->json([
                'success' => true,
                'message' => 'Proposal accepted successfully'
            ]);
        } else {
            $this->json([
                'success' => false,
                'message' => 'Failed to accept proposal'
            ], 500);
        }
    }

    public function reject($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }
        
        // Only job owners (clients) can reject proposals
        $proposal = $this->proposalModel->findById($id);
        if (!$proposal) {
            $this->json(['success' => false, 'message' => 'Proposal not found'], 404);
            return;
        }
        
        $job = $this->jobModel->findById($proposal['job_id']);
        if (!$job || $job['client_id'] != SessionHelper::getUserId()) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
            return;
        }
        
        $success = $this->proposalModel->rejectProposal($id);
        
        if ($success) {
            $this->json([
                'success' => true,
                'message' => 'Proposal rejected successfully'
            ]);
        } else {
            $this->json([
                'success' => false,
                'message' => 'Failed to reject proposal'
            ], 500);
        }
    }

    public function details($id) {
        $proposal = $this->proposalModel->findById($id);
        
        if (!$proposal) {
            $this->json(['success' => false, 'message' => 'Proposal not found'], 404);
            return;
        }
        
        // Check authorization
        $userId = SessionHelper::getUserId();
        $accountType = SessionHelper::get('account_type');
        
        $authorized = false;
        
        if ($accountType === 'freelancer' && $proposal['freelancer_id'] == $userId) {
            $authorized = true;
        } elseif ($accountType === 'client') {
            $job = $this->jobModel->findById($proposal['job_id']);
            if ($job && $job['client_id'] == $userId) {
                $authorized = true;
            }
        }
        
        if (!$authorized) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
            return;
        }
        
        // Get freelancer info
        $freelancer = $this->userModel->findById($proposal['freelancer_id']);
        $job = $this->jobModel->findById($proposal['job_id']);
        
        $this->json([
            'success' => true,
            'proposal' => $proposal,
            'freelancer' => $freelancer,
            'job' => $job
        ]);
    }
}