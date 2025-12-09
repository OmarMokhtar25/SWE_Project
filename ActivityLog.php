<?php

class ActivityLog extends Model {
    
    public function log($userId, $action, $details = null, $ipAddress = null) {
        $sql = "INSERT INTO activity_logs (user_id, action, details, ip_address) 
                VALUES (:user_id, :action, :details, :ip_address)";
        
        return $this->insert($sql, [
            ':user_id' => $userId,
            ':action' => $action,
            ':details' => json_encode($details),
            ':ip_address' => $ipAddress ?? $_SERVER['REMOTE_ADDR']
        ]);
    }

    public function getRecentActivities($limit = 50) {
        $sql = "SELECT al.*, u.first_name, u.last_name, u.account_type 
                FROM activity_logs al 
                JOIN users u ON al.user_id = u.id 
                ORDER BY al.created_at DESC 
                LIMIT :limit";
        return $this->select($sql, [':limit' => $limit]);
    }

    public function getUserActivities($userId, $limit = 20) {
        $sql = "SELECT * FROM activity_logs 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC 
                LIMIT :limit";
        return $this->select($sql, [':user_id' => $userId, ':limit' => $limit]);
    }

    public function getStats() {
        $sql = "SELECT 
                DATE(created_at) as date,
                COUNT(*) as count
                FROM activity_logs 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY DATE(created_at)
                ORDER BY date DESC";
        return $this->select($sql);
    }
}