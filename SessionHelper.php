<?php

class SessionHelper {
    
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has($key) {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove($key) {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function destroy() {
        self::start();
        session_unset();
        session_destroy();
    }

    public static function setFlash($key, $message) {
        self::set('flash_' . $key, $message);
    }

    public static function getFlash($key) {
        self::start();
        $message = self::get('flash_' . $key);
        self::remove('flash_' . $key);
        return $message;
    }

    public static function isLoggedIn() {
        return self::has('user_id');
    }

    public static function getUserId() {
        return self::get('user_id');
    }

    public static function getUserType() {
        return self::get('user_type');
    }

    public static function getUserData() {
        return [
            'id' => self::get('user_id'),
            'username' => self::get('username'),
            'email' => self::get('email'),
            'first_name' => self::get('first_name'),
            'last_name' => self::get('last_name'),
            'account_type' => self::get('account_type')
        ];
    }
}