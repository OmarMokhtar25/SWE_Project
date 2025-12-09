<?php require_once dirname(__DIR__) . '/includes/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Proposals</h2>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-primary <?php echo $current_status === 'all' ? 'active' : ''; ?>" 
                    onclick="filterProposals('all')">All</button>
            <button type="button" class="btn btn-outline-primary <?php echo $current_status === 'pending' ? 'active' : ''; ?>" 
                    onclick="filterProposals('pending')">Pending</button>
            <button type="button" class="btn btn-outline-primary <?php echo $current_status === 'accepted' ? 'active' : ''; ?>" 
                    onclick="filterProposals('accepted')">Accepted</button>
            <button type="button" class="btn btn-outline-primary <?php echo $current_status === 'rejected' ? 'active' : ''; ?>" 
                    onclick="filterProposals('rejected')">Rejected</button>
            <button type="button" class="btn btn-outline-primary <?php echo $current_status === 'completed' ? 'active' : ''; ?>" 
                    onclick="filterProposals('completed')">Completed</button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body py-3">
                    <h6 class="mb-0">Total</h6>
                    <h3 class="mb-0"><?php echo count($proposals); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body py-3">
                    <h6 class="mb-0">Accepted</h6>
                    <h3 class="mb-0"><?php echo count(array_filter($proposals, fn($p) => $p['status'] === 'accepted')); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body py-3">
                    <h6 class="mb-0">Pending</h6>
                    <h3 class="mb-0"><?php echo count(array_filter($proposals, fn($p) => $p['status'] === 'pending')); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body py-3">
                    <h6 class="mb-0">Rejected</h6>
                    <h3 class="mb-0"><?php echo count(array_filter($proposals, fn($p) => $p['status'] === 'rejected')); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($proposals)): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Client</th>
                        <th>Bid Amount</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($proposals as $proposal): ?>
                        <tr data-status="<?php echo $proposal['status']; ?>">
                            <td>
                                <strong><?php echo htmlspecialchars($proposal['job_title']); ?></strong>
                                <br>
                                <small class="text-muted"><?php echo substr(htmlspecialchars($proposal['cover_letter']), 0, 50); ?>...</small>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($proposal['client_first_name'] . ' ' . $proposal['client_last_name']); ?>
                            </td>
                            <td>
                                <strong class="text-success">$<?php echo number_format($proposal['bid_amount'], 2); ?></strong>
                                <br>
                                <small class="text-muted"><?php echo $proposal['delivery_time']; ?> days</small>
                            </td>
                            <td>
                                <?php echo date('M d, Y', strtotime($proposal['created_at'])); ?>
                                <br>
                                <small class="text-muted"><?php echo date('h:i A', strtotime($proposal['created_at'])); ?></small>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo getStatusBadgeColor($proposal['status']); ?>">
                                    <?php echo ucfirst($proposal['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary view-proposal" 
                                            data-id="<?php echo $proposal['id']; ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if ($proposal['status'] === 'pending'): ?>
                                        <button type="button" class="btn btn-outline-warning edit-proposal" 
                                                data-id="<?php echo $proposal['id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger withdraw-proposal" 
                                                data-id="<?php echo $proposal['id']; ?>">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($proposal['status'] === 'accepted'): ?>
                                        <button type="button" class="btn btn-outline-success start-work" 
                                                data-id="<?php echo $proposal['id']; ?>">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-paper-plane fa-4x text-muted mb-4"></i>
            <h4>No proposals yet</h4>
            <p class="text-muted">You haven't submitted any proposals.</p>
            <a href="<?php echo BASE_URL; ?>freelancer/wall" class="btn btn-primary">
                <i class="fas fa-search"></i> Find Jobs
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Proposal Details Modal -->
<div class="modal fade" id="proposalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Proposal Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="proposalDetails">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Edit Proposal Modal -->
<div class="modal fade" id="editProposalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Proposal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editProposalForm">
                    <input type="hidden" id="editProposalId" name="proposal_id">
                    <div class="mb-3">
                        <label class="form-label">Cover Letter *</label>
                        <textarea class="form-control" id="editCoverLetter" name="cover_letter" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bid Amount ($) *</label>
                        <input type="number" class="form-control" id="editBidAmount" name="bid_amount" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Delivery Time (days) *</label>
                        <input type="number" class="form-control" id="editDeliveryTime" name="delivery_time" required>
                    </div>
                    <div class="message" id="editProposalMessage"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editProposalForm" class="btn btn-primary">Update Proposal</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const proposalModal = new bootstrap.Modal(document.getElementById('proposalModal'));
    const editProposalModal = new bootstrap.Modal(document.getElementById('editProposalModal'));
    
    // View proposal details
    document.querySelectorAll('.view-proposal').forEach(button => {
        button.addEventListener('click', function() {
            const proposalId = this.dataset.id;
            loadProposalDetails(proposalId);
        });
    });
    
    // Edit proposal
    document.querySelectorAll('.edit-proposal').forEach(button => {
        button.addEventListener('click', function() {
            const proposalId = this.dataset.id;
            loadProposalForEdit(proposalId);
        });
    });
    
    // Withdraw proposal
    document.querySelectorAll('.withdraw-proposal').forEach(button => {
        button.addEventListener('click', function() {
            const proposalId = this.dataset.id;
            
            if (confirm('Are you sure you want to withdraw this proposal?')) {
                fetch(BASE_URL + 'proposal/withdraw/' + proposalId, {
                    method: 'POST',
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
    
    // Edit proposal form submission
    const editProposalForm = document.getElementById('editProposalForm');
    if (editProposalForm) {
        editProposalForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch(BASE_URL + 'proposal/update', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    location.reload();
                } else {
                    showMessage('editProposalMessage', result.message, 'error');
                }
            } catch (error) {
                showMessage('editProposalMessage', 'Update failed', 'error');
            }
        });
    }
    
    function loadProposalDetails(proposalId) {
        fetch(BASE_URL + 'proposal/details/' + proposalId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const proposal = data.proposal;
                    const freelancer = data.freelancer;
                    const job = data.job;
                    
                    const html = `
                        <div class="proposal-details">
                            <h6>Job: ${job.title}</h6>
                            <p><strong>Client:</strong> ${job.first_name} ${job.last_name}</p>
                            
                            <hr>
                            
                            <h6>Cover Letter</h6>
                            <p>${proposal.cover_letter}</p>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Bid Details</h6>
                                    <p><strong>Amount:</strong> $${proposal.bid_amount}</p>
                                    <p><strong>Delivery Time:</strong> ${proposal.delivery_time} days</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Timeline</h6>
                                    <p><strong>Submitted:</strong> ${new Date(proposal.created_at).toLocaleDateString()}</p>
                                    <p><strong>Last Updated:</strong> ${new Date(proposal.updated_at).toLocaleDateString()}</p>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <h6>Job Details</h6>
                            <p>${job.description}</p>
                            <p><strong>Budget:</strong> $${job.fixed_budget || job.max_budget}</p>
                            <p><strong>Deadline:</strong> ${job.deadline}</p>
                        </div>
                    `;
                    
                    document.getElementById('proposalDetails').innerHTML = html;
                    proposalModal.show();
                }
            });
    }
    
    function loadProposalForEdit(proposalId) {
        fetch(BASE_URL + 'proposal/get/' + proposalId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('editProposalId').value = proposalId;
                    document.getElementById('editCoverLetter').value = data.proposal.cover_letter;
                    document.getElementById('editBidAmount').value = data.proposal.bid_amount;
                    document.getElementById('editDeliveryTime').value = data.proposal.delivery_time;
                    editProposalModal.show();
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

function filterProposals(status) {
    window.location.href = BASE_URL + 'freelancer/proposals?status=' + status;
}

<?php
function getStatusBadgeColor($status) {
    switch($status) {
        case 'accepted': return 'success';
        case 'pending': return 'warning';
        case 'rejected': return 'danger';
        case 'completed': return 'info';
        default: return 'secondary';
    }
}
?>
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>