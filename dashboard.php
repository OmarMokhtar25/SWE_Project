<?php require_once dirname(__DIR__) . '/includes/header.php'; ?>

<div class="freelancer-dashboard">
    <!-- Welcome Section -->
    <div class="profile-header mb-5">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-6 mb-3">Welcome, <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>! 🚀</h1>
                <p class="lead mb-0">Find your next freelance opportunity</p>
                <small class="text-light">Last login: <?php echo date('F j, Y \a\t g:i A'); ?></small>
            </div>
            <div class="col-md-4 text-end">
                <div class="balance-card">
                    <h6 class="text-muted mb-2">Available Balance</h6>
                    <h2 class="balance-amount mb-3">$<?php echo number_format($balance, 2); ?></h2>
                    <button class="btn btn-light w-100" id="withdrawBtn">
                        <i class="fas fa-wallet"></i> Withdraw Funds
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-5">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon text-primary">
                    <i class="fas fa-paper-plane fa-2x"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number"><?php echo $stats['total_proposals'] ?? 0; ?></h3>
                    <p class="stat-label">Total Proposals</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon text-success">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number"><?php echo $stats['accepted_proposals'] ?? 0; ?></h3>
                    <p class="stat-label">Accepted</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon text-warning">
                    <i class="fas fa-clock fa-2x"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number"><?php echo $stats['pending_proposals'] ?? 0; ?></h3>
                    <p class="stat-label">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon text-info">
                    <i class="fas fa-briefcase fa-2x"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number"><?php echo $stats['completed_proposals'] ?? 0; ?></h3>
                    <p class="stat-label">Completed</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="<?php echo BASE_URL; ?>freelancer/wall" class="btn btn-primary w-100 h-100 py-4">
                                <i class="fas fa-search fa-2x mb-2"></i>
                                <h6>Find Jobs</h6>
                                <small class="text-light">Browse opportunities</small>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?php echo BASE_URL; ?>freelancer/proposals" class="btn btn-success w-100 h-100 py-4">
                                <i class="fas fa-paper-plane fa-2x mb-2"></i>
                                <h6>My Proposals</h6>
                                <small class="text-light">View applications</small>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?php echo BASE_URL; ?>freelancer/saved-posts" class="btn btn-info w-100 h-100 py-4">
                                <i class="fas fa-bookmark fa-2x mb-2"></i>
                                <h6>Saved Jobs</h6>
                                <small class="text-light">Bookmarked posts</small>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?php echo BASE_URL; ?>freelancer/profile" class="btn btn-warning w-100 h-100 py-4">
                                <i class="fas fa-user-edit fa-2x mb-2"></i>
                                <h6>Update Profile</h6>
                                <small class="text-light">Improve visibility</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <!-- Recommended Jobs -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recommended Jobs</h5>
                    <a href="<?php echo BASE_URL; ?>freelancer/wall" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($recommended_jobs)): ?>
                        <div class="list-group">
                            <?php foreach ($recommended_jobs as $job): ?>
                                <a href="<?php echo BASE_URL; ?>wall/post/<?php echo $job['id']; ?>" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($job['title']); ?></h6>
                                        <span class="badge bg-success">$<?php echo number_format($job['fixed_budget'] ?? $job['max_budget'], 2); ?></span>
                                    </div>
                                    <p class="mb-1 text-muted small">
                                        <?php echo substr(htmlspecialchars($job['description']), 0, 80); ?>...
                                    </p>
                                    <small class="text-muted">
                                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($job['first_name'] . ' ' . $job['last_name']); ?> • 
                                        <i class="fas fa-clock"></i> <?php echo date('M d', strtotime($job['created_at'])); ?>
                                    </small>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No recommended jobs</p>
                            <a href="<?php echo BASE_URL; ?>freelancer/wall" class="btn btn-primary">Browse All Jobs</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Proposals -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Proposals</h5>
                    <a href="<?php echo BASE_URL; ?>freelancer/proposals" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_proposals)): ?>
                        <div class="list-group">
                            <?php foreach ($recent_proposals as $proposal): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($proposal['job_title']); ?></h6>
                                        <span class="badge bg-<?php echo $proposal['status'] === 'accepted' ? 'success' : 
                                                               ($proposal['status'] === 'pending' ? 'warning' : 'danger'); ?>">
                                            <?php echo ucfirst($proposal['status']); ?>
                                        </span>
                                    </div>
                                    <p class="mb-1 small">
                                        <strong>Client:</strong> <?php echo htmlspecialchars($proposal['client_first_name'] . ' ' . $proposal['client_last_name']); ?>
                                    </p>
                                    <p class="mb-1 small">
                                        <strong>Your Bid:</strong> $<?php echo number_format($proposal['bid_amount'], 2); ?> • 
                                        <strong>Delivery:</strong> <?php echo $proposal['delivery_time']; ?> days
                                    </p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i> <?php echo date('M d, Y', strtotime($proposal['created_at'])); ?>
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-paper-plane fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No proposals yet</p>
                            <a href="<?php echo BASE_URL; ?>freelancer/wall" class="btn btn-primary">Find Jobs to Apply</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Stats -->
    <div class="row mt-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Profile Completeness</h5>
                </div>
                <div class="card-body">
                    <div class="progress mb-3" style="height: 25px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 75%;" 
                             aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                            75% Complete
                        </div>
                    </div>
                    <div class="completion-checklist">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" checked disabled>
                            <label class="form-check-label">
                                <span class="text-success"><i class="fas fa-check-circle"></i></span>
                                Basic Information
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" checked disabled>
                            <label class="form-check-label">
                                <span class="text-success"><i class="fas fa-check-circle"></i></span>
                                Profile Picture
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" disabled>
                            <label class="form-check-label">
                                <span class="text-warning"><i class="fas fa-exclamation-circle"></i></span>
                                Portfolio Items (2/5)
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" disabled>
                            <label class="form-check-label">
                                <span class="text-warning"><i class="fas fa-exclamation-circle"></i></span>
                                Skills (Add more)
                            </label>
                        </div>
                        <a href="<?php echo BASE_URL; ?>freelancer/profile" class="btn btn-outline-primary btn-sm mt-2">
                            <i class="fas fa-edit"></i> Complete Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tips for Success</h5>
                </div>
                <div class="card-body">
                    <div class="tips-list">
                        <div class="tip-item mb-3">
                            <i class="fas fa-bullseye text-primary"></i>
                            <div class="tip-content">
                                <h6 class="mb-1">Customize Your Proposals</h6>
                                <p class="text-muted small">Tailor each proposal to the specific job requirements.</p>
                            </div>
                        </div>
                        <div class="tip-item mb-3">
                            <i class="fas fa-clock text-success"></i>
                            <div class="tip-content">
                                <h6 class="mb-1">Respond Quickly</h6>
                                <p class="text-muted small">Fast responses increase your chances of getting hired.</p>
                            </div>
                        </div>
                        <div class="tip-item">
                            <i class="fas fa-star text-warning"></i>
                            <div class="tip-content">
                                <h6 class="mb-1">Build Your Portfolio</h6>
                                <p class="text-muted small">Showcase your best work to attract better clients.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-header {
    background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%);
    color: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 176, 155, 0.3);
}
.balance-card {
    background: rgba(255, 255, 255, 0.9);
    padding: 20px;
    border-radius: 10px;
    color: #333;
}
.balance-amount {
    color: #00b09b;
    font-weight: bold;
}
.stat-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    gap: 15px;
}
.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.stat-content {
    flex: 1;
}
.stat-number {
    font-size: 2em;
    margin: 0;
    line-height: 1;
}
.stat-label {
    color: #666;
    margin: 0;
}
.tip-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
}
.tip-item i {
    margin-top: 5px;
    font-size: 1.2em;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Withdraw button
    const withdrawBtn = document.getElementById('withdrawBtn');
    if (withdrawBtn) {
        withdrawBtn.addEventListener('click', function() {
            const amount = prompt('Enter withdrawal amount:');
            if (amount && !isNaN(amount) && amount > 0) {
                // Implement withdrawal logic
                alert('Withdrawal request submitted for $' + amount);
            }
        });
    }
});
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>