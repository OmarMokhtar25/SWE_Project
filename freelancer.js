// Freelancer JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Filter proposals
    const filterButtons = document.querySelectorAll('.filter-btn');
    const proposalsContainer = document.querySelector('.proposals-list');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            const status = this.dataset.status;
            
            // Filter proposals
            if (proposalsContainer) {
                const proposals = proposalsContainer.querySelectorAll('.proposal-item');
                proposals.forEach(proposal => {
                    if (status === 'all' || proposal.dataset.status === status) {
                        proposal.style.display = 'flex';
                    } else {
                        proposal.style.display = 'none';
                    }
                });
            }
        });
    });

    // Apply for job
    const applyButtons = document.querySelectorAll('.apply-job');
    const applyModal = document.getElementById('applyModal');
    
    applyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const postTitle = this.dataset.postTitle;
            
            // Set post info in modal
            document.getElementById('applyPostId').value = postId;
            document.getElementById('applyPostTitle').textContent = postTitle;
            
            // Show modal
            if (applyModal) {
                applyModal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }
        });
    });

    // Close modal
    const closeModal = document.querySelector('.close-modal');
    if (closeModal && applyModal) {
        closeModal.addEventListener('click', function() {
            applyModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            if (e.target === applyModal) {
                applyModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    }

    // Submit application
    const applyForm = document.getElementById('applyForm');
    if (applyForm) {
        applyForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            try {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Submitting...';
                
                const response = await fetch(BASE_URL + 'freelancer/submit-proposal', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage('applyMessage', result.message, 'success');
                    setTimeout(() => {
                        applyModal.style.display = 'none';
                        document.body.style.overflow = 'auto';
                        location.reload();
                    }, 1500);
                } else {
                    showMessage('applyMessage', result.message, 'error');
                    if (result.errors) {
                        displayErrors(result.errors);
                    }
                }
            } catch (error) {
                showMessage('applyMessage', 'An error occurred. Please try again.', 'error');
                console.error('Error:', error);
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }

    // Withdraw balance
    const withdrawBtn = document.getElementById('withdrawBtn');
    if (withdrawBtn) {
        withdrawBtn.addEventListener('click', function() {
            const amount = prompt('Enter withdrawal amount:');
            
            if (amount && !isNaN(amount) && amount > 0) {
                fetch(BASE_URL + 'freelancer/withdraw', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ amount: amount })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
            }
        });
    }

    // Save post
    const saveButtons = document.querySelectorAll('.save-post');
    saveButtons.forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const isSaved = this.classList.contains('saved');
            
            fetch(BASE_URL + 'freelancer/save-post', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    post_id: postId,
                    action: isSaved ? 'unsave' : 'save'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (isSaved) {
                        this.classList.remove('saved');
                        this.innerHTML = '<i class="far fa-bookmark"></i> Save';
                    } else {
                        this.classList.add('saved');
                        this.innerHTML = '<i class="fas fa-bookmark"></i> Saved';
                    }
                }
            });
        });
    });

    // Load more proposals
    let currentPage = 1;
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            currentPage++;
            const status = document.querySelector('.filter-btn.active').dataset.status;
            
            fetch(BASE_URL + 'freelancer/load-proposals?page=' + currentPage + '&status=' + status)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.proposals.length > 0) {
                            appendProposals(data.proposals);
                        } else {
                            loadMoreBtn.style.display = 'none';
                        }
                    }
                });
        });
    }

    // Update profile skills
    const skillsInput = document.getElementById('skillsInput');
    const skillsContainer = document.getElementById('skillsContainer');
    
    if (skillsInput) {
        skillsInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const skill = this.value.trim();
                
                if (skill) {
                    addSkillTag(skill);
                    this.value = '';
                    
                    // Update hidden input
                    const skills = Array.from(skillsContainer.querySelectorAll('.skill-tag'))
                        .map(tag => tag.dataset.skill);
                    document.getElementById('userSkills').value = JSON.stringify(skills);
                }
            }
        });
    }

    // Portfolio upload
    const portfolioUpload = document.getElementById('portfolioUpload');
    if (portfolioUpload) {
        portfolioUpload.addEventListener('change', function(e) {
            const files = e.target.files;
            const portfolioGrid = document.querySelector('.portfolio-grid');
            
            for (let file of files) {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const portfolioItem = document.createElement('div');
                        portfolioItem.className = 'portfolio-item';
                        portfolioItem.innerHTML = `
                            <img src="${e.target.result}" class="portfolio-img" alt="Portfolio">
                            <div class="portfolio-content">
                                <button type="button" class="remove-portfolio">Remove</button>
                            </div>
                        `;
                        portfolioGrid.appendChild(portfolioItem);
                        
                        // Add remove functionality
                        portfolioItem.querySelector('.remove-portfolio').addEventListener('click', function() {
                            portfolioItem.remove();
                        });
                    };
                    reader.readAsDataURL(file);
                }
            }
        });
    }

    // Helper functions
    function appendProposals(proposals) {
        const proposalsContainer = document.querySelector('.proposals-list');
        
        proposals.forEach(proposal => {
            const proposalItem = document.createElement('div');
            proposalItem.className = 'proposal-item';
            proposalItem.dataset.status = proposal.status;
            proposalItem.innerHTML = `
                <div class="proposal-info">
                    <h4>${proposal.post_title}</h4>
                    <div class="proposal-client">Client: ${proposal.client_name}</div>
                    <div class="proposal-date">Applied: ${proposal.created_at}</div>
                </div>
                <span class="proposal-status status-${proposal.status}">${proposal.status}</span>
            `;
            proposalsContainer.appendChild(proposalItem);
        });
    }

    function addSkillTag(skill) {
        const skillTag = document.createElement('span');
        skillTag.className = 'skill-tag';
        skillTag.dataset.skill = skill;
        skillTag.innerHTML = `
            ${skill}
            <span class="remove-skill">×</span>
        `;
        
        skillTag.querySelector('.remove-skill').addEventListener('click', function() {
            skillTag.remove();
        });
        
        skillsContainer.appendChild(skillTag);
    }

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
});