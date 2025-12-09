<?php require_once dirname(__DIR__) . '/includes/header.php'; ?>

<div class="admin-dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Manage Users</h1>
        <div class="btn-group" role="group">
            <button class="btn btn-outline-primary active" data-filter="all">All</button>
            <button class="btn btn-outline-primary" data-filter="client">Clients</button>
            <button class="btn btn-outline-primary" data-filter="freelancer">Freelancers</button>
            <button class="btn btn-outline-primary" data-filter="admin">Admins</button>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" placeholder="Search users..." 
                           value="<?php echo htmlspecialchars($filters['search']); ?>" id="searchUsers">
                </div>
                <div class="col-md-2">
                    <select class="form-control" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active" <?php echo $filters['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="pending" <?php echo $filters['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="suspended" <?php echo $filters['status'] === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control" id="dateFilter">
                        <option value="">Joined Any Time</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control" id="sortFilter">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="name">By Name</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" id="applyFilters">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Users List (<?php echo count($users); ?>)</h5>
            <div>
                <button class="btn btn-sm btn-outline-primary me-2" id="exportUsers">
                    <i class="fas fa-download"></i> Export
                </button>
                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-user-plus"></i> Add User
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAllUsers">
                            </th>
                            <th>User</th>
                            <th>Contact</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTable">
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr data-type="<?php echo $user['account_type']; ?>" 
                                    data-status="<?php echo $user['status']; ?>">
                                    <td>
                                        <input type="checkbox" class="select-user" value="<?php echo $user['id']; ?>">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <?php if (!empty($user['avatar'])): ?>
                                                    <img src="<?php echo $user['avatar']; ?>" class="rounded-circle" width="40" height="40">
                                                <?php else: ?>
                                                    <div class="avatar-placeholder rounded-circle bg-secondary d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong>
                                                <br>
                                                <small class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div><?php echo htmlspecialchars($user['email']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($user['phone_number'] ?? 'N/A'); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $user['account_type'] === 'admin' ? 'primary' : 
                                                               ($user['account_type'] === 'client' ? 'success' : 'warning'); ?>">
                                            <?php echo ucfirst($user['account_type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $user['status'] === 'active' ? 'success' : 
                                                               ($user['status'] === 'pending' ? 'warning' : 'danger'); ?>">
                                            <?php echo ucfirst($user['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                                        <br>
                                        <small class="text-muted"><?php echo date('h:i A', strtotime($user['created_at'])); ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-outline-primary view-user" data-id="<?php echo $user['id']; ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-warning edit-user" data-id="<?php echo $user['id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if ($user['status'] === 'active'): ?>
                                                <button class="btn btn-outline-danger suspend-user" data-id="<?php echo $user['id']; ?>">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-outline-success activate-user" data-id="<?php echo $user['id']; ?>">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h4>No users found</h4>
                                    <p class="text-muted">Try adjusting your search filters</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Bulk Actions -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <select class="form-control" id="bulkUserAction">
                        <option value="">Bulk Action</option>
                        <option value="activate">Activate Selected</option>
                        <option value="suspend">Suspend Selected</option>
                        <option value="delete">Delete Selected</option>
                        <option value="export">Export Selected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" id="applyBulkAction">Apply</button>
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted">
                        Showing <?php echo count($users); ?> users • 
                        <?php echo isset($user_stats['active_users']) ? $user_stats['active_users'] . ' active' : ''; ?> • 
                        <?php echo isset($user_stats['pending_users']) ? $user_stats['pending_users'] . ' pending' : ''; ?>
                    </small>
                </div>
            </div>

            <!-- Pagination -->
            <?php if (!empty($users)): ?>
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
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name *</label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name *</label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username *</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password *</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Account Type *</label>
                        <select class="form-control" name="account_type" required>
                            <option value="client">Client</option>
                            <option value="freelancer">Freelancer</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-control" name="status">
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                    <div class="message" id="addUserMessage"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addUserForm" class="btn btn-success">Add User</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addUserModal = new bootstrap.Modal(document.getElementById('addUserModal'));
    let currentUserId = null;

    // Select all users
    document.getElementById('selectAllUsers').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.select-user');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Filter buttons
    document.querySelectorAll('[data-filter]').forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active button
            document.querySelectorAll('[data-filter]').forEach(btn => {
                btn.classList.remove('active');
            });
            this.classList.add('active');
            
            // Filter users
            filterUsers();
        });
    });

    // Apply filters button
    document.getElementById('applyFilters').addEventListener('click', function() {
        applyFilters();
    });

    // View user
    document.querySelectorAll('.view-user').forEach(button => {
        button.addEventListener('click', function() {
            currentUserId = this.dataset.id;
            viewUser(currentUserId);
        });
    });

    // Edit user
    document.querySelectorAll('.edit-user').forEach(button => {
        button.addEventListener('click', function() {
            currentUserId = this.dataset.id;
            editUser(currentUserId);
        });
    });

    // Suspend user
    document.querySelectorAll('.suspend-user').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.id;
            if (confirm('Are you sure you want to suspend this user?')) {
                suspendUser(userId);
            }
        });
    });

    // Activate user
    document.querySelectorAll('.activate-user').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.id;
            if (confirm('Are you sure you want to activate this user?')) {
                activateUser(userId);
            }
        });
    });

    // Bulk actions
    document.getElementById('applyBulkAction').addEventListener('click', function() {
        const action = document.getElementById('bulkUserAction').value;
        const selectedUsers = getSelectedUsers();

        if (!action) {
            alert('Please select a bulk action');
            return;
        }

        if (selectedUsers.length === 0) {
            alert('Please select at least one user');
            return;
        }

        if (confirm(`Are you sure you want to ${action} ${selectedUsers.length} user(s)?`)) {
            performBulkAction(action, selectedUsers);
        }
    });

    // Add user form
    const addUserForm = document.getElementById('addUserForm');
    if (addUserForm) {
        addUserForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch(BASE_URL + 'admin/add-user', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    location.reload();
                } else {
                    showMessage('addUserMessage', result.message, 'error');
                }
            } catch (error) {
                showMessage('addUserMessage', 'Failed to add user', 'error');
            }
        });
    }

    function filterUsers() {
        const rows = document.querySelectorAll('#usersTable tr');
        const filterValue = document.querySelector('[data-filter].active')?.dataset.filter;
        
        rows.forEach(row => {
            if (filterValue === 'all' || row.dataset.type === filterValue) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function applyFilters() {
        const search = document.getElementById('searchUsers').value;
        const status = document.getElementById('statusFilter').value;
        const date = document.getElementById('dateFilter').value;
        const sort = document.getElementById('sortFilter').value;
        
        let url = BASE_URL + 'admin/users?';
        if (search) url += 'search=' + encodeURIComponent(search) + '&';
        if (status) url += 'status=' + status + '&';
        if (date) url += 'date=' + date + '&';
        if (sort) url += 'sort=' + sort;
        
        window.location.href = url;
    }

    function viewUser(userId) {
        window.location.href = BASE_URL + 'admin/user-details/' + userId;
    }

    function editUser(userId) {
        window.location.href = BASE_URL + 'admin/edit-user/' + userId;
    }

    function suspendUser(userId) {
        fetch(BASE_URL + 'admin/suspend-user', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: userId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }

    function activateUser(userId) {
        fetch(BASE_URL + 'admin/activate-user', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: userId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }

    function getSelectedUsers() {
        const checkboxes = document.querySelectorAll('.select-user:checked');
        return Array.from(checkboxes).map(checkbox => checkbox.value);
    }

    function performBulkAction(action, userIds) {
        fetch(BASE_URL + 'admin/bulk-user-action', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: action,
                ids: userIds
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }

    function showMessage(elementId, message, type) {
        const element = document.getElementById(elementId);
        if (element) {
            element.textContent = message;
            element.className = `message ${type}`;
            element.style.display = 'block';
        }
    }
});
</script>

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
}
</style>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>