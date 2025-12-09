// Admin JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle for mobile
    const sidebarToggle = document.getElementById('sidebarToggle');
    const adminSidebar = document.getElementById('adminSidebar');
    
    if (sidebarToggle && adminSidebar) {
        sidebarToggle.addEventListener('click', function() {
            adminSidebar.classList.toggle('active');
        });
    }

    // Initialize charts
    initializeCharts();

    // Approve/Reject actions
    document.querySelectorAll('.btn-approve').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const type = this.dataset.type; // 'user' or 'post'
            
            if (confirm('Are you sure you want to approve this ' + type + '?')) {
                processApproval(id, type, 'approve');
            }
        });
    });

    document.querySelectorAll('.btn-reject').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const type = this.dataset.type;
            
            if (confirm('Are you sure you want to reject this ' + type + '?')) {
                processApproval(id, type, 'reject');
            }
        });
    });

    // Delete actions
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const type = this.dataset.type;
            
            if (confirm('Are you sure you want to delete this ' + type + '? This action cannot be undone.')) {
                deleteItem(id, type);
            }
        });
    });

    // View details modal
    document.querySelectorAll('.btn-view').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const type = this.dataset.type;
            
            showDetailsModal(id, type);
        });
    });

    // Edit modal
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const type = this.dataset.type;
            
            showEditModal(id, type);
        });
    });

    // Close modal
    document.querySelectorAll('.close-modal').forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal-overlay');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    });

    // Close modal when clicking outside
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    });

    // Search functionality
    const searchInput = document.querySelector('.admin-search');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function() {
            const query = this.value.trim();
            const table = this.closest('.widget').querySelector('table');
            
            if (table && query) {
                filterTable(table, query);
            }
        }, 300));
    }

    // Export data
    const exportButtons = document.querySelectorAll('.export-btn');
    exportButtons.forEach(button => {
        button.addEventListener('click', function() {
            const type = this.dataset.type;
            const format = this.dataset.format || 'csv';
            
            exportData(type, format);
        });
    });

    // Statistics refresh
    const refreshStatsBtn = document.getElementById('refreshStats');
    if (refreshStatsBtn) {
        refreshStatsBtn.addEventListener('click', function() {
            refreshStatistics();
        });
    }

    // Bulk actions
    const bulkSelectAll = document.getElementById('bulkSelectAll');
    const bulkActionSelect = document.getElementById('bulkActionSelect');
    const bulkActionBtn = document.getElementById('bulkActionBtn');
    
    if (bulkSelectAll) {
        bulkSelectAll.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.bulk-select');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
    
    if (bulkActionBtn && bulkActionSelect) {
        bulkActionBtn.addEventListener('click', function() {
            const action = bulkActionSelect.value;
            const selectedIds = getSelectedIds();
            
            if (selectedIds.length === 0) {
                alert('Please select items to perform bulk action.');
                return;
            }
            
            if (confirm(`Are you sure you want to ${action} ${selectedIds.length} items?`)) {
                performBulkAction(action, selectedIds);
            }
        });
    }

    // Helper functions
    function processApproval(id, type, action) {
        fetch(BASE_URL + 'admin/' + action + '-' + type, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id })
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

    function deleteItem(id, type) {
        fetch(BASE_URL + 'admin/delete-' + type, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id })
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

    function showDetailsModal(id, type) {
        fetch(BASE_URL + 'admin/get-' + type + '-details/' + id)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const modal = document.getElementById('detailsModal');
                    const modalBody = modal.querySelector('.modal-body');
                    
                    modalBody.innerHTML = formatDetails(data.details, type);
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }
            });
    }

    function showEditModal(id, type) {
        fetch(BASE_URL + 'admin/get-' + type + '/' + id)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const modal = document.getElementById('editModal');
                    const modalBody = modal.querySelector('.modal-body');
                    
                    modalBody.innerHTML = formatEditForm(data.data, type);
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                    
                    // Add form submission handler
                    const editForm = document.getElementById('editForm');
                    if (editForm) {
                        editForm.addEventListener('submit', function(e) {
                            e.preventDefault();
                            submitEditForm(id, type);
                        });
                    }
                }
            });
    }

    function filterTable(table, query) {
        const rows = table.querySelectorAll('tbody tr');
        const lowerQuery = query.toLowerCase();
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(lowerQuery) ? '' : 'none';
        });
    }

    function exportData(type, format) {
        window.location.href = BASE_URL + 'admin/export-' + type + '?format=' + format;
    }

    function refreshStatistics() {
        fetch(BASE_URL + 'admin/refresh-stats')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateStatsUI(data.stats);
                }
            });
    }

    function getSelectedIds() {
        const checkboxes = document.querySelectorAll('.bulk-select:checked');
        return Array.from(checkboxes).map(checkbox => checkbox.value);
    }

    function performBulkAction(action, ids) {
        fetch(BASE_URL + 'admin/bulk-action', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: action,
                ids: ids
            })
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

    function initializeCharts() {
        // Revenue chart
        const revenueCtx = document.getElementById('revenueChart');
        if (revenueCtx) {
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Revenue',
                        data: [12000, 19000, 15000, 25000, 22000, 30000],
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        borderWidth: 2,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        // User growth chart
        const usersCtx = document.getElementById('usersChart');
        if (usersCtx) {
            new Chart(usersCtx, {
                type: 'bar',
                data: {
                    labels: ['Freelancers', 'Clients', 'Admins'],
                    datasets: [{
                        label: 'Users',
                        data: [150, 80, 5],
                        backgroundColor: [
                            '#ffc107',
                            '#28a745',
                            '#667eea'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
    }

    function updateStatsUI(stats) {
        // Update all stat cards
        Object.keys(stats).forEach(stat => {
            const element = document.getElementById(stat + 'Stat');
            if (element) {
                element.textContent = stats[stat];
            }
        });
    }

    function formatDetails(details, type) {
        // Format details based on type
        let html = '<div class="details-view">';
        
        if (type === 'user') {
            html += `
                <div class="detail-item">
                    <strong>Name:</strong> ${details.first_name} ${details.last_name}
                </div>
                <div class="detail-item">
                    <strong>Email:</strong> ${details.email}
                </div>
                <div class="detail-item">
                    <strong>Username:</strong> ${details.username}
                </div>
                <div class="detail-item">
                    <strong>Account Type:</strong> ${details.account_type}
                </div>
                <div class="detail-item">
                    <strong>Joined:</strong> ${details.created_at}
                </div>
            `;
        } else if (type === 'post') {
            html += `
                <div class="detail-item">
                    <strong>Title:</strong> ${details.title}
                </div>
                <div class="detail-item">
                    <strong>Client:</strong> ${details.client_name}
                </div>
                <div class="detail-item">
                    <strong>Budget:</strong> $${details.budget}
                </div>
                <div class="detail-item">
                    <strong>Status:</strong> ${details.status}
                </div>
                <div class="detail-item">
                    <strong>Posted:</strong> ${details.created_at}
                </div>
                <div class="detail-item">
                    <strong>Description:</strong>
                    <p>${details.description}</p>
                </div>
            `;
        }
        
        html += '</div>';
        return html;
    }

    function formatEditForm(data, type) {
        // Format edit form based on type
        let html = `<form id="editForm">`;
        
        if (type === 'user') {
            html += `
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" value="${data.first_name}" class="form-control">
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="${data.last_name}" class="form-control">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="${data.email}" class="form-control">
                </div>
                <div class="form-group">
                    <label>Account Type</label>
                    <select name="account_type" class="form-control">
                        <option value="freelancer" ${data.account_type === 'freelancer' ? 'selected' : ''}>Freelancer</option>
                        <option value="client" ${data.account_type === 'client' ? 'selected' : ''}>Client</option>
                        <option value="admin" ${data.account_type === 'admin' ? 'selected' : ''}>Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="active" ${data.status === 'active' ? 'selected' : ''}>Active</option>
                        <option value="inactive" ${data.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                        <option value="suspended" ${data.status === 'suspended' ? 'selected' : ''}>Suspended</option>
                    </select>
                </div>
            `;
        }
        
        html += `</form>`;
        return html;
    }

    function submitEditForm(id, type) {
        const form = document.getElementById('editForm');
        const formData = new FormData(form);
        
        fetch(BASE_URL + 'admin/update-' + type + '/' + id, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Update successful!');
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }

    // Debounce function for search
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
});