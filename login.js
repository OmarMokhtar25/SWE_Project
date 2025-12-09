document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');

    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Clear previous errors
            clearErrors();

            const formData = new FormData(loginForm);

            try {
                const response = await fetch(window.location.origin + '/SWE_Project/public/auth/login', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showMessage('loginMessage', result.message, 'success');
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1000);
                } else {
                    if (result.errors) {
                        displayErrors(result.errors);
                    }
                }
            } catch (error) {
                showMessage('loginMessage', 'An error occurred. Please try again.', 'error');
                console.error('Error:', error);
            }
        });
    }

    function clearErrors() {
        const errorElements = document.querySelectorAll('.error');
        errorElements.forEach(el => el.textContent = '');
        
        const messageElements = document.querySelectorAll('.message');
        messageElements.forEach(el => {
            el.textContent = '';
            el.className = 'message';
        });
    }

    function displayErrors(errors) {
        for (const [field, message] of Object.entries(errors)) {
            if (field === 'general') {
                showMessage('loginMessage', message, 'error');
            } else {
                const errorElement = document.getElementById(field + 'Error');
                if (errorElement) {
                    errorElement.textContent = message;
                }
            }
        }
    }

    function showMessage(elementId, message, type) {
        const messageElement = document.getElementById(elementId);
        if (messageElement) {
            messageElement.textContent = message;
            messageElement.className = `message ${type}`;
        }
    }
});