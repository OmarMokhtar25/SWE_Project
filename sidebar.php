<?php if (SessionHelper::isLoggedIn() && $user['account_type'] === 'admin'): ?>
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-logo">
            <h2><i class="fas fa-shield-alt"></i> Admin Panel</h2>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li>
                    <a href="<?php echo BASE_URL; ?>admin/dashboard" class="<?php echo $page_title === 'Admin Dashboard' ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>admin/pending-jobs" class="<?php echo $page_title === 'Pending Jobs' ? 'active' : ''; ?>">
                        <i class="fas fa-clock"></i> Pending Jobs
                        <?php if (isset($pending_jobs_count) && $pending_jobs_count > 0): ?>
                            <span class="badge bg-warning float-end"><?php echo $pending_jobs_count; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>admin/pending-users" class="<?php echo $page_title === 'Pending Users' ? 'active' : ''; ?>">
                        <i class="fas fa-user-clock"></i> Pending Users
                        <?php if (isset($pending_users_count) && $pending_users_count > 0): ?>
                            <span class="badge bg-warning float-end"><?php echo $pending_users_count; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>admin/users" class="<?php echo $page_title === 'Manage Users' ? 'active' : ''; ?>">
                        <i class="fas fa-users"></i> All Users
                        <span class="badge bg-info float-end"><?php echo $user_stats['total_users'] ?? 0; ?></span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>admin/jobs" class="<?php echo $page_title === 'All Jobs' ? 'active' : ''; ?>">
                        <i class="fas fa-briefcase"></i> All Jobs
                        <span class="badge bg-info float-end"><?php echo $job_stats['total_jobs'] ?? 0; ?></span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>admin/proposals" class="<?php echo $page_title === 'Proposals' ? 'active' : ''; ?>">
                        <i class="fas fa-paper-plane"></i> Proposals
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>admin/activity-log" class="<?php echo $page_title === 'Activity Log' ? 'active' : ''; ?>">
                        <i class="fas fa-history"></i> Activity Log
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>admin/settings" class="<?php echo $page_title === 'Settings' ? 'active' : ''; ?>">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </li>
            </ul>
        </nav>
    </aside>
<?php endif; ?>