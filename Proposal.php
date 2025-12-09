<?php

class Proposal extends Model {
    
    public function create($data) {
        $sql = "INSERT INTO proposals (freelancer_id, job_id, cover_letter, bid_amount, delivery_time, attachments, status) 
                VALUES (:freelancer_id, :job_id, :cover_letter, :bid_amount, :delivery_time, :attachments, :status)";
        
        return $this->insert($sql, [
            ':freelancer_id' => $data['freelancer_id'],
            ':job_id' => $data['job_id'],
            ':cover_letter' => $data['cover_letter'],
            ':bid_amount' => $data['bid_amount'],
            ':delivery_time' => $data['delivery_time'],
            ':attachments' => json_encode($data['attachments']),
            ':status' => 'pending'
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE proposals SET cover_letter = :cover_letter, bid_amount = :bid_amount, 
                delivery_time = :delivery_time, attachments = :attachments, status = :status, 
                updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id";
        
        return $this->update($sql, [
            ':cover_letter' => $data['cover_letter'],
            ':bid_amount' => $data['bid_amount'],
            ':delivery_time' => $data['delivery_time'],
            ':attachments' => json_encode($data['attachments']),
            ':status' => $data['status'],
            ':id' => $id
        ]);
    }

    public function findByFreelancer($freelancerId, $status = null) {
        $sql = "SELECT p.*, j.title as job_title, u.first_name as client_first_name, u.last_name as client_last_name 
                FROM proposals p 
                JOIN jobs j ON p.job_id = j.id 
                JOIN users u ON j.client_id = u.id 
                WHERE p.freelancer_id = :freelancer_id";
        
        if ($status) {
            $sql .= " AND p.status = :status";
            $params = [':freelancer_id' => $freelancerId, ':status' => $status];
        } else {
            $params = [':freelancer_id' => $freelancerId];
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        return $this->select($sql, $params);
    }

    public function findByJob($jobId) {
        $sql = "SELECT p.*, u.first_name, u.last_name, u.email as freelancer_email 
                FROM proposals p 
                JOIN users u ON p.freelancer_id = u.id 
                WHERE p.job_id = :job_id 
                ORDER BY p.created_at DESC";
        return $this->select($sql, [':job_id' => $jobId]);
    }

    public function acceptProposal($id) {
        $sql = "UPDATE proposals SET status = 'accepted', updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        return $this->update($sql, [':id' => $id]);
    }

    public function rejectProposal($id) {
        $sql = "UPDATE proposals SET status = 'rejected', updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        return $this->update($sql, [':id' => $id]);
    }

    public function getStats($freelancerId) {
        $sql = "SELECT 
                COUNT(*) as total_proposals,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_proposals,
                COUNT(CASE WHEN status = 'accepted' THEN 1 END) as accepted_proposals,
                COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_proposals,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_proposals
                FROM proposals 
                WHERE freelancer_id = :freelancer_id";
        return $this->selectOne($sql, [':freelancer_id' => $freelancerId]);
    }
}