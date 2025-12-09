<?php require_once dirname(__DIR__) . '/includes/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <!-- Job Details -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <!-- Job Header -->
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h1 class="h3 mb-2"><?php echo htmlspecialchars($job['title']); ?></h1>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-2">
                                    <?php echo ucfirst(str_replace('_', ' ', $job['category'])); ?>
                                </span>
                                <span class="badge bg-success">
                                    <i class="fas fa-dollar-sign"></i> $<?php echo number_format($job['fixed_budget'] ?? $job['max_budget'], 2); ?>
                                </span>
                            </div>
                        </div>
                        <?php if (SessionHelper::isLoggedIn() && SessionHelper::get('account_type') === 'freelancer'): ?>
                            <button class="btn btn-primary" id="applyNowBtn">
                                <i class="fas fa-paper-plane"></i> Apply Now
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- Client Info -->
                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar me-3">
                            <?php if (!empty($job['avatar'])): ?>
                                <img src="<?php echo $job['avatar']; ?>" class="rounded-circle" width="50" height="50">
                            <?php else: ?>
                                <div class="avatar-placeholder rounded-circle bg-secondary d-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h6 class="mb-0"><?php echo htmlspecialchars($job['first_name'] . ' ' . $job['last_name']); ?></h6>
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> Posted <?php echo date('M d, Y', strtotime($job['created_at'])); ?>
                            </small>
                        </div>
                    </div>

                    <!-- Job Description -->
                    <div class="mb-5">
                        <h5 class="mb-3">Job Description</h5>
                        <div class="job-description">
                            <?php echo nl2br(htmlspecialchars($job['description'])); ?>
                        </div>
                    </div>

                    <!-- Requirements -->
                    <?php if (!empty($job['requirements'])): ?>
                        <div class="mb-5">
                            <h5 class="mb-3">Requirements</h5>
                            <ul class="list-group list-group-flush">
                                <?php 
                                $requirements = json_decode($job['requirements'], true) ?? [];
                                foreach ($requirements as $requirement): 
                                ?>
                                    <li class="list-group-item">
                                        <i class="fas fa-check text-success me-2"></i>
                                        <?php echo htmlspecialchars($requirement); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Job Details -->
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Job Details</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <th>Budget</th>
                                            <td class="text-success">$<?php echo number_format($job['fixed_budget'] ?? $job['max_budget'], 2); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Deadline</th>
                                            <td><?php echo $job['deadline'] ? date('M d, Y', strtotime($job['deadline'])) : 'Flexible'; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Job Type</th>
                                            <td><?php echo $job['budget_type'] === 'fixed' ? 'Fixed Price' : 'Hourly'; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Experience Level</th>
                                            <td>Intermediate</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Activity</h6>
                                    <div class="text-center py-3">
                                        <div class="display-4 text-primary mb-2"><?php echo rand(5, 50); ?></div>
                                        <p class="text-muted mb-0">Proposals Submitted</p>
                                    </div>
                                    <div class="text-center">
                                        <div class="display-4 text-success mb-2"><?php echo rand(100, 1000); ?></div>
                                        <p class="text-muted mb-0">Views</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Comments Section -->
                    <div class="comments-section">
                        <h5 class="mb-4">Comments & Questions</h5>
                        
                        <?php if (SessionHelper::isLoggedIn() && SessionHelper::get('account_type') === 'freelancer'): ?>
                            <!-- Comment Form -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <form id="commentForm">
                                        <input type="hidden" name="post_id" value="<?php echo $job['id']; ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Add a comment or question</label>
                                            <textarea class="form-control" name="content" rows="3" placeholder="Ask the client about this job..." required></textarea>
                                        </div>
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-paper-plane"></i> Post Comment
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php elseif (!SessionHelper::isLoggedIn()): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> You must be logged in as a freelancer to comment.
                                <a href="<?php echo BASE_URL; ?>auth/login" class="alert-link">Login</a> or 
                                <a href="<?php echo BASE_URL; ?>auth/register?type=freelancer" class="alert-link">Register as freelancer</a>
                            </div>
                        <?php endif; ?>

                        <!-- Comments List -->
                        <div class="comments-list">
                            <?php if (!empty($comments)): ?>
                                <?php foreach ($comments as $comment): ?>
                                    <div class="comment-item mb-4">
                                        <div class="d-flex">
                                            <div class="avatar me-3">
                                                <?php if (!empty($comment['avatar'])): ?>
                                                    <img src="<?php echo $comment['avatar']; ?>" class="rounded-circle" width="40" height="40">
                                                <?php else: ?>
                                                    <div class="avatar-placeholder rounded-circle bg-secondary d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="comment-header mb-2">
                                                    <strong><?php echo htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']); ?></strong>
                                                    <small class="text-muted ms-2">
                                                        <?php echo date('M d, Y h:i A', strtotime($comment['created_at'])); ?>
                                                    </small>
                                                </div>
                                                <div class="comment-body">
                                                    <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                                                </div>
                                                
                                                <!-- Replies -->
                                                <?php if (!empty($comment['replies'])): ?>
                                                    <div class="replies ms-4 mt-3">
                                                        <?php foreach ($comment['replies'] as $reply): ?>
                                                            <div class="reply-item mb-3">
                                                                <div class="d-flex">
                                                                    <div class="avatar me-3">
                                                                        <?php if (!empty($reply['avatar'])): ?>
                                                                            <img src="<?php echo $reply['avatar']; ?>" class="rounded-circle" width="30" height="30">
                                                                        <?php else: ?>
                                                                            <div class="avatar-placeholder rounded-circle bg-secondary d-flex align-items-center justify-content-center" 
                                                                                 style="width: 30px; height: 30px;">
                                                                                <i class="fas fa-user text-white"></i>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <div class="comment-header mb-1">
                                                                            <strong><?php echo htmlspecialchars($reply['first_name'] . ' ' . $reply['last_name']); ?></strong>
                                                                            <small class="text-muted ms-2">
                                                                                <?php echo date('M d, Y h:i A', strtotime($reply['created_at'])); ?>
                                                                            </small>
                                                                        </div>
                                                                        <div class="comment-body">
                                                                            <p><?php echo nl2br(htmlspecialchars($reply['content'])); ?></p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <!-- Reply Form (for freelancers) -->
                                                <?php if (SessionHelper::isLoggedIn() && SessionHelper::get('account_type') === 'freelancer'): ?>
                                                    <form class="reply-form ms-4 mt-2" data-comment-id="<?php echo $comment['id']; ?>" style="display: none;">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" placeholder="Write a reply..." required>
                                                            <button class="btn btn-outline-primary" type="submit">Reply</button>
                                                        </div>
                                                    </form>
                                                    <button class="btn btn-sm btn-outline-secondary reply-btn" data-comment-id="<?php echo $comment['id']; ?>">
                                                        <i class="fas fa-reply"></i> Reply
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No comments yet. Be the first to comment!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Client Info Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">About the Client</h5>
                    <div class="text-center mb-3">
                        <?php if (!empty($job['avatar'])): ?>
                            <img src="<?php echo $job['avatar']; ?>" class="rounded-circle mb-3" width="100" height="100">
                        <?php else: ?>
                            <div class="avatar-placeholder rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto mb-3" 
                                 style="width: 100px; height: 100px;">
                                <i class="fas fa-user fa-3x text-white"></i>
                            </div>
                        <?php endif; ?>
                        <h6><?php echo htmlspecialchars($job['first_name'] . ' ' . $job['last_name']); ?></h6>
                        <small class="text-muted">Client</small>
                    </div>
                    
                    <div class="client-stats mb-3">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="display-6">4.8</div>
                                <small class="text-muted">Rating</small>
                            </div>
                            <div class="col-4">
                                <div class="display-6">42</div>
                                <small class="text-muted">Jobs</small>
                            </div>
                            <div class="col-4">
                                <div class="display-6">98%</div>
                                <small class="text-muted">Hire Rate</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="client-info">
                        <p><i class="fas fa-map-marker-alt text-muted me-2"></i> United States</p>
                        <p><i class="fas fa-clock text-muted me-2"></i> Member since <?php echo date('Y', strtotime($job['created_at'])); ?></p>
                    </div>
                </div>
            </div>

            <!-- Similar Jobs -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Similar Jobs</h5>
                    <div class="similar-jobs">
                        <?php for ($i = 0; $i < 3; $i++): ?>
                            <div class="similar-job-item mb-3">
                                <h6 class="mb-1">Web Developer Needed</h6>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">$500 - $1,000</small>
                                    <small class="text-muted">2 days ago</small>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                    <a href="<?php echo BASE_URL; ?>wall" class="btn btn-outline-primary w-100 mt-2">
                        <i class="fas fa-search"></i> Browse More Jobs
                    </a>
                </div>
            </div>

            <!-- Application Tips -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Application Tips</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Customize your proposal
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Highlight relevant experience
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Ask specific questions
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Provide portfolio links
                        </li>
                        <li>
                            <i class="fas fa-check text-success me-2"></i>
                            Be professional and polite
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Apply Now button
    const applyNowBtn = document.getElementById('applyNowBtn');
    if (applyNowBtn) {
        applyNowBtn.addEventListener('click', function() {
            const jobId = <?php echo $job['id']; ?>;
            const jobTitle = "<?php echo addslashes($job['title']); ?>";
            
            // Set data for modal
            document.getElementById('applyPostId').value = jobId;
            document.getElementById('applyPostTitle').textContent = jobTitle;
            
            // Show modal
            const applyModal = new bootstrap.Modal(document.getElementById('applyModal'));
            applyModal.show();
        });
    }

    // Comment form submission
    const commentForm = document.getElementById('commentForm');
    if (commentForm) {
        commentForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch(BASE_URL + 'wall/add-comment', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    location.reload();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert('Failed to post comment');
            }
        });
    }

    // Reply buttons
    document.querySelectorAll('.reply-btn').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const replyForm = document.querySelector(`.reply-form[data-comment-id="${commentId}"]`);
            replyForm.style.display = 'block';
            this.style.display = 'none';
        });
    });

    // Reply forms
    document.querySelectorAll('.reply-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const commentId = this.dataset.commentId;
            const content = this.querySelector('input').value;
            
            try {
                const response = await fetch(BASE_URL + 'wall/add-comment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        post_id: <?php echo $job['id']; ?>,
                        content: content,
                        parent_id: commentId
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    location.reload();
                }
            } catch (error) {
                alert('Failed to post reply');
            }
        });
    });
});
</script>

<style>
.job-description {
    line-height: 1.8;
    color: #444;
}
.comment-item {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
}
.reply-form {
    transition: all 0.3s ease;
}
.similar-job-item {
    padding: 10px;
    border-bottom: 1px solid #eee;
}
.similar-job-item:last-child {
    border-bottom: none;
}
</style>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>