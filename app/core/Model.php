<?php
    require_once __DIR__ . '/../../config/database.php';

    class Model {
        protected $db;

        public function __construct() {
            $database = new Database();
            $this->db = $database->getConnection();
        }

        protected function hashPassword($password) {
            return password_hash($password, PASSWORD_DEFAULT);
        }

        protected function verifyPassword($password, $hash) {
            return password_verify($password, $hash);
        }
    }
?>