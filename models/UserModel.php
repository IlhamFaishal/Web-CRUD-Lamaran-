<?php

class UserModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch();
    }
}
