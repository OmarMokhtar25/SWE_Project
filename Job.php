<?php

class Job extends Model {
    
    public function create($data) {
        $sql = "INSERT INTO jobs (client_id, title, description, category, budget_type, min_budget, max_budget, 
                fixed_budget, deadline, requirements, status, admin_status) 
                VALUES (:client_id, :title, :description, :category, :budget_type, :min_budget, :max_budget, 
                :fixed_budget, :deadline, :requirements, :status, :admin_status)";
        
        return $this->insert($sql, [
            ':client_id' => $data['client_id'],
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':category' => $data['category'],
            ':budget_type' => $data['budget_type'],
            ':min_budget' => $data['min_budget'],
            ':max_budget' => $data['max_budget'],
            ':fixed_budget' => $data['fixed_budget'],
            ':deadline' => $data['deadline'],
            ':requirements' => json_encode($data['requirements']),
            ':status' => $data['status'],
            ':admin_status' => 'pending'
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE jobs SET title = :title, description = :description, category = :category, 
                budget_type = :budget_type, min_budget = :min_budget, max_budget = :max_budget, 
                fixed_budget = :fixed_budget, deadline = :deadline, requirements = :requirements, 
                status = :status, updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id";
        
        return $this->update($sql, [
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':category' => $data['category'],
            ':budget_type' => $data['budget_type'],
            ':min_budget' => $data['min_budget'],
            ':max_budget' => $data['max_budget'],
            ':fixed_budget' => $data['fixed_budget'],
            ':deadline' => $data['deadline'],
            ':requirements' => json_encode($data['requirements']),
            ':status' => $data['status'],
            ':id' => $id
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM jobs WHERE id = :id";
        return $this->delete($sql, [':id' => $id]);
    }

    public function findById($id) {
        $sql = "SELECT j.*, u.first_name, u.last_name, u.email as client_email 
                FROM jobs j 
                JOIN users u ON j.client_id = u.id 
                WHERE j.id = :id";
        return $this->selectOne($sql, [':id' => $id]);
    }

    public function findByClient($clientId, $status = null) {
        $sql = "SELECT * FROM jobs WHERE client_id = :client_id";
        
        if ($status) {
            $sql .= " AND status = :status";
            $params = [':client_id' => $clientId, ':status' => $status];
        } else {
            $params = [':client_id' => $clientId];
        }
        
        $sql .= " ORDER BY created_at DESC";
        return $this->select($sql, $params);
    }

    public function getAllActive() {
        $sql = "SELECT j.*, u.first_name, u.last_name 
                FROM jobs j 
                JOIN users u ON j.client_id = u.id 
                WHERE j.admin_status = 'approved' 
                AND j.status = 'active' 
                ORDER BY j.created_at DESC";
        return $this->select($sql);
    }

    public function getPendingJobs() {
        $sql = "SELECT j.*, u.first_name, u.last_name, u.email 
                FROM jobs j 
                JOIN users u ON j.client_id = u.id 
                WHERE j.admin_status = 'pending' 
                ORDER BY j.created_at DESC";
        return $this->select($sql);
    }

    public function approveJob($id) {
        $sql = "UPDATE jobs SET admin_status = 'approved', updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        return $this->update($sql, [':id' => $id]);
    }

    public function rejectJob($id) {
        $sql = "UPDATE jobs SET admin_status = 'rejected', updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        return $this->update($sql, [':id' => $id]);
    }

    public function search($query, $category = null, $budgetMin = null, $budgetMax = null) {
        $sql = "SELECT j.*, u.first_name, u.last_name 
                FROM jobs j 
                JOIN users u ON j.client_id = u.id 
                WHERE j.admin_status = 'approved' 
                AND j.status = 'active' 
                AND (j.title LIKE :query OR j.description LIKE :query)";
        
        $params = [':query' => "%{$query}%"];
        
        if ($category) {
            $sql .= " AND j.category = :category";
            $params[':category'] = $category;
        }
        
        if ($budgetMin) {
            $sql .= " AND (j.fixed_budget >= :budget_min OR j.max_budget >= :budget_min)";
            $params[':budget_min'] = $budgetMin;
        }
        
        if ($budgetMax) {
            $sql .= " AND (j.fixed_budget <= :budget_max OR j.min_budget <= :budget_max)";
            $params[':budget_max'] = $budgetMax;
        }
        
        $sql .= " ORDER BY j.created_at DESC";
        return $this->select($sql, $params);
    }

    public function getJobStats() {
        $sql = "SELECT 
                COUNT(*) as total_jobs,
                COUNT(CASE WHEN admin_status = 'pending' THEN 1 END) as pending_jobs,
                COUNT(CASE WHEN admin_status = 'approved' THEN 1 END) as approved_jobs,
                COUNT(CASE WHEN admin_status = 'rejected' THEN 1 END) as rejected_jobs,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_jobs
                FROM jobs";
        return $this->selectOne($sql);
    }

    public function getRecentJobs($limit = 10) {
        $sql = "SELECT j.*, u.first_name, u.last_name 
                FROM jobs j 
                JOIN users u ON j.client_id = u.id 
                WHERE j.admin_status = 'approved' 
                ORDER BY j.created_at DESC 
                LIMIT :limit";
        return $this->select($sql, [':limit' => $limit]);
    }
}