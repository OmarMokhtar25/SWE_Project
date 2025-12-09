<?php

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'Controller.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'SessionHelper.php';

class WallController extends Controller {
    
    private $jobModel;
    private $commentModel;

    public function __construct() {
        $this->jobModel = $this->model('Job');
        $this->commentModel = $this->model('Comment');
    }

    public function index() {
        $search = $_GET['q'] ?? '';
        $category = $_GET['category'] ?? '';
        $budgetMin = $_GET['budget_min'] ?? '';
        $budgetMax = $_GET['budget_max'] ?? '';
        
        if ($search) {
            $jobs = $this->jobModel->search($search, $category, $budgetMin, $budgetMax);
        } else {
            $jobs = $this->jobModel->getAllActive();
        }
        
        $data = [
            'jobs' => $jobs,
            'search_query' => $search,
            'selected_category' => $category,
            'budget_min' => $budgetMin,
            'budget_max' => $budgetMax,
            'page_title' => 'Job Wall'
        ];
        
        $this->view('wall/index', $data);
    }

    public function post($id) {
        $job = $this->jobModel->findById($id);
        
        if (!$job || $job['admin_status'] !== 'approved') {
            $this->redirect('wall');
            return;
        }
        
        // Get comments
        $comments = $this->commentModel->findByPost($id);
        
        // Get replies for each comment
        foreach ($comments as &$comment) {
            $comment['replies'] = $this->commentModel->getReplies($comment['id']);
        }
        
        $data = [
            'job' => $job,
            'comments' => $comments,
            'page_title' => $job['title']
        ];
        
        $this->view('wall/post_detail', $data);
    }

    public function search() {
        $search = $_GET['q'] ?? '';
        $category = $_GET['category'] ?? '';
        $budgetMin = $_GET['budget_min'] ?? '';
        $budgetMax = $_GET['budget_max'] ?? '';
        
        $jobs = $this->jobModel->search($search, $category, $budgetMin, $budgetMax);
        
        $data = [
            'jobs' => $jobs,
            'search_query' => $search,
            'selected_category' => $category,
            'budget_min' => $budgetMin,
            'budget_max' => $budgetMax,
            'page_title' => 'Search Results'
        ];
        
        $this->view('wall/all_posts', $data);
    }

    public function addComment($postId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }
        
        // Check if user is logged in and is a freelancer
        if (!SessionHelper::isLoggedIn() || SessionHelper::get('account_type') !== 'freelancer') {
            $this->json(['success' => false, 'message' => 'Only freelancers can comment'], 403);
            return;
        }
        
        $userId = SessionHelper::getUserId();
        $content = trim($_POST['content'] ?? '');
        $parentId = $_POST['parent_id'] ?? null;
        
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

    public function getComments($postId) {
        $comments = $this->commentModel->findByPost($postId);
        
        // Get replies for each comment
        foreach ($comments as &$comment) {
            $comment['replies'] = $this->commentModel->getReplies($comment['id']);
        }
        
        $this->json(['success' => true, 'comments' => $comments]);
    }
}