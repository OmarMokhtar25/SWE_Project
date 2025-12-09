<?php

class Comment extends Model {
    
    public function create($data) {
        $sql = "INSERT INTO comments (post_id, freelancer_id, content, parent_id) 
                VALUES (:post_id, :freelancer_id, :content, :parent_id)";
        
        return $this->insert($sql, [
            ':post_id' => $data['post_id'],
            ':freelancer_id' => $data['freelancer_id'],
            ':content' => $data['content'],
            ':parent_id' => $data['parent_id'] ?? null
        ]);
    }

    public function findByPost($postId) {
        $sql = "SELECT c.*, u.first_name, u.last_name, u.username 
                FROM comments c 
                JOIN users u ON c.freelancer_id = u.id 
                WHERE c.post_id = :post_id AND c.parent_id IS NULL 
                ORDER BY c.created_at DESC";
        return $this->select($sql, [':post_id' => $postId]);
    }

    public function getReplies($commentId) {
        $sql = "SELECT c.*, u.first_name, u.last_name, u.username 
                FROM comments c 
                JOIN users u ON c.freelancer_id = u.id 
                WHERE c.parent_id = :parent_id 
                ORDER BY c.created_at ASC";
        return $this->select($sql, [':parent_id' => $commentId]);
    }

    public function delete($id) {
        $sql = "DELETE FROM comments WHERE id = :id";
        return $this->delete($sql, [':id' => $id]);
    }
}