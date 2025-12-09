<?php require_once dirname(__DIR__) . '/includes/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Saved Posts</h2>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-primary active" data-filter="all">All</button>
            <button type="button" class="btn btn-outline-primary" data-filter="web_development">Web Dev</button>
            <button type="button" class="btn btn-outline-primary" data-filter="design">Design</button>
            <button type="button" class="btn btn-outline-primary" data-filter="writing">Writing</button>
        </div>
    </div>

    <?php if (!empty($saved_posts)): ?>
        <div class="row" id="savedPostsGrid">
            <?php foreach ($saved_posts as $post): ?>
                <div class="col-md-4 mb-4" data-category="<?php echo $post['category']; ?>">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title mb-0"><?php echo htmlspecialchars($post['title']); ?></h5>
                                <button class="btn btn-sm btn-outline-danger unsave-post" data-post-id="<?php echo $post['id']; ?>">
                                    <i class="fas fa-bookmark"></i>
                                </button>
                            </div>
                            
                            <p class="card-text text-muted mb-3">
                                <?php echo substr(htmlspecialchars($post['description']), 0, 100); ?>...
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($post['first_name'] . ' ' . $post['last_name']); ?>
                                </span>
                                <span class="badge bg-primary">
                                    $<?php echo number_format($post['budget'], 2); ?>
                                </span>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                                </small>
                                <a href="<?php echo BASE_URL; ?>wall/post/<?php echo $post['id']; ?>" class="btn btn-sm btn-primary">
                                    View Post
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-bookmark fa-4x text-muted mb-4"></i>
            <h4>No saved posts</h4>
            <p class="text-muted">Save interesting job posts to view them later.</p>
            <a href="<?php echo BASE_URL; ?>client/wall" class="btn btn-primary">
                <i class="fas fa-search"></i> Browse Jobs
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter saved posts
    document.querySelectorAll('[data-filter]').forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active button
            document.querySelectorAll('[data-filter]').forEach(btn => {
                btn.classList.remove('active');
            });
            this.classList.add('active');
            
            // Filter posts
            const posts = document.querySelectorAll('#savedPostsGrid > div');
            posts.forEach(post => {
                if (filter === 'all' || post.dataset.category === filter) {
                    post.style.display = 'block';
                } else {
                    post.style.display = 'none';
                }
            });
        });
    });

    // Unsave post
    document.querySelectorAll('.unsave-post').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            
            fetch(BASE_URL + 'client/unsave-post', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ post_id: postId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.closest('.col-md-4').remove();
                    
                    // Check if no posts left
                    if (document.querySelectorAll('#savedPostsGrid > div').length === 0) {
                        location.reload();
                    }
                }
            });
        });
    });
});
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>