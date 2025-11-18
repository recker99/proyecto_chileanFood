<?php
require_once __DIR__ . '/../core/Model.php';

class ContactModel extends Model {
    
    public function __construct() {
        parent::__construct();
    }

    public function getPendingContactsCount() {
        $query = "SELECT COUNT(*) as total FROM contact_messages WHERE status = 'pending'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getAllMessages() {
        $query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead($id) {
        $query = "UPDATE contact_messages SET status = 'read', updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    public function deleteMessage($id) {
        $query = "DELETE FROM contact_messages WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
}
?>