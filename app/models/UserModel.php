<?php
require_once __DIR__ . '/../core/Model.php';

class UserModel extends Model {
    
    public function __construct() {
        parent::__construct();
    }

    // Método de login existente
    public function login($email, $password) {
        $query = "SELECT * FROM users WHERE email = :email AND status = 'active'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    // Método de registro existente
    public function register($data) {
        // Verificar si el email ya existe
        $query = "SELECT id FROM users WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $data['email']);
        $stmt->execute();
        
        if ($stmt->fetch()) {
            return false; // Email ya existe
        }

        // Insertar nuevo usuario
        $query = "INSERT INTO users (name, email, password, created_at) 
                 VALUES (:name, :email, :password, NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        
        return $stmt->execute();
    }

    // MÉTODOS NUEVOS PARA EL DASHBOARD

    public function getTotalUsers() {
        $query = "SELECT COUNT(*) as total FROM users WHERE status = 'active'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getRecentUsers($limit = 5) {
        $query = "SELECT id, name, email, role, created_at 
                 FROM users 
                 WHERE status = 'active' 
                 ORDER BY created_at DESC 
                 LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUsers() {
        $query = "SELECT id, name, email, role, created_at, status 
                 FROM users 
                 ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($id) {
        $query = "SELECT id, name, email, role, created_at, status 
                 FROM users 
                 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUser($id, $data) {
        $query = "UPDATE users 
                 SET name = :name, email = :email, role = :role, updated_at = NOW() 
                 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':role' => $data['role'],
            ':id' => $id
        ]);
    }

    public function deleteUser($id) {
        $query = "UPDATE users 
                 SET status = 'inactive', updated_at = NOW() 
                 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    // Método adicional para verificar si el usuario existe
    public function userExists($email) {
        $query = "SELECT id FROM users WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch() !== false;
    }
}
?>