<?php require_once dirname(__DIR__) . '/includes/header.php'; ?>

<div class="wall-container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="display-6 mb-2">
                <?php if ($search_query): ?>
                    Search Results for "<?php echo htmlspecialchars($search_query); ?>"
                <?php else: ?>
                    All Job Posts
                <?php endif; ?>
            </h1>
            <p class="text-muted mb-0">
                <?php echo count($jobs); ?> jobs found
                <?php if ($search_query): ?>
                    • <a href="<?php echo BASE_URL; ?>wall" class="text-primary">Clear search</a>
                <?php endif; ?>
            </p>
        </div>
        <div class="btn-group" role="group">
            <button class="btn btn-outline-primary active" data-view="grid">
                <i class="fas fa-th-large"></i>
            </button>
            <button class="btn btn-outline-primary" data-view="list">
                <i class="fas fa-list"></i>
            </button>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="card mb-5">
        <div class="card-body">
            <form action="<?php echo BASE_URL; ?>wall/search" method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="q" placeholder="Search jobs..." 
                           value="<?php echo htmlspecialchars($search_query); ?>">
                </div>
                <div class="col-md-2">
                    <select class="form-control" name="category">
                        <option value="">All Categories</option>
                        <option value="web_development" <?php echo $selected_category === 'web_development' ? 'selected' : ''; ?>>
                            Web Development
                        </option>
                        <option value="mobile_development" <?php echo $selected_category === 'mobile_development' ? 'selected' : ''; ?>>
                            Mobile Development
                        </option>
                        <option value="design" <?php echo $selected_category === 'design' ? 'selected' : ''; ?>>
                            Design
                        </option>
                        <option value="writing" <?php echo $selected_category === 'writing' ? 'selected' : ''; ?>>
                            Writing
                        </option>
                        <option value="marketing" <?php echo $selected_category === 'marketing' ? 'selected' : ''; ?>>
                            Marketing
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control" name="budget_range">
                        <option value="">Budget Range</option>
                        <option value="0-100">Under $100</option>
                        <option value="100-500">$100 - $500</option>
                        <option value="500-1000">$500 - $1,000</option>
                        <option value="1000-5000">$1,000 - $5,000</option>
                        <option value="5000+">$5,000+</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control" name="job_type">
                        <option value="">Job Type</option>
                        <option value="fixed">Fixed Price</option>
                        <option value="hourly">Hourly</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control" name="experience">
                        <option value="">Experience Level</option>
                        <option value="entry">Entry Level</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="expert">Expert</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Jobs Grid/List -->
    <div id="jobsView" class="<?php echo isset($_GET['view']) && $_GET['view'] === 'list' ? 'list-view' : 'grid-view'; ?>">
        <?php if (!empty($jobs)): ?>
            <?php if (!isset($_GET['view']) || $_GET['view'] !== 'list'): ?>
                <!-- Grid View -->
                <div class="row" id="jobsGrid">
                    <?php foreach ($jobs as $job): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 job-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="card-title mb-1"><?php echo htmlspecialchars($job['title']); ?></h5>
                                            <div class="d-flex align-items-center">
                                                <small class="text-muted me-3">
                                                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($job['first_name'] . ' ' . $job['last_name']); ?>
                                                </small>
                                                <span class="badge bg-light text-dark">
                                                    <i class="fas fa-star text-warning"></i> 4.8
                                                </span>
                                            </div>
                                        </div>
                                        <?php if (SessionHelper::isLoggedIn() && SessionHelper::get('account_type') === 'freelancer'): ?>
                                            <button class="btn btn-sm btn-outline-primary save-job" 
                                                    data-job-id="<?php echo $job['id']; ?>"
                                                    data-saved="false">
                                                <i class="fas fa-bookmark-o"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <p class="card-text text-muted mb-3">
                                        <?php echo substr(htmlspecialchars($job['description']), 0, 120); ?>...
                                    </p>
                                    
                                    <div class="job-details mb-3">
                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                            <span class="badge bg-primary">
                                                <?php echo ucfirst(str_replace('_', ' ', $job['category'])); ?>
                                            </span>
                                            <span class="badge bg-success">
                                                <i class="fas fa-clock"></i> <?php echo $job['deadline'] ? date('M d', strtotime($job['deadline'])) : 'Flexible'; ?>
                                            </span>
                                            <span class="badge bg-info">
                                                <i class="fas fa-briefcase"></i> <?php echo $job['budget_type'] === 'fixed' ? 'Fixed' : 'Hourly'; ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h4 class="text-success mb-0">
                                                $<?php echo number_format($job['fixed_budget'] ?? $job['max_budget'], 2); ?>
                                            </h4>
                                            <small class="text-muted">
                                                <?php echo $job['budget_type'] === 'fixed' ? 'Fixed Price' : 'Up to'; ?>
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <div class="text-muted">
                                                <i class="fas fa-paper-plane"></i> <?php echo rand(5, 50); ?> proposals
                                            </div>
                                            <small class="text-muted">
                                                <i class="fas fa-eye"></i> <?php echo rand(100, 1000); ?> views
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between">
                                        <a href="<?php echo BASE_URL; ?>wall/post/<?php echo $job['id']; ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                        <?php if (SessionHelper::isLoggedIn() && SessionHelper::get('account_type') === 'freelancer'): ?>
                                            <button class="btn btn-success btn-sm apply-job" 
                                                    data-job-id="<?php echo $job['id']; ?>" 
                                                    data-job-title="<?php echo htmlspecialchars($job['title']); ?>">
                                                <i class="fas fa-paper-plane"></i> Apply Now
                                            </button>
                                        <?php elseif (!SessionHelper::isLoggedIn()): ?>
                                            <a href="<?php echo BASE_URL; ?>auth/register?type=freelancer" class="btn btn-success btn-sm">
                                                <i class="fas fa-user-plus"></i> Sign Up to Apply
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- List View -->
                <div class="list-group" id="jobsList">
                    <?php foreach ($jobs as $job): ?>
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="mb-1"><?php echo htmlspecialchars($job['title']); ?></h5>
                                    <p class="mb-1 text-muted">
                                        <?php echo substr(htmlspecialchars($job['description']), 0, 200); ?>...
                                    </p>
                                    <div class="d-flex align-items-center">
                                        <small class="text-muted me-3">
                                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($job['first_name'] . ' ' . $job['last_name']); ?>
                                        </small>
                                        <span class="badge bg-primary me-2">
                                            <?php echo ucfirst(str_replace('_', ' ', $job['category'])); ?>
                                        </span>
                                        <span class="badge bg-success me-2">
                                            $<?php echo number_format($job['fixed_budget'] ?? $job['max_budget'], 2); ?>
                                        </span>
                                        <span class="badge bg-info">
                                            <i class="fas fa-paper-plane"></i> <?php echo rand(5, 50); ?> proposals
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo BASE_URL; ?>wall/post/<?php echo $job['id']; ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <?php if (SessionHelper::isLoggedIn() && SessionHelper::get('account_type') === 'freelancer'): ?>
                                            <button class="btn btn-success btn-sm apply-job" 
                                                    data-job-id="<?php echo $job['id']; ?>" 
                                                    data-job-title="<?php echo htmlspecialchars($job['title']); ?>">
                                                <i class="fas fa-paper-plane"></i> Apply
                                            </button>
                                        <?php endif; ?>
                                        <?php if (SessionHelper::isLoggedIn() && SessionHelper::get('account_type') === 'freelancer'): ?>
                                            <button class="btn btn-outline-secondary btn-sm save-job" 
                                                    data-job-id="<?php echo $job['id']; ?>"
                                                    data-saved="false">
                                                <i class="fas fa-bookmark-o"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- Pagination -->
            <nav aria-label="Page navigation" class="mt-5">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo ($current_page > 1) ? BASE_URL . 'wall/all-posts?page=' . ($current_page - 1) : '#'; ?>">
                            Previous
                        </a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                            <a class="page-link" href="<?php echo BASE_URL . 'wall/all-posts?page=' . $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo ($current_page < $total_pages) ? BASE_URL . 'wall/all-posts?page=' . ($current_page + 1) : '#'; ?>">
                            Next
                        </a>
                    </li>
                </ul>
            </nav>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-search fa-4x text-muted mb-4"></i>
                <h4>No jobs found</h4>
                <p class="text-muted">
                    <?php if ($search_query): ?>
                        No jobs match your search criteria. Try different keywords or filters.
                    <?php else: ?>
                        There are currently no active job posts. Check back later!
                    <?php endif; ?>
                </p>
                <a href="<?php echo BASE_URL; ?>wall" class="btn btn-primary">
                    <i class="fas fa-redo"></i> Back to All Jobs
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Categories -->
    <div class="mt-5">
        <h5 class="mb-4">Browse by Category</h5>
        <div class="row">
            <div class="col-md-2 col-6 mb-3">
                <a href="<?php echo BASE_URL; ?>wall/search?category=web_development" class="category-card">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-code fa-2x text-primary mb-2"></i>
                            <h6>Web Development</h6>
                            <small class="text-muted"><?php echo rand(50, 200); ?> jobs</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-6 mb-3">
                <a href="<?php echo BASE_URL; ?>wall/search?category=mobile_development" class="category-card">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-mobile-alt fa-2x text-primary mb-2"></i>
                            <h6>Mobile Development</h6>
                            <small class="text-muted"><?php echo rand(30, 150); ?> jobs</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-6 mb-3">
                <a href="<?php echo BASE_URL; ?>wall/search?category=design" class="category-card">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-paint-brush fa-2x text-primary mb-2"></i>
                            <h6>Design</h6>
                            <small class="text-muted"><?php echo rand(40, 180); ?> jobs</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-6 mb-3">
                <a href="<?php echo BASE_URL; ?>wall/search?category=writing" class="category-card">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-pen-fancy fa-2x text-primary mb-2"></i>
                            <h6>Writing</h6>
                            <small class="text-muted"><?php echo rand(60, 220); ?> jobs</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-6 mb-3">
                <a href="<?php echo BASE_URL; ?>wall/search?category=marketing" class="category-card">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-bullhorn fa-2x text-primary mb-2"></i>
                            <h6>Marketing</h6>
                            <small class="text-muted"><?php echo rand(35, 160); ?> jobs</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-6 mb-3">
                <a href="<?php echo BASE_URL; ?>wall" class="category-card">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-list fa-2x text-primary mb-2"></i>
                            <h6>View All</h6>
                            <small class="text-muted"><?php echo count($jobs); ?>+ jobs</small>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.job-card {
    transition: transform 0.3s, box-shadow 0.3s;
    border: 1px solid #e0e0e0;
}
.job-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    border-color: #667eea;
}
.category-card {
    text-decoration: none;
    color: inherit;
    display: block;
}
.category-card .card {
    transition: transform 0.3s;
}
.category-card:hover .card {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.list-view .job-card {
    margin-bottom: 10px;
}
.grid-view .job-card {
    height: 100%;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // View toggle
    document.querySelectorAll('[data-view]').forEach(button => {
        button.addEventListener('click', function() {
            const view = this.dataset.view;
            
            // Update active button
            document.querySelectorAll('[data-view]').forEach(btn => {
                btn.classList.remove('active');
            });
            this.classList.add('active');
            
            // Toggle view
            document.getElementById('jobsView').className = view + '-view';
            
            // Update URL
            const url = new URL(window.location);
            url.searchParams.set('view', view);
            window.history.pushState({}, '', url);
        });
    });
    
    // Save job
    document.querySelectorAll('.save-job').forEach(button => {
        button.addEventListener('click', function() {
            const jobId = this.dataset.jobId;
            const isSaved = this.dataset.saved === 'true';
            
            fetch(BASE_URL + 'freelancer/save-post', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    post_id: jobId,
                    action: isSaved ? 'unsave' : 'save'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (isSaved) {
                        this.innerHTML = '<i class="fas fa-bookmark-o"></i>';
                        this.dataset.saved = 'false';
                    } else {
                        this.innerHTML = '<i class="fas fa-bookmark"></i>';
                        this.dataset.saved = 'true';
                    }
                }
            });
        });
    });
    
    // Apply job
    document.querySelectorAll('.apply-job').forEach(button => {
        button.addEventListener('click', function() {
            const jobId = this.dataset.jobId;
            const jobTitle = this.dataset.jobTitle;
            
            // Set data for modal
            document.getElementById('applyPostId').value = jobId;
            document.getElementById('applyPostTitle').textContent = jobTitle;
            
            // Show modal
            const applyModal = new bootstrap.Modal(document.getElementById('applyModal'));
            applyModal.show();
        });
    });
});
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>