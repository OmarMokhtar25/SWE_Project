<?php

class Model {
    protected $db;

    public function __construct() {
        $this->db = new Database();
    }

    protected function query($sql, $params = []) {
        return $this->db->query($sql, $params);
    }

    protected function select($sql, $params = []) {
        return $this->db->select($sql, $params);
    }

    protected function selectOne($sql, $params = []) {
        return $this->db->selectOne($sql, $params);
    }

    protected function insert($sql, $params = []) {
        return $this->db->insert($sql, $params);
    }

    protected function update($sql, $params = []) {
        return $this->db->update($sql, $params);
    }

    protected function delete($sql, $params = []) {
        return $this->db->delete($sql, $params);
    }
}