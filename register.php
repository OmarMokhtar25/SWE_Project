<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

$csrf = '';
if (class_exists('SessionHelper')) {
	$csrf = SessionHelper::get('csrf_token') ?? '';
}
$csrf = $csrf ?: ($_SESSION['csrf_token'] ?? '');
?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/login.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<title>Register - Quicklance</title>
</head>
<body>
<div class="container" id="authContainer">
	<div class="header">
		<h1>Quicklance</h1>
		<p>Create your account</p>
	</div>

	<div class="form-container">
		<div class="tab-container">
			<div class="tab" data-tab="login" onclick="window.location.href='<?php echo BASE_URL; ?>auth/login'">Login</div>
			<div class="tab active" data-tab="register">Register</div>
		</div>

		<form id="registerForm" class="form active" method="POST" action="<?php echo BASE_URL; ?>auth/register">
			<!-- CSRF hidden token (required by server) -->
			<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">

			<div class="message" id="registerMessage"></div>

			<div class="form-row">
				<div class="form-group">
					<label for="firstName">First Name <span class="required">*</span></label>
					<input type="text" id="firstName" name="first_name" placeholder="First name" required>
					<span class="error" id="firstNameError"></span>
				</div>

				<div class="form-group">
					<label for="lastName">Last Name <span class="required">*</span></label>
					<input type="text" id="lastName" name="last_name" placeholder="Last name" required>
					<span class="error" id="lastNameError"></span>
				</div>
			</div>

			<div class="form-group">
				<label for="username">Username <span class="required">*</span></label>
				<input type="text" id="username" name="username" placeholder="Choose a unique username" required>
				<span class="error" id="usernameError"></span>
			</div>

			<div class="form-group">
				<label for="registerEmail">Email Address <span class="required">*</span></label>
				<input type="email" id="registerEmail" name="email" placeholder="Enter your email" required>
				<span class="error" id="emailError"></span>
			</div>

			<div class="form-group">
				<label for="phoneNumber">Phone Number</label>
				<input type="tel" id="phoneNumber" name="phone_number" placeholder="Enter phone number">
				<span class="error" id="phoneNumberError"></span>
			</div>

			<div class="form-row">
				<div class="form-group">
					<label for="registerPassword">Password <span class="required">*</span></label>
					<input type="password" id="registerPassword" name="password" placeholder="Create a password" required>
					<span class="error" id="passwordError"></span>
				</div>

				<div class="form-group">
					<label for="confirmPassword">Confirm Password <span class="required">*</span></label>
					<input type="password" id="confirmPassword" name="confirm_password" placeholder="Confirm your password" required>
					<span class="error" id="confirmPasswordError"></span>
				</div>
			</div>

			<div class="form-group">
				<label>Account Type <span class="required">*</span></label>
				<div class="user-type-container">
					<div class="user-type active" data-type="freelancer">
						<i class="fas fa-laptop-code"></i>
						<div>Freelancer</div>
						<small>Find work</small>
					</div>
					<div class="user-type" data-type="client">
						<i class="fas fa-briefcase"></i>
						<div>Client</div>
						<small>Hire talent</small>
					</div>
				</div>
				<input type="hidden" id="accountType" name="account_type" value="freelancer">
				<span class="error" id="accountTypeError"></span>
			</div>

			<div class="form-check mb-3">
				<input type="checkbox" class="form-check-input" id="terms" required>
				<label class="form-check-label" for="terms">
					I agree to the <a href="<?php echo BASE_URL; ?>terms" target="_blank">Terms of Service</a> and <a href="<?php echo BASE_URL; ?>privacy" target="_blank">Privacy Policy</a>
				</label>
			</div>

			<button type="submit" class="btn">Create Account</button>

			<div class="login-link">
				Already have an account? <a href="<?php echo BASE_URL; ?>auth/login">Login here</a>
			</div>
		</form>
	</div>
</div>


<script src="<?php echo BASE_URL; ?>js/register.js"></script>
</body>
</html>