<?php require_once dirname(__DIR__) . '/includes/header.php'; ?>

<div class="admin-dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Pending User Approvals</h1>
        <div class="input-group" style="width: 300px;">
            <input type="text" class="form-control" placeholder="Search users..." id="searchUsers">
            <button class="btn btn-outline-secondary" type="button">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>

    <?php if (!empty($pending_users)): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAllUsers">
                        </th>
                        <th>User</th>
                        <th>Contact</th>
                        <th>Account Type</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTable">
                    <?php foreach ($pending_users as $user): ?>
                        <tr>
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
                                <span class="badge bg-<?php echo $user['account_type'] === 'client' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($user['account_type']); ?>
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
                                    <button class="btn btn-outline-success approve-user" data-id="<?php echo $user['id']; ?>">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-outline-danger reject-user" data-id="<?php echo $user['id']; ?>">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Bulk Actions -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <select class="form-control" id="bulkUserAction">
                            <option value="">Bulk Action</option>
                            <option value="approve">Approve Selected</option>
                            <option value="reject">Reject Selected</option>
                            <option value="delete">Delete Selected</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" id="applyBulkUserAction">Apply</button>
                    </div>
                    <div class="col-md-6 text-end">
                        <small class="text-muted">
                            <?php echo count($pending_users); ?> users pending approval
                        </small>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-user-check fa-4x text-success mb-4"></i>
            <h4>No pending users</h4>
            <p class="text-muted">All users have been reviewed and approved.</p>
            <a href="<?php echo BASE_URL; ?>admin/dashboard" class="btn btn-primary">
                <i class="fas fa-tachometer-alt"></i> Back to Dashboard
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- User Details Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userDetails">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger reject-user-modal">Reject</button>
                <button type="button" class="btn btn-success approve-user-modal">Approve</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userModal = new bootstrap.Modal(document.getElementById('userModal'));
    let currentUserId = null;

    // Select all users
    document.getElementById('selectAllUsers').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.select-user');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Search users
    document.getElementById('searchUsers').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#usersTable tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    // View user details
    document.querySelectorAll('.view-user').forEach(button => {
        button.addEventListener('click', function() {
            currentUserId = this.dataset.id;
            loadUserDetails(currentUserId);
        });
    });

    // Approve user
    document.querySelectorAll('.approve-user').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.id;
            approveUser(userId);
        });
    });

    // Reject user
    document.querySelectorAll('.reject-user').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.id;
            rejectUser(userId);
        });
    });

    // Modal approve/reject buttons
    document.querySelector('.approve-user-modal').addEventListener('click', function() {
        if (currentUserId) {
            approveUser(currentUserId);
            userModal.hide();
        }
    });

    document.querySelector('.reject-user-modal').addEventListener('click', function() {
        if (currentUserId) {
            rejectUser(currentUserId);
            userModal.hide();
        }
    });

    // Bulk actions
    document.getElementById('applyBulkUserAction').addEventListener('click', function() {
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
            performBulkUserAction(action, selectedUsers);
        }
    });

    function loadUserDetails(userId) {
        fetch(BASE_URL + 'admin/get-user-details/' + userId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const user = data.details;
                    
                    const html = `
                        <div class="user-details">
                            <div class="row">
                                <div class="col-md-3 text-center">
                                    ${user.avatar ? 
                                        `<img src="${user.avatar}" class="rounded-circle mb-3" width="120" height="120">` :
                                        `<div class="avatar-placeholder rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto mb-3" 
                                              style="width: 120px; height: 120px;">
                                            <i class="fas fa-user fa-3x text-white"></i>
                                         </div>`
                                    }
                                    <h5>${user.first_name} ${user.last_name}</h5>
                                    <p class="text-muted">@${user.username}</p>
                                </div>
                                <div class="col-md-9">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="30%">Email</th>
                                            <td>${user.email}</td>
                                        </tr>
                                        <tr>
                                            <th>Phone</th>
                                            <td>${user.phone_number || 'N/A'}</td>
                                        </tr>
                                        <tr>
                                            <th>Account Type</th>
                                            <td>
                                                <span class="badge bg-${user.account_type === 'client' ? 'success' : 'warning'}">
                                                    ${user.account_type}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <span class="badge bg-${user.status === 'active' ? 'success' : 
                                                                       user.status === 'pending' ? 'warning' : 'danger'}">
                                                    ${user.status}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Registered</th>
                                            <td>${new Date(user.created_at).toLocaleString()}</td>
                                        </tr>
                                        <tr>
                                            <th>Last Login</th>
                                            <td>${user.last_login ? new Date(user.last_login).toLocaleString() : 'Never'}</td>
                                        </tr>
                                    </table>
                                    
                                    ${user.bio ? `
                                        <h6 class="mt-3">Bio</h6>
                                        <p>${user.bio}</p>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('userDetails').innerHTML = html;
                    userModal.show();
                }
            });
    }

    function approveUser(userId) {
        if (confirm('Approve this user?')) {
            fetch(BASE_URL + 'admin/approve-user', {
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
    }

    function rejectUser(userId) {
        if (confirm('Reject this user?')) {
            fetch(BASE_URL + 'admin/reject-user', {
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
    }

    function getSelectedUsers() {
        const checkboxes = document.querySelectorAll('.select-user:checked');
        return Array.from(checkboxes).map(checkbox => checkbox.value);
    }

    function performBulkUserAction(action, userIds) {
        fetch(BASE_URL + 'admin/bulk-action', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: action,
                ids: userIds,
                type: 'user'
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
});
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>