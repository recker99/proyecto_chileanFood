<?php
require_once __DIR__ . '/../core/Model.php';

class RestaurantModel extends Model {
    
    public function __construct() {
        parent::__construct();
    }

    // ===== MÉTODOS PARA LA APLICACIÓN PRINCIPAL =====

    // Obtener restaurantes por región
    public function getRestaurantsByRegion($region) {
        $query = "SELECT * FROM restaurants WHERE region = :region AND status = 'active'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':region', $region);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener restaurante por ID
    public function getRestaurantById($id) {
        $query = "SELECT * FROM restaurants WHERE id = :id AND status = 'active'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener platos destacados con JOIN entre restaurantes y menu_items
    public function getFeaturedDishes() {
    $query = "
        SELECT 
            r.id AS restaurant_id,
            r.name AS restaurant_name,
            r.region,
            r.location,
            r.image AS restaurant_image,
            r.description AS restaurant_description,
            m.id AS dish_id,
            m.name AS dish_name,
            m.description AS dish_description,
            m.price AS dish_price,
            m.image AS dish_image,
            m.category,
            COALESCE(AVG(rev.rating), 0) AS avg_rating,
            COUNT(rev.id) AS review_count
        FROM restaurants r
        INNER JOIN menu_items m ON m.restaurant_id = r.id
        LEFT JOIN reviews rev ON rev.restaurant_id = r.id
        WHERE r.status = 'active'
        AND m.id = (
            SELECT MIN(mi.id)
            FROM menu_items mi
            WHERE mi.restaurant_id = r.id
        )
        GROUP BY 
            r.id, r.name, r.region, r.location, r.image, r.description,
            m.id, m.name, m.description, m.price, m.image, m.category
        ORDER BY avg_rating DESC, review_count DESC
        LIMIT 6
    ";

    $stmt = $this->db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    // Obtener platos de un restaurante específico
    public function getDishesByRestaurant($restaurant_id) {
        $query = "SELECT 
                    m.*,
                    -- Calcular rating promedio y conteo de reseñas del restaurante
                    (SELECT AVG(rating) FROM reviews WHERE restaurant_id = :restaurant_id) as avg_rating,
                    (SELECT COUNT(*) FROM reviews WHERE restaurant_id = :restaurant_id) as review_count
                  FROM menu_items m
                  WHERE m.restaurant_id = :restaurant_id
                  ORDER BY m.name ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':restaurant_id', $restaurant_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar platos por término de búsqueda
    public function searchDishes($search_term) {
        $query = "SELECT 
                    r.id as restaurant_id,
                    r.name as restaurant_name,
                    r.region,
                    r.location,
                    r.image as restaurant_image,
                    m.id as dish_id,
                    m.name as dish_name,
                    m.description as dish_description,
                    m.price as dish_price,
                    m.image as dish_image,
                    m.category,
                    -- Calcular rating promedio y conteo de reseñas del restaurante
                    (SELECT AVG(rating) FROM reviews WHERE restaurant_id = r.id) as avg_rating,
                    (SELECT COUNT(*) FROM reviews WHERE restaurant_id = r.id) as review_count
                  FROM menu_items m
                  INNER JOIN restaurants r ON m.restaurant_id = r.id
                  WHERE (m.name LIKE :search_term 
                         OR m.description LIKE :search_term 
                         OR r.name LIKE :search_term)
                    AND r.status = 'active'
                  ORDER BY avg_rating DESC, review_count DESC";
        
        $stmt = $this->db->prepare($query);
        $search_pattern = "%$search_term%";
        $stmt->bindParam(':search_term', $search_pattern);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener restaurante con todos sus datos incluyendo platos
    public function getRestaurantWithDishes($restaurant_id) {
        // Primero obtener datos del restaurante
        $restaurant = $this->getRestaurantById($restaurant_id);
        
        if (!$restaurant) {
            return null;
        }

        // Luego obtener sus platos
        $restaurant['dishes'] = $this->getDishesByRestaurant($restaurant_id);
        
        return $restaurant;
    }

    // Agregar reseña (solo para restaurante, sin dish_id)
    public function addReview($data) {
        $query = "INSERT INTO reviews (restaurant_id, user_id, rating, comment, created_at) 
                  VALUES (:restaurant_id, :user_id, :rating, :comment, NOW())";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':restaurant_id' => $data['restaurant_id'],
            ':user_id' => $data['user_id'],
            ':rating' => $data['rating'],
            ':comment' => $data['comment']
        ]);
    }

    // Obtener reseñas de un restaurante
    public function getRestaurantReviews($restaurant_id) {
        $query = "SELECT 
                    rev.*,
                    u.name as user_name
                  FROM reviews rev
                  LEFT JOIN users u ON rev.user_id = u.id
                  WHERE rev.restaurant_id = :restaurant_id
                  ORDER BY rev.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':restaurant_id', $restaurant_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener rating promedio de un restaurante
    public function getRestaurantRating($restaurant_id) {
        $query = "SELECT 
                    AVG(rating) as avg_rating,
                    COUNT(*) as review_count
                  FROM reviews 
                  WHERE restaurant_id = :restaurant_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':restaurant_id', $restaurant_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ===== MÉTODOS PARA EL DASHBOARD =====

    public function getTotalRestaurants() {
        $query = "SELECT COUNT(*) as total FROM restaurants WHERE status = 'active'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getRecentRestaurants($limit = 5) {
        $query = "SELECT * FROM restaurants 
                 WHERE status = 'active' 
                 ORDER BY created_at DESC 
                 LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllRestaurants() {
        $query = "SELECT r.*, 
                         u.name as owner_name,
                         (SELECT AVG(rating) FROM reviews WHERE restaurant_id = r.id) as avg_rating,
                         (SELECT COUNT(*) FROM reviews WHERE restaurant_id = r.id) as review_count
                 FROM restaurants r 
                 LEFT JOIN users u ON r.owner_id = u.id 
                 ORDER BY r.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Métodos para gestionar platos desde el dashboard
    public function getAllDishes() {
        $query = "SELECT m.*, r.name as restaurant_name 
                 FROM menu_items m 
                 INNER JOIN restaurants r ON m.restaurant_id = r.id 
                 ORDER BY m.name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDishById($id) {
        $query = "SELECT m.*, r.name as restaurant_name 
                 FROM menu_items m 
                 INNER JOIN restaurants r ON m.restaurant_id = r.id 
                 WHERE m.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addDish($data) {
        $query = "INSERT INTO menu_items (restaurant_id, name, description, price, image, category) 
                  VALUES (:restaurant_id, :name, :description, :price, :image, :category)";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':restaurant_id' => $data['restaurant_id'],
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':price' => $data['price'],
            ':image' => $data['image'],
            ':category' => $data['category']
        ]);
    }

    public function updateDish($id, $data) {
        $query = "UPDATE menu_items SET 
                 name = :name, 
                 description = :description, 
                 price = :price, 
                 image = :image, 
                 category = :category
                 WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':price' => $data['price'],
            ':image' => $data['image'],
            ':category' => $data['category'],
            ':id' => $id
        ]);
    }

    public function deleteDish($id) {
        $query = "DELETE FROM menu_items WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    public function updateRestaurant($id, $data) {
        $query = "UPDATE restaurants SET 
                 name = :name, 
                 description = :description, 
                 location = :location, 
                 region = :region, 
                 phone = :phone, 
                 email = :email, 
                 updated_at = NOW() 
                 WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':location' => $data['location'],
            ':region' => $data['region'],
            ':phone' => $data['phone'],
            ':email' => $data['email'],
            ':id' => $id
        ]);
    }

    public function deleteRestaurant($id) {
        $query = "UPDATE restaurants 
                 SET status = 'inactive', updated_at = NOW() 
                 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    // Método para obtener restaurante por ID (sin filtro de status para dashboard)
    public function getRestaurantByIdForDashboard($id) {
        $query = "SELECT r.*, 
                         u.name as owner_name,
                         (SELECT AVG(rating) FROM reviews WHERE restaurant_id = r.id) as avg_rating,
                         (SELECT COUNT(*) FROM reviews WHERE restaurant_id = r.id) as review_count
                 FROM restaurants r 
                 LEFT JOIN users u ON r.owner_id = u.id 
                 WHERE r.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>