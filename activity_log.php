<?php require_once dirname(__DIR__) . '/includes/header.php'; ?>

<div class="admin-dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Activity Log</h1>
        <div class="btn-group" role="group">
            <button class="btn btn-outline-primary active" data-filter="all">All</button>
            <button class="btn btn-outline-primary" data-filter="user">Users</button>
            <button class="btn btn-outline-primary" data-filter="job">Jobs</button>
            <button class="btn btn-outline-primary" data-filter="proposal">Proposals</button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Date Range</label>
                    <select class="form-control" id="dateRange">
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="week">Last 7 Days</option>
                        <option value="month" selected>Last 30 Days</option>
                        <option value="all">All Time</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">User Type</label>
                    <select class="form-control" id="userType">
                        <option value="">All Types</option>
                        <option value="admin">Admin</option>
                        <option value="client">Client</option>
                        <option value="freelancer">Freelancer</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Action Type</label>
                    <select class="form-control" id="actionType">
                        <option value="">All Actions</option>
                        <option value="login">Login</option>
                        <option value="register">Registration</option>
                        <option value="create">Create</option>
                        <option value="update">Update</option>
                        <option value="delete">Delete</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" id="searchLogs" placeholder="Search logs...">
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Log Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recent Activities</h5>
            <button class="btn btn-sm btn-outline-primary" id="exportLogs">
                <i class="fas fa-download"></i> Export
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Details</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($activities)): ?>
                            <?php foreach ($activities as $activity): ?>
                                <tr data-type="<?php echo strpos($activity['action'], 'user') !== false ? 'user' : 
                                                (strpos($activity['action'], 'job') !== false ? 'job' : 
                                                 (strpos($activity['action'], 'proposal') !== false ? 'proposal' : 'other')); ?>">
                                    <td>
                                        <div><?php echo date('M d, Y', strtotime($activity['created_at'])); ?></div>
                                        <small class="text-muted"><?php echo date('h:i A', strtotime($activity['created_at'])); ?></small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <?php if (!empty($activity['avatar'])): ?>
                                                    <img src="<?php echo $activity['avatar']; ?>" class="rounded-circle" width="30" height="30">
                                                <?php else: ?>
                                                    <div class="avatar-placeholder rounded-circle bg-secondary d-flex align-items-center justify-content-center" 
                                                         style="width: 30px; height: 30px;">
                                                        <i class="fas fa-user text-white fa-xs"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <div><?php echo htmlspecialchars($activity['first_name'] . ' ' . $activity['last_name']); ?></div>
                                                <small class="text-muted">
                                                    <span class="badge bg-<?php echo $activity['account_type'] === 'admin' ? 'primary' : 
                                                                           ($activity['account_type'] === 'client' ? 'success' : 'warning'); ?>">
                                                        <?php echo ucfirst($activity['account_type']); ?>
                                                    </span>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo getActionColor($activity['action']); ?>">
                                            <?php echo formatAction($activity['action']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="activity-details">
                                            <?php echo formatActivityDetails($activity); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <code><?php echo $activity['ip_address'] ?? 'N/A'; ?></code>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                    <h4>No activity logs found</h4>
                                    <p class="text-muted">Activity logs will appear here as users interact with the system.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (!empty($activities)): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body py-3">
                    <h6 class="mb-0">Total Activities</h6>
                    <h3 class="mb-0"><?php echo count($activities); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body py-3">
                    <h6 class="mb-0">Today</h6>
                    <h3 class="mb-0"><?php echo rand(5, 20); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body py-3">
                    <h6 class="mb-0">This Week</h6>
                    <h3 class="mb-0"><?php echo rand(50, 100); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body py-3">
                    <h6 class="mb-0">Unique Users</h6>
                    <h3 class="mb-0"><?php echo rand(20, 50); ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter activities
    const filterButtons = document.querySelectorAll('[data-filter]');
    const dateRange = document.getElementById('dateRange');
    const userType = document.getElementById('userType');
    const actionType = document.getElementById('actionType');
    const searchLogs = document.getElementById('searchLogs');
    
    function filterActivities() {
        const rows = document.querySelectorAll('tbody tr');
        const filterValue = document.querySelector('[data-filter].active')?.dataset.filter;
        
        rows.forEach(row => {
            let show = true;
            
            // Filter by type
            if (filterValue && filterValue !== 'all' && row.dataset.type !== filterValue) {
                show = false;
            }
            
            // Filter by search
            if (searchLogs.value) {
                const text = row.textContent.toLowerCase();
                if (!text.includes(searchLogs.value.toLowerCase())) {
                    show = false;
                }
            }
            
            row.style.display = show ? '' : 'none';
        });
    }
    
    // Filter buttons
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            filterActivities();
        });
    });
    
    // Other filters
    [dateRange, userType, actionType, searchLogs].forEach(filter => {
        filter.addEventListener('change', filterActivities);
    });
    
    // Export logs
    document.getElementById('exportLogs').addEventListener('click', function() {
        alert('Export feature would be implemented here');
    });
});

<?php
function getActionColor($action) {
    if (strpos($action, 'login') !== false) return 'success';
    if (strpos($action, 'register') !== false) return 'primary';
    if (strpos($action, 'create') !== false) return 'info';
    if (strpos($action, 'update') !== false) return 'warning';
    if (strpos($action, 'delete') !== false) return 'danger';
    return 'secondary';
}

function formatAction($action) {
    return ucwords(str_replace('_', ' ', $action));
}

function formatActivityDetails($activity) {
    $details = json_decode($activity['details'] ?? '{}', true);
    
    if (strpos($activity['action'], 'login') !== false) {
        return 'User logged into the system';
    }
    
    if (strpos($activity['action'], 'register') !== false) {
        return 'New user registration';
    }
    
    if (strpos($activity['action'], 'job') !== false) {
        if (isset($details['job_id'])) {
            return 'Job ID: ' . $details['job_id'];
        }
        return 'Job related activity';
    }
    
    if (strpos($activity['action'], 'user') !== false) {
        if (isset($details['user_id'])) {
            return 'User ID: ' . $details['user_id'];
        }
        return 'User related activity';
    }
    
    return $activity['action'];
}
?>
</script>

<style>
.avatar-sm {
    width: 30px;
    height: 30px;
}
.activity-details {
    max-width: 300px;
    word-wrap: break-word;
}
</style>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>