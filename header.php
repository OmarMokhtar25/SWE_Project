<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Quicklance' : 'Quicklance'; ?></title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/bootstrap/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/header.css">
    <?php if (isset($user['account_type'])): ?>
        <?php if ($user['account_type'] === 'client'): ?>
            <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/client.css">
        <?php elseif ($user['account_type'] === 'freelancer'): ?>
            <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/freelancer.css">
        <?php elseif ($user['account_type'] === 'admin'): ?>
            <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/admin.css">
        <?php endif; ?>
    <?php endif; ?>
    
    <!-- Page-specific CSS -->
    <?php if (isset($page_css)): ?>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/<?php echo $page_css; ?>">
    <?php endif; ?>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_URL; ?>assets/images/favicon.ico">
</head>
<body>
    <?php if (SessionHelper::isLoggedIn()): ?>
        <!-- Header for logged-in users -->
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
            <div class="container">
                <a class="navbar-brand" href="<?php echo BASE_URL . strtolower($user['account_type']) . '/dashboard'; ?>">
                    <i class="fas fa-bolt"></i> Quicklance
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <?php if ($user['account_type'] === 'client'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>client/dashboard">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>client/post">
                                    <i class="fas fa-plus-circle"></i> New Post
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>client/view-posts">
                                    <i class="fas fa-list"></i> My Posts
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>client/wall">
                                    <i class="fas fa-th-large"></i> Job Wall
                                </a>
                            </li>
                        <?php elseif ($user['account_type'] === 'freelancer'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>freelancer/dashboard">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>freelancer/wall">
                                    <i class="fas fa-th-large"></i> Find Jobs
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>freelancer/proposals">
                                    <i class="fas fa-paper-plane"></i> My Proposals
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>freelancer/saved-posts">
                                    <i class="fas fa-bookmark"></i> Saved
                                </a>
                            </li>
                        <?php elseif ($user['account_type'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>admin/dashboard">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>admin/pending-jobs">
                                    <i class="fas fa-clock"></i> Pending Jobs
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>admin/pending-users">
                                    <i class="fas fa-user-clock"></i> Pending Users
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>admin/users">
                                    <i class="fas fa-users"></i> All Users
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>admin/activity-log">
                                    <i class="fas fa-history"></i> Activity Log
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                    
                    <ul class="navbar-nav ms-auto">
                        <!-- Search form -->
                        <li class="nav-item">
                            <form class="d-flex search-form" id="searchForm">
                                <input class="form-control me-2" type="search" placeholder="Search..." aria-label="Search">
                                <button class="btn btn-outline-light" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </li>
                        
                        <!-- Notifications -->
                        <li class="nav-item dropdown">
                            <a class="nav-link position-relative" href="#" id="notificationBtn" role="button">
                                <i class="fas fa-bell"></i>
                                <span class="notification-badge">3</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" id="notificationDropdown">
                                <h6 class="dropdown-header">Notifications</h6>
                                <a class="dropdown-item" href="#">
                                    <div class="notification-item">
                                        <i class="fas fa-check-circle text-success"></i>
                                        <div class="notification-content">
                                            <small>Your proposal was accepted</small>
                                            <div class="notification-time">2 hours ago</div>
                                        </div>
                                    </div>
                                </a>
                                <a class="dropdown-item" href="#">
                                    <div class="notification-item">
                                        <i class="fas fa-comment text-primary"></i>
                                        <div class="notification-content">
                                            <small>New message from client</small>
                                            <div class="notification-time">5 hours ago</div>
                                        </div>
                                    </div>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-center" href="#">
                                    View All Notifications
                                </a>
                            </div>
                        </li>
                        
                        <!-- User dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdownBtn" role="button">
                                <i class="fas fa-user-circle"></i>
                                <?php echo htmlspecialchars($user['first_name']); ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" id="userDropdown">
                                <a class="dropdown-item" href="<?php echo BASE_URL; ?>profile">
                                    <i class="fas fa-user"></i> My Profile
                                </a>
                                <a class="dropdown-item" href="<?php echo BASE_URL; ?>profile/settings">
                                    <i class="fas fa-cog"></i> Settings
                                </a>
                                <?php if ($user['account_type'] === 'freelancer'): ?>
                                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>freelancer/balance">
                                        <i class="fas fa-wallet"></i> Balance: $1,500
                                    </a>
                                <?php endif; ?>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?php echo BASE_URL; ?>auth/logout">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <!-- Sidebar for admin -->
        <?php if ($user['account_type'] === 'admin'): ?>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <aside class="admin-sidebar" id="adminSidebar">
                <div class="admin-logo">
                    <h2><i class="fas fa-shield-alt"></i> Admin Panel</h2>
                </div>
                <nav class="sidebar-nav">
                    <ul>
                        <li>
                            <a href="<?php echo BASE_URL; ?>admin/dashboard" class="active">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>admin/pending-jobs">
                                <i class="fas fa-clock"></i> Pending Jobs
                                <span class="badge bg-warning float-end">5</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>admin/pending-users">
                                <i class="fas fa-user-clock"></i> Pending Users
                                <span class="badge bg-warning float-end">3</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>admin/users">
                                <i class="fas fa-users"></i> All Users
                                <span class="badge bg-info float-end">150</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>admin/jobs">
                                <i class="fas fa-briefcase"></i> All Jobs
                                <span class="badge bg-info float-end">80</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>admin/proposals">
                                <i class="fas fa-paper-plane"></i> Proposals
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>admin/activity-log">
                                <i class="fas fa-history"></i> Activity Log
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>admin/settings">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                        </li>
                    </ul>
                </nav>
            </aside>
        <?php endif; ?>
        
        <!-- Main content -->
        <div class="main-content">
            <?php if ($user['account_type'] === 'admin'): ?>
                <div class="admin-content">
            <?php endif; ?>
    <?php else: ?>
        <!-- Public header (for non-logged in users) -->
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="<?php echo BASE_URL; ?>">
                    <i class="fas fa-bolt"></i> Quicklance
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>wall">Find Work</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>about">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>contact">Contact</a>
                        </li>
                    </ul>
                    
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>auth/login">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary" href="<?php echo BASE_URL; ?>auth/register">
                                Sign Up Free
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    <?php endif; ?>