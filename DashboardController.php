<?php

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'Controller.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'SessionHelper.php';

class DashboardController extends Controller {

    public function __construct() {
        // Ensure session is started
        if (method_exists('SessionHelper','start')) {
            SessionHelper::start();
        } else if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Show dashboard (single render only)
     */
    public function index() {
        // Redirect to login if not logged in
        if (!SessionHelper::isLoggedIn()) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        // Get user data from session helper
        $userData = SessionHelper::getUserData();

        // Prepare data for view
        $data = [
            'user' => $userData,
            'page_title' => 'Dashboard'
        ];

        // SINGLE call to view — ensure there is only this call for dashboard rendering
        $this->view('dashboard/index', $data);
    }
}
