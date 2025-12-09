// Client JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Post creation modal
    const createPostBtn = document.getElementById('createPostBtn');
    const postModal = document.getElementById('postModal');
    const closeModal = document.querySelector('.close-modal');
    
    if (createPostBtn && postModal) {
        createPostBtn.addEventListener('click', function() {
            postModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        });
        
        if (closeModal) {
            closeModal.addEventListener('click', function() {
                postModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            });
        }
        
        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            if (e.target === postModal) {
                postModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    }

    // Post form submission
    const postForm = document.getElementById('postForm');
    if (postForm) {
        postForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            try {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Creating...';
                
                const response = await fetch(BASE_URL + 'client/create-post', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage('postMessage', result.message, 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showMessage('postMessage', result.message, 'error');
                    if (result.errors) {
                        displayErrors(result.errors);
                    }
                }
            } catch (error) {
                showMessage('postMessage', 'An error occurred. Please try again.', 'error');
                console.error('Error:', error);
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }

    // File upload handling
    const fileUpload = document.querySelector('.file-upload');
    if (fileUpload) {
        fileUpload.addEventListener('click', function() {
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.multiple = true;
            fileInput.accept = '.pdf,.doc,.docx,.jpg,.jpeg,.png';
            
            fileInput.addEventListener('change', function(e) {
                const files = e.target.files;
                const fileList = document.querySelector('.file-list');
                
                for (let file of files) {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'file-item';
                    fileItem.innerHTML = `
                        <span>${file.name}</span>
                        <span class="remove-file" data-name="${file.name}">×</span>
                    `;
                    fileList.appendChild(fileItem);
                }
                
                // Add remove functionality
                document.querySelectorAll('.remove-file').forEach(removeBtn => {
                    removeBtn.addEventListener('click', function() {
                        this.parentElement.remove();
                    });
                });
            });
            
            fileInput.click();
        });
    }

    // Add requirement field
    const addRequirementBtn = document.getElementById('addRequirementBtn');
    const requirementsContainer = document.getElementById('requirementsContainer');
    
    if (addRequirementBtn && requirementsContainer) {
        addRequirementBtn.addEventListener('click', function() {
            const requirementItem = document.createElement('div');
            requirementItem.className = 'requirement-item';
            requirementItem.innerHTML = `
                <input type="text" name="requirements[]" placeholder="Enter requirement" required>
                <button type="button" class="remove-requirement">×</button>
            `;
            requirementsContainer.appendChild(requirementItem);
            
            // Add remove functionality
            requirementItem.querySelector('.remove-requirement').addEventListener('click', function() {
                requirementItem.remove();
            });
        });
    }

    // Budget calculation
    const budgetType = document.getElementById('budgetType');
    const budgetRange = document.getElementById('budgetRange');
    const minBudget = document.getElementById('minBudget');
    const maxBudget = document.getElementById('maxBudget');
    const fixedBudget = document.getElementById('fixedBudget');
    
    if (budgetType) {
        budgetType.addEventListener('change', function() {
            if (this.value === 'range') {
                budgetRange.style.display = 'block';
                fixedBudget.style.display = 'none';
            } else {
                budgetRange.style.display = 'none';
                fixedBudget.style.display = 'block';
            }
        });
    }

    // Category selection
    document.querySelectorAll('.category-tag').forEach(tag => {
        tag.addEventListener('click', function() {
            this.classList.toggle('selected');
        });
    });

    // Post status filter
    const filterButtons = document.querySelectorAll('.filter-btn');
    const postsContainer = document.querySelector('.posts-grid');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            const status = this.dataset.status;
            
            // Filter posts
            if (postsContainer) {
                const posts = postsContainer.querySelectorAll('.post-card');
                posts.forEach(post => {
                    if (status === 'all' || post.dataset.status === status) {
                        post.style.display = 'block';
                    } else {
                        post.style.display = 'none';
                    }
                });
            }
        });
    });

    // Edit post functionality
    document.querySelectorAll('.edit-post').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const postId = this.dataset.postId;
            
            // Fetch post data and populate modal
            fetch(BASE_URL + 'client/get-post/' + postId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Populate form with data
                        populateEditForm(data.post);
                        // Show edit modal
                        postModal.style.display = 'block';
                        document.body.style.overflow = 'hidden';
                    }
                });
        });
    });

    // Delete post confirmation
    document.querySelectorAll('.delete-post').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const postId = this.dataset.postId;
            
            if (confirm('Are you sure you want to delete this post?')) {
                fetch(BASE_URL + 'client/delete-post/' + postId, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            }
        });
    });

    // Helper functions
    function showMessage(elementId, message, type) {
        const messageElement = document.getElementById(elementId);
        if (messageElement) {
            messageElement.textContent = message;
            messageElement.className = `message ${type}`;
            messageElement.style.display = 'block';
            
            setTimeout(() => {
                messageElement.style.display = 'none';
            }, 5000);
        }
    }

    function displayErrors(errors) {
        // Clear previous errors
        document.querySelectorAll('.error').forEach(el => el.textContent = '');
        
        for (const [field, message] of Object.entries(errors)) {
            const errorElement = document.getElementById(field + 'Error');
            if (errorElement) {
                errorElement.textContent = message;
            }
        }
    }

    function populateEditForm(post) {
        // Implementation for populating edit form
        // This would be specific to your form structure
    }
});