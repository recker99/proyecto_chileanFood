<?php
require_once __DIR__ . '/../core/Model.php';

class ReviewModel extends Model 
{
    public function __construct() 
    {
        parent::__construct();
    }

public function getReviewsByRestaurant($restaurant_id)
{
    $sql = "SELECT 
                rv.id,
                rv.rating,
                rv.comment,
                rv.created_at,
                u.name AS username
            FROM reviews rv
            INNER JOIN users u ON u.id = rv.user_id
            WHERE rv.restaurant_id = :restaurant_id
            ORDER BY rv.created_at DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':restaurant_id', $restaurant_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function addReview($restaurant_id, $user_id, $comment, $rating) 
{
    try {
        if ($rating < 1 || $rating > 5) {
            throw new Exception("Rating inválido: debe estar entre 1 y 5");
        }

        $comment = trim($comment);

        $this->db->beginTransaction();

        $query = "INSERT INTO reviews (restaurant_id, user_id, rating, comment, created_at) 
                  VALUES (:restaurant_id, :user_id, :rating, :comment, NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':restaurant_id' => $restaurant_id,
            ':user_id'       => $user_id,
            ':rating'        => $rating,
            ':comment'       => $comment
        ]);

        $updateQuery = "UPDATE restaurants 
                        SET review_count = review_count + 1,
                            updated_at = NOW()
                        WHERE id = :restaurant_id";
        $updateStmt = $this->db->prepare($updateQuery);
        $updateStmt->execute([':restaurant_id' => $restaurant_id]);

        $this->db->commit();
        return true;

    } catch (PDOException $e) {
        $this->db->rollBack();
        throw new Exception("Error al agregar reseña: " . $e->getMessage());
    }
}


    public function getAverageRating($restaurant_id) 
    {
        try {
            $query = "SELECT 
                AVG(rating) as average,
                COUNT(*) as total 
            FROM reviews 
            WHERE restaurant_id = :restaurant_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':restaurant_id', $restaurant_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'average' => $result['average'] ? round($result['average'], 1) : 0,
                'total' => $result['total'] ?? 0
            ];
            
        } catch (PDOException $e) {
            throw new Exception("Error al calcular rating promedio: " . $e->getMessage());
        }
    }

    public function getAllReviews() 
    {
        try {
            $query = "SELECT 
                r.*, 
                u.name as user_name, 
                rest.name as restaurant_name 
            FROM reviews r 
            JOIN users u ON r.user_id = u.id 
            JOIN restaurants rest ON r.restaurant_id = rest.id 
            ORDER BY r.created_at DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todas las reseñas: " . $e->getMessage());
        }
    }

    public function getTotalReviews() 
    {
        try {
            $query = "SELECT COUNT(*) as total FROM reviews";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['total'];
            
        } catch (PDOException $e) {
            throw new Exception("Error al contar reseñas: " . $e->getMessage());
        }
    }

    public function deleteReview($id) 
    {
        try {
            $query = "DELETE FROM reviews WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar reseña: " . $e->getMessage());
        }
    }
}
?>