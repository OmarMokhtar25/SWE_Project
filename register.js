document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    const userTypes = document.querySelectorAll('.user-type');
    const accountTypeInput = document.getElementById('accountType');

    // Handle account type selection
    userTypes.forEach(type => {
        type.addEventListener('click', function() {
            userTypes.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            accountTypeInput.value = this.dataset.type;
        });
    });

    if (registerForm) {
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Clear previous errors
            clearErrors();

            const formData = new FormData(registerForm);

            try {
                const response = await fetch(window.location.origin + '/SWE_Project/public/auth/register', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showMessage('registerMessage', result.message, 'success');
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1000);
                } else {
                    if (result.errors) {
                        displayErrors(result.errors);
                    }
                }
            } catch (error) {
                showMessage('registerMessage', 'An error occurred. Please try again.', 'error');
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
                showMessage('registerMessage', message, 'error');
            } else {
                let errorElementId = field + 'Error';
                
                // Handle field name mappings
                if (field === 'first_name') errorElementId = 'firstNameError';
                if (field === 'last_name') errorElementId = 'lastNameError';
                if (field === 'phone_number') errorElementId = 'phoneNumberError';
                if (field === 'confirm_password') errorElementId = 'confirmPasswordError';
                if (field === 'account_type') errorElementId = 'accountTypeError';
                
                const errorElement = document.getElementById(errorElementId);
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