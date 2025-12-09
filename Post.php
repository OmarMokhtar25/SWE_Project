<?php

class Post extends Model {
    
    public function create($data) {
        $sql = "INSERT INTO posts (client_id, title, content, category, tags, attachments, budget, deadline, status) 
                VALUES (:client_id, :title, :content, :category, :tags, :attachments, :budget, :deadline, :status)";
        
        return $this->insert($sql, [
            ':client_id' => $data['client_id'],
            ':title' => $data['title'],
            ':content' => $data['content'],
            ':category' => $data['category'],
            ':tags' => json_encode($data['tags']),
            ':attachments' => json_encode($data['attachments']),
            ':budget' => $data['budget'],
            ':deadline' => $data['deadline'],
            ':status' => 'pending'
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE posts SET title = :title, content = :content, category = :category, 
                tags = :tags, attachments = :attachments, budget = :budget, deadline = :deadline, 
                status = :status, updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id";
        
        return $this->update($sql, [
            ':title' => $data['title'],
            ':content' => $data['content'],
            ':category' => $data['category'],
            ':tags' => json_encode($data['tags']),
            ':attachments' => json_encode($data['attachments']),
            ':budget' => $data['budget'],
            ':deadline' => $data['deadline'],
            ':status' => $data['status'],
            ':id' => $id
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM posts WHERE id = :id";
        return $this->delete($sql, [':id' => $id]);
    }

    public function findById($id) {
        $sql = "SELECT p.*, u.first_name, u.last_name, u.email as client_email 
                FROM posts p 
                JOIN users u ON p.client_id = u.id 
                WHERE p.id = :id";
        return $this->selectOne($sql, [':id' => $id]);
    }

    public function findByClient($clientId) {
        $sql = "SELECT * FROM posts WHERE client_id = :client_id ORDER BY created_at DESC";
        return $this->select($sql, [':client_id' => $clientId]);
    }

    public function getAllApproved() {
        $sql = "SELECT p.*, u.first_name, u.last_name 
                FROM posts p 
                JOIN users u ON p.client_id = u.id 
                WHERE p.status = 'approved' 
                ORDER BY p.created_at DESC";
        return $this->select($sql);
    }

    public function getPendingPosts() {
        $sql = "SELECT p.*, u.first_name, u.last_name, u.email 
                FROM posts p 
                JOIN users u ON p.client_id = u.id 
                WHERE p.status = 'pending' 
                ORDER BY p.created_at DESC";
        return $this->select($sql);
    }

    public function approvePost($id) {
        $sql = "UPDATE posts SET status = 'approved', updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        return $this->update($sql, [':id' => $id]);
    }

    public function rejectPost($id) {
        $sql = "UPDATE posts SET status = 'rejected', updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        return $this->update($sql, [':id' => $id]);
    }
}