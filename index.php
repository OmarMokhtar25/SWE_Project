<?php
// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base URL
define('BASE_URL', 'http://localhost/SWE_Project/public/');
define('ROOT_PATH', dirname(__DIR__));

// Get the base directory
$baseDir = dirname(__DIR__);

// Load core classes
require_once $baseDir . '/core/Database.php';
require_once $baseDir . '/core/Model.php';
require_once $baseDir . '/core/Controller.php';
require_once $baseDir . '/core/App.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize the application
try {
    $app = new App();
} catch (Exception $e) {
    die("Application Error: " . $e->getMessage());
}