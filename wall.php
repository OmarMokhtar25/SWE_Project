<?php require_once dirname(__DIR__) . '/includes/header.php'; ?>

<div class="container-fluid mt-4">
    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-md-3">
            <div class="card sticky-top" style="top: 80px;">
                <div class="card-body">
                    <h5 class="card-title">Filters</h5>
                    
                    <!-- Search -->
                    <div class="mb-3">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" id="searchInput" placeholder="Job title, skills..." value="<?php echo htmlspecialchars($search_query); ?>">
                    </div>
                    
                    <!-- Category -->
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-control" id="categoryFilter">
                            <option value="">All Categories</option>
                            <option value="web_development" <?php echo $selected_category === 'web_development' ? 'selected' : ''; ?>>Web Development</option>
                            <option value="mobile_development" <?php echo $selected_category === 'mobile_development' ? 'selected' : ''; ?>>Mobile Development</option>
                            <option value="design" <?php echo $selected_category === 'design' ? 'selected' : ''; ?>>Design</option>
                            <option value="writing" <?php echo $selected_category === 'writing' ? 'selected' : ''; ?>>Writing</option>
                            <option value="marketing" <?php echo $selected_category === 'marketing' ? 'selected' : ''; ?>>Marketing</option>
                        </select>
                    </div>
                    
                    <!-- Budget Range -->
                    <div class="mb-3">
                        <label class="form-label">Budget Range</label>
                        <div class="row g-2">
                            <div class="col">
                                <input type="number" class="form-control" id="minBudget" placeholder="Min" value="<?php echo htmlspecialchars($budget_min); ?>">
                            </div>
                            <div class="col">
                                <input type="number" class="form-control" id="maxBudget" placeholder="Max" value="<?php echo htmlspecialchars($budget_max); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Job Type -->
                    <div class="mb-3">
                        <label class="form-label">Job Type</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="typeFixed" checked>
                            <label class="form-check-label" for="typeFixed">Fixed Price</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="typeHourly" checked>
                            <label class="form-check-label" for="typeHourly">Hourly</label>
                        </div>
                    </div>
                    
                    <!-- Experience Level -->
                    <div class="mb-3">
                        <label class="form-label">Experience Level</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="levelEntry" checked>
                            <label class="form-check-label" for="levelEntry">Entry Level</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="levelIntermediate" checked>
                            <label class="form-check-label" for="levelIntermediate">Intermediate</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="levelExpert" checked>
                            <label class="form-check-label" for="levelExpert">Expert</label>
                        </div>
                    </div>
                    
                    <!-- Location -->
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <select class="form-control" id="locationFilter">
                            <option value="">Any Location</option>
                            <option value="remote">Remote Only</option>
                            <option value="onsite">On-site</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                    </div>
                    
                    <!-- Posted Date -->
                    <div class="mb-3">
                        <label class="form-label">Posted</label>
                        <select class="form-control" id="postedFilter">
                            <option value="">Any Time</option>
                            <option value="1">Last 24 hours</option>
                            <option value="3">Last 3 days</option>
                            <option value="7">Last week</option>
                            <option value="30">Last month</option>
                        </select>
                    </div>
                    
                    <button class="btn btn-primary w-100" id="applyFilters">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <button class="btn btn-outline-secondary w-100 mt-2" id="resetFilters">
                        Reset Filters
                    </button>
                </div>
            </div>
            
            <!-- Skills Match -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title">Skills Match</h6>
                    <div class="skills-list">
                        <span class="badge bg-primary mb-1">PHP</span>
                        <span class="badge bg-primary mb-1">JavaScript</span>
                        <span class="badge bg-primary mb-1">Laravel</span>
                        <span class="badge bg-primary mb-1">React</span>
                        <span class="badge bg-secondary mb-1">+5 more</span>
                    </div>
                    <button class="btn btn-sm btn-outline-primary w-100 mt-2">
                        Update Skills
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Jobs List -->
        <div class="col-md-9">
            <!-- Stats Bar -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body py-3">
                            <h6 class="mb-0">Total Jobs</h6>
                            <h3 class="mb-0"><?php echo count($jobs); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body py-3">
                            <h6 class="mb-0">Perfect Matches</h6>
                            <h3 class="mb-0">12</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body py-3">
                            <h6 class="mb-0">Applied</h6>
                            <h3 class="mb-0">8</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body py-3">
                            <h6 class="mb-0">Saved</h6>
                            <h3 class="mb-0">5</h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sort and View Options -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0">Available Jobs</h4>
                    <p class="text-muted mb-0">Find your next freelance opportunity</p>
                </div>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary active" data-sort="relevance">
                        <i class="fas fa-fire"></i> Relevance
                    </button>
                    <button type="button" class="btn btn-outline-primary" data-sort="newest">
                        <i class="fas fa-clock"></i> Newest
                    </button>
                    <button type="button" class="btn btn-outline-primary" data-sort="budget">
                        <i class="fas fa-dollar-sign"></i> Budget
                    </button>
                </div>
            </div>
            
            <!-- Jobs Grid -->
            <?php if (!empty($jobs)): ?>
                <div class="row" id="jobsList">
                    <?php foreach ($jobs as $job): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 job-card">
                                <div class="card-body">
                                    <!-- Job Header -->
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
                                        <button class="btn btn-sm btn-outline-primary save-job" data-job-id="<?php echo $job['id']; ?>"
                                                data-saved="<?php echo $job['is_saved'] ? 'true' : 'false'; ?>">
                                            <i class="fas fa-bookmark<?php echo $job['is_saved'] ? '' : '-o'; ?>"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Job Description -->
                                    <p class="card-text text-muted mb-3">
                                        <?php echo substr(htmlspecialchars($job['description']), 0, 120); ?>...
                                    </p>
                                    
                                    <!-- Job Details -->
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
                                            <span class="badge bg-warning">
                                                <i class="fas fa-map-marker-alt"></i> Remote
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Budget and Stats -->
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
                                    
                                    <!-- Actions -->
                                    <div class="d-flex justify-content-between">
                                        <a href="<?php echo BASE_URL; ?>wall/post/<?php echo $job['id']; ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                        <button class="btn btn-success btn-sm apply-job" data-job-id="<?php echo $job['id']; ?>" 
                                                data-job-title="<?php echo htmlspecialchars($job['title']); ?>">
                                            <i class="fas fa-paper-plane"></i> Apply Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
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
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-4"></i>
                    <h4>No jobs found</h4>
                    <p class="text-muted">Try adjusting your filters or search terms.</p>
                    <button class="btn btn-primary" id="resetFilters">
                        <i class="fas fa-redo"></i> Reset Filters
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.job-card {
    transition: all 0.3s ease;
    border: 1px solid #e0e0e0;
}
.job-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    border-color: #667eea;
}
.sticky-top {
    position: sticky;
    z-index: 100;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Apply filters
    const applyFiltersBtn = document.getElementById('applyFilters');
    const resetFiltersBtn = document.getElementById('resetFilters');
    
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            applyFilters();
        });
    }
    
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function() {
            resetFilters();
        });
    }
    
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
    
    // Sort buttons
    document.querySelectorAll('[data-sort]').forEach(button => {
        button.addEventListener('click', function() {
            const sort = this.dataset.sort;
            
            // Update active button
            document.querySelectorAll('[data-sort]').forEach(btn => {
                btn.classList.remove('active');
            });
            this.classList.add('active');
            
            // Sort jobs (you would typically reload with sort parameter)
            window.location.href = BASE_URL + 'freelancer/wall?sort=' + sort;
        });
    });
    
    function applyFilters() {
        const search = document.getElementById('searchInput').value;
        const category = document.getElementById('categoryFilter').value;
        const minBudget = document.getElementById('minBudget').value;
        const maxBudget = document.getElementById('maxBudget').value;
        const location = document.getElementById('locationFilter').value;
        const posted = document.getElementById('postedFilter').value;
        
        let url = BASE_URL + 'freelancer/wall?';
        if (search) url += 'q=' + encodeURIComponent(search) + '&';
        if (category) url += 'category=' + category + '&';
        if (minBudget) url += 'budget_min=' + minBudget + '&';
        if (maxBudget) url += 'budget_max=' + maxBudget + '&';
        if (location) url += 'location=' + location + '&';
        if (posted) url += 'posted=' + posted;
        
        window.location.href = url;
    }
    
    function resetFilters() {
        window.location.href = BASE_URL + 'freelancer/wall';
    }
    
    // Quick apply from job cards
    document.querySelectorAll('.apply-job').forEach(button => {
        button.addEventListener('click', function() {
            const jobId = this.dataset.jobId;
            const jobTitle = this.dataset.jobTitle;
            
            // Set data for modal
            document.getElementById('applyPostId').value = jobId;
            document.getElementById('applyPostTitle').textContent = jobTitle;
            
            // Show modal (assuming you have a modal with id 'applyModal')
            const applyModal = new bootstrap.Modal(document.getElementById('applyModal'));
            applyModal.show();
        });
    });
});
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>