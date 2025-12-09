<?php

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'Controller.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'SessionHelper.php';

class HomeController extends Controller {
    
    private $jobModel;
    private $userModel;

    public function __construct() {
        $this->jobModel = $this->model('Job');
        $this->userModel = $this->model('User');
    }

    public function index() {
        // If user is logged in, redirect to appropriate dashboard
        if (SessionHelper::isLoggedIn()) {
            $accountType = SessionHelper::get('account_type');
            
            if ($accountType === 'client') {
                $this->redirect('client/dashboard');
            } elseif ($accountType === 'freelancer') {
                $this->redirect('freelancer/dashboard');
            } elseif ($accountType === 'admin') {
                $this->redirect('admin/dashboard');
            }
            return;
        }
        
        // Get featured jobs for homepage
        $featuredJobs = $this->jobModel->getRecentJobs(6);
        
        // Get statistics
        $userStats = $this->userModel->getUserStats();
        
        $data = [
            'featured_jobs' => $featuredJobs,
            'stats' => $userStats,
            'page_title' => 'Quicklance - Find Freelancers & Projects'
        ];
        
        $this->view('home/index', $data);
    }

    public function about() {
        $data = [
            'page_title' => 'About Us'
        ];
        
        $this->view('home/about', $data);
    }

    public function contact() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processContact();
            return;
        }
        
        $data = [
            'page_title' => 'Contact Us'
        ];
        
        $this->view('home/contact', $data);
    }

    private function processContact() {
        $errors = [];
        $data = [];
        
        // Validate input
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        
        if (empty($name)) {
            $errors['name'] = 'Name is required';
        }
        
        if (empty($email)) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }
        
        if (empty($subject)) {
            $errors['subject'] = 'Subject is required';
        }
        
        if (empty($message)) {
            $errors['message'] = 'Message is required';
        }
        
        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
            return;
        }
        
        // Process contact form (send email, save to database, etc.)
        // For now, just return success
        
        $this->json([
            'success' => true,
            'message' => 'Thank you for your message. We will get back to you soon.'
        ]);
    }

    public function faq() {
        $data = [
            'page_title' => 'Frequently Asked Questions'
        ];
        
        $this->view('home/faq', $data);
    }

    public function terms() {
        $data = [
            'page_title' => 'Terms of Service'
        ];
        
        $this->view('home/terms', $data);
    }

    public function privacy() {
        $data = [
            'page_title' => 'Privacy Policy'
        ];
        
        $this->view('home/privacy', $data);
    }
}