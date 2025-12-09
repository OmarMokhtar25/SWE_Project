<?php require_once dirname(__DIR__) . '/includes/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <!-- Profile Card -->
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-container mb-3">
                        <?php if (!empty($user['avatar'])): ?>
                            <img src="<?php echo $user['avatar']; ?>" class="rounded-circle avatar-lg" alt="Avatar">
                        <?php else: ?>
                            <div class="avatar-placeholder rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center">
                                <i class="fas fa-user fa-2x"></i>
                            </div>
                        <?php endif; ?>
                        <button class="btn btn-sm btn-outline-success mt-2" data-bs-toggle="modal" data-bs-target="#avatarModal">
                            <i class="fas fa-camera"></i> Change
                        </button>
                    </div>
                    <h4><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h4>
                    <p class="text-muted">Freelancer</p>
                    
                    <!-- Rating -->
                    <div class="rating mb-3">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star-half-alt text-warning"></i>
                        <span class="ms-2">4.5 (24 reviews)</span>
                    </div>
                    
                    <!-- Stats -->
                    <div class="row text-center mt-4">
                        <div class="col-4">
                            <h5 class="mb-0">42</h5>
                            <small class="text-muted">Jobs</small>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0">98%</h5>
                            <small class="text-muted">Success</small>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0">2.1k</h5>
                            <small class="text-muted">Earned</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Balance Card -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title">Account Balance</h6>
                    <h2 class="text-success mb-3">$1,500.00</h2>
                    <button class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                        <i class="fas fa-wallet"></i> Withdraw Funds
                    </button>
                    <button class="btn btn-outline-success w-100">
                        <i class="fas fa-history"></i> Transaction History
                    </button>
                </div>
            </div>

            <!-- Verification Status -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title">Verification Status</h6>
                    <ul class="list-unstyled">
                        <li class="d-flex justify-content-between align-items-center py-2">
                            <span>Email Verified</span>
                            <i class="fas fa-check-circle text-success"></i>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2">
                            <span>Phone Verified</span>
                            <i class="fas fa-check-circle text-success"></i>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2">
                            <span>ID Verification</span>
                            <i class="fas fa-clock text-warning"></i>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2">
                            <span>Payment Method</span>
                            <i class="fas fa-check-circle text-success"></i>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Edit Profile Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Profile Information</h5>
                </div>
                <div class="card-body">
                    <form id="profileForm">
                        <div class="message" id="profileMessage"></div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name *</label>
                                <input type="text" class="form-control" name="first_name" 
                                       value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name *</label>
                                <input type="text" class="form-control" name="last_name" 
                                       value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Professional Title</label>
                            <input type="text" class="form-control" name="title" placeholder="e.g., Senior Web Developer">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bio *</label>
                            <textarea class="form-control" name="bio" rows="4" required><?php echo htmlspecialchars($user['bio'] ?? 'I am a professional freelancer...'); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Hourly Rate ($/hour)</label>
                            <input type="number" class="form-control" name="hourly_rate" min="0" step="0.01" value="45.00">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Skills *</label>
                            <div class="skills-container mb-2" id="skillsContainer">
                                <?php if (!empty($skills)): ?>
                                    <?php foreach ($skills as $skill): ?>
                                        <span class="skill-tag">
                                            <?php echo htmlspecialchars($skill); ?>
                                            <span class="remove-skill">&times;</span>
                                        </span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <div class="input-group">
                                <input type="text" class="form-control" id="skillInput" placeholder="Add skill (press Enter)">
                                <button class="btn btn-outline-secondary" type="button" id="addSkillBtn">Add</button>
                            </div>
                            <input type="hidden" name="skills" id="skillsInput">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Portfolio Links</label>
                            <div id="portfolioLinks">
                                <div class="input-group mb-2">
                                    <input type="url" class="form-control" name="portfolio[]" placeholder="https://">
                                    <button class="btn btn-outline-danger" type="button" onclick="removePortfolioLink(this)">&times;</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addPortfolioLink()">
                                <i class="fas fa-plus"></i> Add Link
                            </button>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" name="location" placeholder="City, Country">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Availability</label>
                            <select class="form-control" name="availability">
                                <option value="available">Available Now</option>
                                <option value="part_time">Part Time</option>
                                <option value="busy">Busy</option>
                                <option value="unavailable">Not Available</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deactivateModal">
                                Deactivate Account
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Save Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Experience Section -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Work Experience</h5>
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#experienceModal">
                        <i class="fas fa-plus"></i> Add Experience
                    </button>
                </div>
                <div class="card-body">
                    <div class="experience-list">
                        <div class="experience-item mb-3">
                            <h6>Senior Web Developer</h6>
                            <p class="text-muted mb-1">Tech Solutions Inc. • 2020 - Present</p>
                            <p>Developed and maintained multiple web applications using modern technologies.</p>
                        </div>
                        <div class="experience-item mb-3">
                            <h6>Frontend Developer</h6>
                            <p class="text-muted mb-1">Creative Agency • 2018 - 2020</p>
                            <p>Created responsive websites and collaborated with design teams.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Education Section -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Education</h5>
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#educationModal">
                        <i class="fas fa-plus"></i> Add Education
                    </button>
                </div>
                <div class="card-body">
                    <div class="education-list">
                        <div class="education-item mb-3">
                            <h6>Bachelor of Computer Science</h6>
                            <p class="text-muted mb-1">University of Technology • 2014 - 2018</p>
                            <p>Graduated with honors</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Avatar Modal -->
<div class="modal fade" id="avatarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="avatarForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <input type="file" class="form-control" name="avatar" accept="image/*" required>
                    </div>
                    <div class="message" id="avatarMessage"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="avatarForm" class="btn btn-success">Upload</button>
            </div>
        </div>
    </div>
</div>

<!-- Withdraw Modal -->
<div class="modal fade" id="withdrawModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Withdraw Funds</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="withdrawForm">
                    <div class="mb-3">
                        <label class="form-label">Amount ($)</label>
                        <input type="number" class="form-control" name="amount" min="10" max="1500" step="0.01" required>
                        <small class="text-muted">Minimum withdrawal: $10.00</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select class="form-control" name="method" required>
                            <option value="">Select Method</option>
                            <option value="paypal">PayPal</option>
                            <option value="bank">Bank Transfer</option>
                            <option value="skrill">Skrill</option>
                        </select>
                    </div>
                    <div class="message" id="withdrawMessage"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="withdrawForm" class="btn btn-success">Request Withdrawal</button>
            </div>
        </div>
    </div>
</div>

<!-- Experience Modal -->
<div class="modal fade" id="experienceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Work Experience</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="experienceForm">
                    <div class="mb-3">
                        <label class="form-label">Job Title *</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Company *</label>
                        <input type="text" class="form-control" name="company" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Start Date *</label>
                            <input type="month" class="form-control" name="start_date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date</label>
                            <input type="month" class="form-control" name="end_date">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="current" id="currentJob">
                                <label class="form-check-label" for="currentJob">I currently work here</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="experienceForm" class="btn btn-success">Add Experience</button>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-lg {
    width: 150px;
    height: 150px;
    object-fit: cover;
}
.avatar-placeholder {
    width: 150px;
    height: 150px;
    font-size: 60px;
}
.skill-tag {
    display: inline-block;
    background: #e9ecef;
    padding: 5px 10px;
    margin: 2px;
    border-radius: 20px;
    font-size: 0.9em;
}
.skill-tag .remove-skill {
    margin-left: 5px;
    cursor: pointer;
    color: #dc3545;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Skills management
    const skillInput = document.getElementById('skillInput');
    const skillsContainer = document.getElementById('skillsContainer');
    const skillsInput = document.getElementById('skillsInput');
    const addSkillBtn = document.getElementById('addSkillBtn');
    
    function updateSkillsInput() {
        const skills = Array.from(skillsContainer.querySelectorAll('.skill-tag'))
            .map(tag => tag.textContent.replace('×', '').trim());
        skillsInput.value = JSON.stringify(skills);
    }
    
    function addSkill(skill) {
        const skillTag = document.createElement('span');
        skillTag.className = 'skill-tag';
        skillTag.innerHTML = `
            ${skill}
            <span class="remove-skill">&times;</span>
        `;
        
        skillTag.querySelector('.remove-skill').addEventListener('click', function() {
            skillTag.remove();
            updateSkillsInput();
        });
        
        skillsContainer.appendChild(skillTag);
        updateSkillsInput();
    }
    
    skillInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const skill = this.value.trim();
            if (skill) {
                addSkill(skill);
                this.value = '';
            }
        }
    });
    
    if (addSkillBtn) {
        addSkillBtn.addEventListener('click', function() {
            const skill = skillInput.value.trim();
            if (skill) {
                addSkill(skill);
                skillInput.value = '';
            }
        });
    }
    
    // Initialize existing skills
    skillsContainer.querySelectorAll('.remove-skill').forEach(removeBtn => {
        removeBtn.addEventListener('click', function() {
            this.parentElement.remove();
            updateSkillsInput();
        });
    });
    updateSkillsInput();
    
    // Profile form submission
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch(BASE_URL + 'profile/update', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage('profileMessage', 'Profile updated successfully!', 'success');
                } else {
                    showMessage('profileMessage', result.message, 'error');
                }
            } catch (error) {
                showMessage('profileMessage', 'An error occurred', 'error');
            }
        });
    }
    
    // Avatar form
    const avatarForm = document.getElementById('avatarForm');
    if (avatarForm) {
        avatarForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch(BASE_URL + 'profile/upload-avatar', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    location.reload();
                } else {
                    showMessage('avatarMessage', result.message, 'error');
                }
            } catch (error) {
                showMessage('avatarMessage', 'Upload failed', 'error');
            }
        });
    }
    
    // Withdraw form
    const withdrawForm = document.getElementById('withdrawForm');
    if (withdrawForm) {
        withdrawForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch(BASE_URL + 'freelancer/withdraw', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage('withdrawMessage', result.message, 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showMessage('withdrawMessage', result.message, 'error');
                }
            } catch (error) {
                showMessage('withdrawMessage', 'Withdrawal failed', 'error');
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

function addPortfolioLink() {
    const container = document.getElementById('portfolioLinks');
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
        <input type="url" class="form-control" name="portfolio[]" placeholder="https://">
        <button class="btn btn-outline-danger" type="button" onclick="removePortfolioLink(this)">&times;</button>
    `;
    container.appendChild(div);
}

function removePortfolioLink(button) {
    button.parentElement.remove();
}
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>