<?php

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'Model.php';

class User extends Model {
    
    public function create($data) {
        $sql = "INSERT INTO users (first_name, last_name, username, email, phone_number, password, account_type) 
                VALUES (:first_name, :last_name, :username, :email, :phone_number, :password, :account_type)";
        
        return $this->insert($sql, [
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':username' => $data['username'],
            ':email' => $data['email'],
            ':phone_number' => $data['phone_number'],
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':account_type' => $data['account_type']
        ]);
    }

    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        return $this->selectOne($sql, [':email' => $email]);
    }

    public function findByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = :username LIMIT 1";
        return $this->selectOne($sql, [':username' => $username]);
    }

    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        return $this->selectOne($sql, [':id' => $id]);
    }

    public function emailExists($email) {
        return $this->findByEmail($email) !== null;
    }

    public function usernameExists($username) {
        return $this->findByUsername($username) !== null;
    }

    public function verifyPassword($inputPassword, $hashedPassword) {
        return password_verify($inputPassword, $hashedPassword);
    }

    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);
        
        if ($user && $this->verifyPassword($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }

    public function updateLastLogin($userId) {
        $sql = "UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        return $this->update($sql, [':id' => $userId]);
    }

    public function getAllUsers() {
        $sql = "SELECT id, first_name, last_name, username, email, phone_number, account_type, created_at 
                FROM users ORDER BY created_at DESC";
        return $this->select($sql);
    }
}