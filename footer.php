        <?php if (SessionHelper::isLoggedIn() && $user['account_type'] === 'admin'): ?>
                </div> <!-- Close admin-content -->
            <?php endif; ?>
        </div> <!-- Close main-content -->
        
        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <h5><i class="fas fa-bolt"></i> Quicklance</h5>
                        <p>Your gateway to freelance opportunities and talented professionals.</p>
                    </div>
                    <div class="col-md-2">
                        <h6>For Freelancers</h6>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo BASE_URL; ?>wall">Find Jobs</a></li>
                            <li><a href="<?php echo BASE_URL; ?>freelancer/proposals">My Proposals</a></li>
                            <li><a href="<?php echo BASE_URL; ?>profile">My Profile</a></li>
                        </ul>
                    </div>
                    <div class="col-md-2">
                        <h6>For Clients</h6>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo BASE_URL; ?>client/post">Post a Job</a></li>
                            <li><a href="<?php echo BASE_URL; ?>client/view-posts">My Jobs</a></li>
                            <li><a href="<?php echo BASE_URL; ?>client/wall">Find Freelancers</a></li>
                        </ul>
                    </div>
                    <div class="col-md-2">
                        <h6>Company</h6>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo BASE_URL; ?>about">About Us</a></li>
                            <li><a href="<?php echo BASE_URL; ?>contact">Contact</a></li>
                            <li><a href="<?php echo BASE_URL; ?>faq">FAQ</a></li>
                        </ul>
                    </div>
                    <div class="col-md-2">
                        <h6>Legal</h6>
                        <ul class="list-unstyled">
                            <li><a href="<?php echo BASE_URL; ?>terms">Terms</a></li>
                            <li><a href="<?php echo BASE_URL; ?>privacy">Privacy</a></li>
                            <li><a href="<?php echo BASE_URL; ?>security">Security</a></li>
                        </ul>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <p>&copy; <?php echo date('Y'); ?> Quicklance. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </footer>
        
        <!-- Modals -->
        <?php if (SessionHelper::isLoggedIn()): ?>
            <!-- Apply Job Modal (for freelancers) -->
            <?php if ($user['account_type'] === 'freelancer'): ?>
                <div class="modal-overlay" id="applyModal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>Apply for Job</h3>
                            <button class="close-modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form id="applyForm">
                                <input type="hidden" id="applyPostId" name="job_id">
                                <div class="form-group">
                                    <label>Job Title</label>
                                    <div id="applyPostTitle" class="form-control-plaintext"></div>
                                </div>
                                <div class="form-group">
                                    <label>Cover Letter *</label>
                                    <textarea name="cover_letter" class="form-control" rows="5" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Bid Amount ($) *</label>
                                    <input type="number" name="bid_amount" class="form-control" step="0.01" required>
                                </div>
                                <div class="form-group">
                                    <label>Delivery Time (days) *</label>
                                    <input type="number" name="delivery_time" class="form-control" required>
                                </div>
                                <div class="message" id="applyMessage"></div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary close-modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Submit Proposal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Post Job Modal (for clients) -->
            <?php if ($user['account_type'] === 'client'): ?>
                <div class="modal-overlay" id="postModal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>Create New Job Post</h3>
                            <button class="close-modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form id="postForm">
                                <div class="form-group">
                                    <label>Job Title *</label>
                                    <input type="text" name="title" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Description *</label>
                                    <textarea name="description" class="form-control" rows="5" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Category *</label>
                                    <select name="category" class="form-control" required>
                                        <option value="">Select Category</option>
                                        <option value="web_development">Web Development</option>
                                        <option value="mobile_development">Mobile Development</option>
                                        <option value="design">Design</option>
                                        <option value="writing">Writing</option>
                                        <option value="marketing">Marketing</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Budget ($) *</label>
                                    <input type="number" name="budget" class="form-control" step="0.01" required>
                                </div>
                                <div class="form-group">
                                    <label>Deadline *</label>
                                    <input type="date" name="deadline" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Requirements</label>
                                    <div id="requirementsContainer">
                                        <!-- Requirements will be added here -->
                                    </div>
                                    <button type="button" id="addRequirementBtn" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-plus"></i> Add Requirement
                                    </button>
                                </div>
                                <div class="message" id="postMessage"></div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary close-modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Create Job Post</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <!-- Bootstrap JS -->
        <script src="<?php echo BASE_URL; ?>assets/bootstrap/js/bootstrap.bundle.min.js"></script>
        
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
        <!-- Custom JS -->
        <script src="<?php echo BASE_URL; ?>js/header.js"></script>
        <?php if (SessionHelper::isLoggedIn()): ?>
            <?php if ($user['account_type'] === 'client'): ?>
                <script src="<?php echo BASE_URL; ?>js/client.js"></script>
            <?php elseif ($user['account_type'] === 'freelancer'): ?>
                <script src="<?php echo BASE_URL; ?>js/freelancer.js"></script>
            <?php elseif ($user['account_type'] === 'admin'): ?>
                <script src="<?php echo BASE_URL; ?>js/admin.js"></script>
            <?php endif; ?>
        <?php endif; ?>
        
        <!-- Page-specific JS -->
        <?php if (isset($page_js)): ?>
            <script src="<?php echo BASE_URL; ?>js/<?php echo $page_js; ?>"></script>
        <?php endif; ?>
        
        <!-- Global JS variables -->
        <script>
            const BASE_URL = '<?php echo BASE_URL; ?>';
            const USER_ID = '<?php echo SessionHelper::getUserId() ?? 0; ?>';
            const USER_TYPE = '<?php echo SessionHelper::get("account_type") ?? ""; ?>';
        </script>
    </body>
</html>