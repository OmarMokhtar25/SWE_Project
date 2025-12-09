<?php require_once dirname(__DIR__) . '/includes/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Job Posts</h2>
        <a href="<?php echo BASE_URL; ?>client/post" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Create New Job
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Status Filter</label>
                    <select class="form-control" id="statusFilter">
                        <option value="all" <?php echo $current_status === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                        <option value="active" <?php echo $current_status === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="pending" <?php echo $current_status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="completed" <?php echo $current_status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $current_status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Category</label>
                    <select class="form-control" id="categoryFilter">
                        <option value="">All Categories</option>
                        <option value="web_development">Web Development</option>
                        <option value="mobile_development">Mobile Development</option>
                        <option value="design">Design</option>
                        <option value="writing">Writing</option>
                        <option value="marketing">Marketing</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sort By</label>
                    <select class="form-control" id="sortFilter">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="budget_high">Budget (High to Low)</option>
                        <option value="budget_low">Budget (Low to High)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Jobs Grid -->
    <?php if (!empty($jobs)): ?>
        <div class="row" id="jobsGrid">
            <?php foreach ($jobs as $job): ?>
                <div class="col-md-6 mb-4" data-status="<?php echo $job['status']; ?>" data-category="<?php echo $job['category']; ?>">
                    <div class="card h-100 job-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title mb-0"><?php echo htmlspecialchars($job['title']); ?></h5>
                                <span class="badge bg-<?php echo getStatusColor($job['status']); ?>">
                                    <?php echo ucfirst($job['status']); ?>
                                </span>
                            </div>
                            
                            <p class="card-text text-muted mb-3">
                                <?php echo substr(htmlspecialchars($job['description']), 0, 150); ?>...
                            </p>
                            
                            <div class="job-meta mb-3">
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-tag"></i> <?php echo ucfirst(str_replace('_', ' ', $job['category'])); ?>
                                    </span>
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($job['created_at'])); ?>
                                    </span>
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-clock"></i> Deadline: <?php echo date('M d, Y', strtotime($job['deadline'])); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h4 class="text-primary mb-0">$<?php echo number_format($job['fixed_budget'], 2); ?></h4>
                                    <small class="text-muted">Budget</small>
                                </div>
                                <div>
                                    <h4 class="text-success mb-0"><?php echo rand(3, 15); ?></h4>
                                    <small class="text-muted">Proposals</small>
                                </div>
                                <div>
                                    <h4 class="text-info mb-0"><?php echo rand(50, 500); ?></h4>
                                    <small class="text-muted">Views</small>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="<?php echo BASE_URL; ?>client/view-post/<?php echo $job['id']; ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                <div class="btn-group" role="group">
                                    <a href="<?php echo BASE_URL; ?>client/edit-post/<?php echo $job['id']; ?>" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-sm delete-job" data-id="<?php echo $job['id']; ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-briefcase fa-4x text-muted mb-4"></i>
            <h4>No job posts found</h4>
            <p class="text-muted">You haven't created any job posts yet.</p>
            <a href="<?php echo BASE_URL; ?>client/post" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Create Your First Job
            </a>
        </div>
    <?php endif; ?>
    
    <!-- Pagination -->
    <?php if (count($jobs) > 0): ?>
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

<style>
.job-card {
    transition: transform 0.3s, box-shadow 0.3s;
}
.job-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter jobs
    const filters = document.querySelectorAll('#statusFilter, #categoryFilter, #sortFilter');
    filters.forEach(filter => {
        filter.addEventListener('change', function() {
            filterJobs();
        });
    });

    // Delete job
    document.querySelectorAll('.delete-job').forEach(button => {
        button.addEventListener('click', function() {
            const jobId = this.dataset.id;
            
            if (confirm('Are you sure you want to delete this job post?')) {
                fetch(BASE_URL + 'client/delete-post/' + jobId, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                    }
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
    });

    function filterJobs() {
        const status = document.getElementById('statusFilter').value;
        const category = document.getElementById('categoryFilter').value;
        const sort = document.getElementById('sortFilter').value;
        
        const jobs = document.querySelectorAll('.job-card').parentElement;
        
        // Implement filtering logic here
        // For now, just reload with query parameters
        window.location.href = BASE_URL + 'client/view-posts?status=' + status + 
                              '&category=' + category + '&sort=' + sort;
    }
});

<?php 
function getStatusColor($status) {
    switch($status) {
        case 'active': return 'success';
        case 'pending': return 'warning';
        case 'completed': return 'info';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}
?>
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>