<?php

class SavedPost extends Model {
    
    public function save($userId, $postId) {
        $sql = "INSERT INTO saved_posts (user_id, post_id) VALUES (:user_id, :post_id)";
        return $this->insert($sql, [':user_id' => $userId, ':post_id' => $postId]);
    }

    public function unsave($userId, $postId) {
        $sql = "DELETE FROM saved_posts WHERE user_id = :user_id AND post_id = :post_id";
        return $this->delete($sql, [':user_id' => $userId, ':post_id' => $postId]);
    }

    public function isSaved($userId, $postId) {
        $sql = "SELECT * FROM saved_posts WHERE user_id = :user_id AND post_id = :post_id";
        return $this->selectOne($sql, [':user_id' => $userId, ':post_id' => $postId]) !== null;
    }

    public function getUserSavedPosts($userId) {
        $sql = "SELECT p.*, u.first_name, u.last_name 
                FROM saved_posts sp 
                JOIN posts p ON sp.post_id = p.id 
                JOIN users u ON p.client_id = u.id 
                WHERE sp.user_id = :user_id 
                ORDER BY sp.created_at DESC";
        return $this->select($sql, [':user_id' => $userId]);
    }
}