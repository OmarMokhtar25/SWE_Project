<?php

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'Controller.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'SessionHelper.php';

class AdminController extends Controller {
    
    private $userModel;
    private $adminUserModel;
    private $jobModel;
    private $activityLogModel;

    public function __construct() {
        // Check if user is logged in and is an admin
        if (!SessionHelper::isLoggedIn() || SessionHelper::get('account_type') !== 'admin') {
            $this->redirect('auth/login');
        }
        
        $this->userModel = $this->model('User');
        $this->adminUserModel = $this->model('AdminUser');
        $this->jobModel = $this->model('Job');
        $this->activityLogModel = $this->model('ActivityLog');
    }

    public function index() {
        $this->dashboard();
    }

    public function dashboard() {
        // Get statistics
        $userStats = $this->adminUserModel->getUserStats();
        $jobStats = $this->jobModel->getJobStats();
        
        // Get recent activities
        $recentActivities = $this->activityLogModel->getRecentActivities(10);
        
        // Get pending items
        $pendingJobs = $this->jobModel->getPendingJobs();
        $pendingUsers = $this->adminUserModel->getPendingUsers();
        
        $data = [
            'user' => SessionHelper::getUserData(),
            'user_stats' => $userStats,
            'job_stats' => $jobStats,
            'recent_activities' => $recentActivities,
            'pending_jobs' => $pendingJobs,
            'pending_users' => $pendingUsers,
            'page_title' => 'Admin Dashboard'
        ];
        
        $this->view('admin/dashboard', $data);
    }

    public function pendingJobs() {
        $pendingJobs = $this->jobModel->getPendingJobs();
        
        $data = [
            'user' => SessionHelper::getUserData(),
            'pending_jobs' => $pendingJobs,
            'page_title' => 'Pending Jobs'
        ];
        
        $this->view('admin/pending_jobs', $data);
    }

    public function approveJob() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }
        
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid job ID'], 400);
            return;
        }
        
        $success = $this->jobModel->approveJob($id);
        
        if ($success) {
            // Log activity
            $this->activityLogModel->log(
                SessionHelper::getUserId(),
                'job_approved',
                ['job_id' => $id]
            );
            
            $this->json(['success' => true, 'message' => 'Job approved successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to approve job'], 500);
        }
    }

    public function rejectJob() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }
        
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid job ID'], 400);
            return;
        }
        
        $success = $this->jobModel->rejectJob($id);
        
        if ($success) {
            // Log activity
            $this->activityLogModel->log(
                SessionHelper::getUserId(),
                'job_rejected',
                ['job_id' => $id]
            );
            
            $this->json(['success' => true, 'message' => 'Job rejected successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to reject job'], 500);
        }
    }

    public function pendingUsers() {
        $pendingUsers = $this->adminUserModel->getPendingUsers();
        
        $data = [
            'user' => SessionHelper::getUserData(),
            'pending_users' => $pendingUsers,
            'page_title' => 'Pending Users'
        ];
        
        $this->view('admin/pending_users', $data);
    }

    public function approveUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }
        
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid user ID'], 400);
            return;
        }
        
        $success = $this->adminUserModel->approveUser($id);
        
        if ($success) {
            // Log activity
            $this->activityLogModel->log(
                SessionHelper::getUserId(),
                'user_approved',
                ['user_id' => $id]
            );
            
            $this->json(['success' => true, 'message' => 'User approved successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to approve user'], 500);
        }
    }

    public function rejectUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }
        
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid user ID'], 400);
            return;
        }
        
        $success = $this->adminUserModel->rejectUser($id);
        
        if ($success) {
            // Log activity
            $this->activityLogModel->log(
                SessionHelper::getUserId(),
                'user_rejected',
                ['user_id' => $id]
            );
            
            $this->json(['success' => true, 'message' => 'User rejected successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to reject user'], 500);
        }
    }

    public function users() {
        $filters = [
            'account_type' => $_GET['account_type'] ?? '',
            'status' => $_GET['status'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];
        
        $users = $this->adminUserModel->getAllUsers($filters);
        
        $data = [
            'user' => SessionHelper::getUserData(),
            'users' => $users,
            'filters' => $filters,
            'page_title' => 'Manage Users'
        ];
        
        $this->view('admin/users', $data);
    }

    public function suspendUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }
        
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid user ID'], 400);
            return;
        }
        
        $success = $this->adminUserModel->suspendUser($id);
        
        if ($success) {
            // Log activity
            $this->activityLogModel->log(
                SessionHelper::getUserId(),
                'user_suspended',
                ['user_id' => $id]
            );
            
            $this->json(['success' => true, 'message' => 'User suspended successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to suspend user'], 500);
        }
    }

    public function activateUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }
        
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid user ID'], 400);
            return;
        }
        
        $success = $this->adminUserModel->activateUser($id);
        
        if ($success) {
            // Log activity
            $this->activityLogModel->log(
                SessionHelper::getUserId(),
                'user_activated',
                ['user_id' => $id]
            );
            
            $this->json(['success' => true, 'message' => 'User activated successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to activate user'], 500);
        }
    }

    public function activityLog() {
        $recentActivities = $this->activityLogModel->getRecentActivities(50);
        
        $data = [
            'user' => SessionHelper::getUserData(),
            'activities' => $recentActivities,
            'page_title' => 'Activity Log'
        ];
        
        $this->view('admin/activity_log', $data);
    }

    public function refreshStats() {
        // This would recalculate statistics
        // For now, just return current stats
        
        $userStats = $this->adminUserModel->getUserStats();
        $jobStats = $this->jobModel->getJobStats();
        
        $this->json([
            'success' => true,
            'stats' => array_merge($userStats, $jobStats)
        ]);
    }

    public function getUserDetails($id) {
        $user = $this->userModel->findById($id);
        
        if (!$user) {
            $this->json(['success' => false, 'message' => 'User not found'], 404);
            return;
        }
        
        $this->json(['success' => true, 'details' => $user]);
    }

    public function getJobDetails($id) {
        $job = $this->jobModel->findById($id);
        
        if (!$job) {
            $this->json(['success' => false, 'message' => 'Job not found'], 404);
            return;
        }
        
        $this->json(['success' => true, 'details' => $job]);
    }

    public function bulkAction() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }
        
        $action = $_POST['action'] ?? '';
        $ids = $_POST['ids'] ?? [];
        
        if (empty($action) || empty($ids)) {
            $this->json(['success' => false, 'message' => 'Invalid parameters'], 400);
            return;
        }
        
        $successCount = 0;
        $failCount = 0;
        
        foreach ($ids as $id) {
            $id = intval($id);
            
            if ($id <= 0) continue;
            
            $success = false;
            
            switch ($action) {
                case 'approve':
                    $success = $this->adminUserModel->approveUser($id);
                    break;
                case 'reject':
                    $success = $this->adminUserModel->rejectUser($id);
                    break;
                case 'suspend':
                    $success = $this->adminUserModel->suspendUser($id);
                    break;
                case 'activate':
                    $success = $this->adminUserModel->activateUser($id);
                    break;
            }
            
            if ($success) {
                $successCount++;
            } else {
                $failCount++;
            }
        }
        
        $this->json([
            'success' => true,
            'message' => "Processed {$successCount} items successfully, {$failCount} failed"
        ]);
    }
}