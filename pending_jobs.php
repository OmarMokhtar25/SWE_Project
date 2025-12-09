<?php require_once dirname(__DIR__) . '/includes/header.php'; ?>

<div class="admin-dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Pending Job Approvals</h1>
        <div class="btn-group" role="group">
            <button class="btn btn-outline-primary active" data-filter="all">All</button>
            <button class="btn btn-outline-primary" data-filter="web_development">Web Dev</button>
            <button class="btn btn-outline-primary" data-filter="design">Design</button>
            <button class="btn btn-outline-primary" data-filter="writing">Writing</button>
        </div>
    </div>

    <?php if (!empty($pending_jobs)): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th>Job Title</th>
                        <th>Client</th>
                        <th>Category</th>
                        <th>Budget</th>
                        <th>Posted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending_jobs as $job): ?>
                        <tr data-category="<?php echo $job['category']; ?>">
                            <td>
                                <input type="checkbox" class="select-job" value="<?php echo $job['id']; ?>">
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($job['title']); ?></strong>
                                <p class="text-muted mb-0 small"><?php echo substr(htmlspecialchars($job['description']), 0, 50); ?>...</p>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($job['first_name'] . ' ' . $job['last_name']); ?>
                                <br>
                                <small class="text-muted"><?php echo htmlspecialchars($job['email']); ?></small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    <?php echo ucfirst(str_replace('_', ' ', $job['category'])); ?>
                                </span>
                            </td>
                            <td>
                                <strong class="text-success">$<?php echo number_format($job['fixed_budget'] ?? 0, 2); ?></strong>
                            </td>
                            <td>
                                <?php echo date('M d, Y', strtotime($job['created_at'])); ?>
                                <br>
                                <small class="text-muted"><?php echo date('h:i A', strtotime($job['created_at'])); ?></small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-outline-primary view-job" data-id="<?php echo $job['id']; ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-success approve-job" data-id="<?php echo $job['id']; ?>">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-outline-danger reject-job" data-id="<?php echo $job['id']; ?>">
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
                        <select class="form-control" id="bulkAction">
                            <option value="">Bulk Action</option>
                            <option value="approve">Approve Selected</option>
                            <option value="reject">Reject Selected</option>
                            <option value="delete">Delete Selected</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" id="applyBulkAction">Apply</button>
                    </div>
                    <div class="col-md-6 text-end">
                        <small class="text-muted">
                            <?php echo count($pending_jobs); ?> jobs pending approval
                        </small>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-check-circle fa-4x text-success mb-4"></i>
            <h4>No pending jobs</h4>
            <p class="text-muted">All jobs have been reviewed and approved.</p>
            <a href="<?php echo BASE_URL; ?>admin/dashboard" class="btn btn-primary">
                <i class="fas fa-tachometer-alt"></i> Back to Dashboard
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Job Details Modal -->
<div class="modal fade" id="jobModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Job Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="jobDetails">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger reject-job-modal">Reject</button>
                <button type="button" class="btn btn-success approve-job-modal">Approve</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const jobModal = new bootstrap.Modal(document.getElementById('jobModal'));
    let currentJobId = null;

    // Select all checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.select-job');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // View job details
    document.querySelectorAll('.view-job').forEach(button => {
        button.addEventListener('click', function() {
            currentJobId = this.dataset.id;
            loadJobDetails(currentJobId);
        });
    });

    // Approve job
    document.querySelectorAll('.approve-job').forEach(button => {
        button.addEventListener('click', function() {
            const jobId = this.dataset.id;
            approveJob(jobId);
        });
    });

    // Reject job
    document.querySelectorAll('.reject-job').forEach(button => {
        button.addEventListener('click', function() {
            const jobId = this.dataset.id;
            rejectJob(jobId);
        });
    });

    // Modal approve/reject buttons
    document.querySelector('.approve-job-modal').addEventListener('click', function() {
        if (currentJobId) {
            approveJob(currentJobId);
            jobModal.hide();
        }
    });

    document.querySelector('.reject-job-modal').addEventListener('click', function() {
        if (currentJobId) {
            rejectJob(currentJobId);
            jobModal.hide();
        }
    });

    // Bulk actions
    document.getElementById('applyBulkAction').addEventListener('click', function() {
        const action = document.getElementById('bulkAction').value;
        const selectedJobs = getSelectedJobs();

        if (!action) {
            alert('Please select a bulk action');
            return;
        }

        if (selectedJobs.length === 0) {
            alert('Please select at least one job');
            return;
        }

        if (confirm(`Are you sure you want to ${action} ${selectedJobs.length} job(s)?`)) {
            performBulkAction(action, selectedJobs);
        }
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

            // Filter rows
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                if (filter === 'all' || row.dataset.category === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    function loadJobDetails(jobId) {
        fetch(BASE_URL + 'admin/get-job-details/' + jobId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const job = data.details;
                    
                    const html = `
                        <div class="job-details">
                            <h4>${job.title}</h4>
                            <p><strong>Client:</strong> ${job.first_name} ${job.last_name} (${job.client_email})</p>
                            <p><strong>Category:</strong> ${job.category}</p>
                            <p><strong>Budget:</strong> $${job.fixed_budget}</p>
                            <p><strong>Deadline:</strong> ${job.deadline}</p>
                            
                            <hr>
                            
                            <h5>Description</h5>
                            <p>${job.description}</p>
                            
                            <h5>Requirements</h5>
                            <ul>
                                ${JSON.parse(job.requirements || '[]').map(req => `<li>${req}</li>`).join('')}
                            </ul>
                            
                            <hr>
                            
                            <p><strong>Posted:</strong> ${new Date(job.created_at).toLocaleString()}</p>
                        </div>
                    `;
                    
                    document.getElementById('jobDetails').innerHTML = html;
                    jobModal.show();
                }
            });
    }

    function approveJob(jobId) {
        if (confirm('Approve this job post?')) {
            fetch(BASE_URL + 'admin/approve-job', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: jobId })
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

    function rejectJob(jobId) {
        if (confirm('Reject this job post?')) {
            fetch(BASE_URL + 'admin/reject-job', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: jobId })
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

    function getSelectedJobs() {
        const checkboxes = document.querySelectorAll('.select-job:checked');
        return Array.from(checkboxes).map(checkbox => checkbox.value);
    }

    function performBulkAction(action, jobIds) {
        fetch(BASE_URL + 'admin/bulk-action', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: action,
                ids: jobIds,
                type: 'job'
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