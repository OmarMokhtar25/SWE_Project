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
	<title>Login - Quicklance</title>
</head>
<body>
<div class="container" id="authContainer">
	<div class="header">
		<h1>Quicklance</h1>
		<p>Your gateway to freelance opportunities</p>
	</div>

	<div class="form-container">
		<div class="tab-container">
			<div class="tab active" data-tab="login">Login</div>
			<div class="tab" data-tab="register" onclick="window.location.href='<?php echo BASE_URL; ?>auth/register'">Register</div>
		</div>

		<form id="loginForm" class="form active" method="POST" action="<?php echo BASE_URL; ?>auth/login">
			<!-- CSRF hidden token (required by server) -->
			<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">

			<div class="message" id="loginMessage"></div>

			<div class="form-group">
				<label for="loginEmail">Email Address <span class="required">*</span></label>
				<input type="email" id="loginEmail" name="email" placeholder="Enter your email" required>
				<span class="error" id="emailError"></span>
			</div>

			<div class="form-group">
				<label for="loginPassword">Password <span class="required">*</span></label>
				<input type="password" id="loginPassword" name="password" placeholder="Enter your password" required>
				<span class="error" id="passwordError"></span>
			</div>

			<button type="submit" class="btn">Login to Quicklance</button>

			<div class="forgot-password">
				<a href="#" id="forgotPassword">Forgot your password?</a>
			</div>

			<div class="register-link">
				Don't have an account? <a href="<?php echo BASE_URL; ?>auth/register">Register here</a>
			</div>
		</form>
	</div>
</div>


<script src="<?php echo BASE_URL; ?>js/login.js"></script>
</body>
</html>