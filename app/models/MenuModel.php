<?php
// Usando el Model base que tiene la conexión a la DB
require_once __DIR__ . '/../core/Model.php';
// Nota: Si usas Model.php y este ya maneja la conexión, NO necesitas 'require_once __DIR__ . '/../core/Database.php';' aquí.

class MenuModel extends Model {
    
    public function __construct() {
        parent::__construct();
    }

    /**
     * Obtiene los platos destacados (featured dishes) de la base de datos.
     * ESTA ES LA FUNCIÓN QUE FALTABA Y QUE NECESITA MenuController.php
     * @return array Lista de platos destacados o un array vacío si no hay resultados.
     */
    public function getFeaturedDishes() {
        // Asume que la columna en la tabla es 'is_featured'
        $query = "SELECT 
                    id AS dish_id, name AS dish_name, description AS dish_description, 
                    price AS dish_price, image AS dish_image, category
                  FROM 
                    menu_items
                  WHERE 
                    is_featured = 1
                  ORDER BY 
                    created_at DESC
                  LIMIT 6"; // Limita a 6 para la sección de destacados

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching featured dishes: " . $e->getMessage());
            return [];
        }
    }
    
    // --- Métodos existentes que proporcionaste ---

    public function getAllMenuItems() {
        $query = "
            SELECT 
                mi.id AS dish_id, 
                mi.name AS dish_name, 
                mi.description AS dish_description, 
                mi.price AS dish_price, 
                mi.image AS dish_image, 
                mi.category, 
                r.id AS restaurant_id,
                r.name AS restaurant_name, 
                r.region, 
                r.address, 
                r.location
            FROM menu_items mi
            INNER JOIN restaurants r ON mi.restaurant_id = r.id
            WHERE r.status = 'active'
            ORDER BY mi.name ASC
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un plato por su ID, incluyendo datos del restaurante.
     * @param int $id ID del plato.
     * @return array|false Datos del plato o false si no existe.
     */
    public function getDishById($id) {
        $query = "
            SELECT 
                mi.id AS dish_id, 
                mi.name AS dish_name, 
                mi.description AS dish_description, 
                mi.price AS dish_price, 
                mi.image AS dish_image, 
                mi.category, 
                r.id AS restaurant_id,
                r.name AS restaurant_name
            FROM menu_items mi
            INNER JOIN restaurants r ON mi.restaurant_id = r.id
            WHERE mi.id = :id AND r.status = 'active'
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Busca platos por término de búsqueda y opcionalmente por categoría.
     * Utilizado para filtros en la página del menú.
     * @param string $searchText Texto para buscar en nombre del plato, descripción o nombre del restaurante.
     * @param string $category Filtro de categoría (ej. 'Almuerzo', 'Cena'). Use 'Todos' para no filtrar.
     * @return array Resultados filtrados.
     */
    public function searchAndFilter($searchText = '', $category = 'Todos') {
        $query = "
            SELECT 
                mi.id AS dish_id, 
                mi.name AS dish_name, 
                mi.description AS dish_description, 
                mi.price AS dish_price, 
                mi.image AS dish_image, 
                mi.category,
                r.id AS restaurant_id,
                r.name AS restaurant_name
            FROM menu_items mi
            INNER JOIN restaurants r ON mi.restaurant_id = r.id
            WHERE r.status = 'active'
        ";
        
        $params = [];
        $search_pattern = "%$searchText%";

        // 1. Lógica de Búsqueda
        if (!empty($searchText)) {
            $query .= " AND (mi.name LIKE :search_term 
                              OR mi.description LIKE :search_term 
                              OR r.name LIKE :search_term)";
            $params[':search_term'] = $search_pattern;
        }

        // 2. Lógica de Filtrado por Categoría
        if ($category !== 'Todos' && !empty($category)) {
            $query .= " AND mi.category = :category ";
            $params[':category'] = $category;
        }

        $query .= " ORDER BY mi.name ASC";

        $stmt = $this->db->prepare($query);
        
        // Asignar parámetros usando bindParam
        foreach ($params as $key => &$value) {
            $stmt->bindParam($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // ===== MÉTODOS DE GESTIÓN (Dashboard) - Duplicados del RestaurantModel para mantener la responsabilidad aquí

    /**
     * Agrega un nuevo plato (asumiendo que es para uso interno/dashboard).
     */
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
    
    /**
     * Actualiza un plato existente.
     */
    public function updateDish($id, $data) {
        $query = "UPDATE menu_items SET 
                      name = :name, 
                      description = :description, 
                      price = :price, 
                      image = :image, 
                      category = :category,
                      restaurant_id = :restaurant_id
                      WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':price' => $data['price'],
            ':image' => $data['image'],
            ':category' => $data['category'],
            ':restaurant_id' => $data['restaurant_id'],
            ':id' => $id
        ]);
    }
    
    /**
     * Elimina un plato (borrado físico).
     */
    public function deleteDish($id) {
        $query = "DELETE FROM menu_items WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
    
}