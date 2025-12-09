<?php

class AdminUser extends Model {
    
    public function getPendingUsers() {
        $sql = "SELECT * FROM users WHERE status = 'pending' AND account_type != 'admin' ORDER BY created_at DESC";
        return $this->select($sql);
    }

    public function approveUser($userId) {
        $sql = "UPDATE users SET status = 'active', updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        return $this->update($sql, [':id' => $userId]);
    }

    public function rejectUser($userId) {
        $sql = "UPDATE users SET status = 'rejected', updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        return $this->update($sql, [':id' => $userId]);
    }

    public function suspendUser($userId) {
        $sql = "UPDATE users SET status = 'suspended', updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        return $this->update($sql, [':id' => $userId]);
    }

    public function activateUser($userId) {
        $sql = "UPDATE users SET status = 'active', updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        return $this->update($sql, [':id' => $userId]);
    }

    public function getAllUsers($filters = []) {
        $sql = "SELECT id, first_name, last_name, username, email, phone_number, account_type, status, created_at 
                FROM users WHERE account_type != 'admin'";
        
        $params = [];
        
        if (!empty($filters['account_type'])) {
            $sql .= " AND account_type = :account_type";
            $params[':account_type'] = $filters['account_type'];
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (first_name LIKE :search OR last_name LIKE :search OR email LIKE :search OR username LIKE :search)";
            $params[':search'] = "%{$filters['search']}%";
        }
        
        $sql .= " ORDER BY created_at DESC";
        return $this->select($sql, $params);
    }

    public function getUserStats() {
        $sql = "SELECT 
                COUNT(*) as total_users,
                COUNT(CASE WHEN account_type = 'client' THEN 1 END) as total_clients,
                COUNT(CASE WHEN account_type = 'freelancer' THEN 1 END) as total_freelancers,
                COUNT(CASE WHEN status = 'active' THEN 1 END) as active_users,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_users,
                COUNT(CASE WHEN status = 'suspended' THEN 1 END) as suspended_users
                FROM users 
                WHERE account_type != 'admin'";
        return $this->selectOne($sql);
    }
}