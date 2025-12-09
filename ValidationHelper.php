<?php

class ValidationHelper {
    
    public static function sanitize($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    public static function validateName($name) {
        $name = self::sanitize($name);
        if (empty($name)) {
            return ['valid' => false, 'error' => 'Name is required'];
        }
        if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
            return ['valid' => false, 'error' => 'Only letters and white space allowed'];
        }
        return ['valid' => true, 'value' => $name];
    }

    public static function validateEmail($email) {
        $email = self::sanitize($email);
        if (empty($email)) {
            return ['valid' => false, 'error' => 'Email is required'];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'error' => 'Invalid email format'];
        }
        return ['valid' => true, 'value' => $email];
    }

    public static function validatePassword($password) {
        if (empty($password)) {
            return ['valid' => false, 'error' => 'Password is required'];
        }
        if (strlen($password) < 6) {
            return ['valid' => false, 'error' => 'Password must be at least 6 characters'];
        }
        return ['valid' => true, 'value' => $password];
    }

    public static function validatePhone($phone) {
        $phone = self::sanitize($phone);
        if (empty($phone)) {
            return ['valid' => false, 'error' => 'Phone number is required'];
        }
        if (!preg_match("/^[0-9+\-\s()]*$/", $phone)) {
            return ['valid' => false, 'error' => 'Invalid phone number format'];
        }
        return ['valid' => true, 'value' => $phone];
    }

    public static function validateUsername($username) {
        $username = self::sanitize($username);
        if (empty($username)) {
            return ['valid' => false, 'error' => 'Username is required'];
        }
        if (strlen($username) < 3) {
            return ['valid' => false, 'error' => 'Username must be at least 3 characters'];
        }
        if (!preg_match("/^[a-zA-Z0-9_]*$/", $username)) {
            return ['valid' => false, 'error' => 'Username can only contain letters, numbers and underscore'];
        }
        return ['valid' => true, 'value' => $username];
    }

    public static function validatePasswordMatch($password, $confirmPassword) {
        if ($password !== $confirmPassword) {
            return ['valid' => false, 'error' => 'Passwords do not match'];
        }
        return ['valid' => true];
    }

    public static function validateAccountType($type) {
        $allowedTypes = ['freelancer', 'client'];
        if (!in_array($type, $allowedTypes)) {
            return ['valid' => false, 'error' => 'Invalid account type'];
        }
        return ['valid' => true, 'value' => $type];
    }
}