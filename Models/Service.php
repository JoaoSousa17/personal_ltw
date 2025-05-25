<?php
class Service {
    private $id;
    private $freelancer_id;
    private $name;
    private $description;
    private $duration;
    private $is_active;
    private $price_per_hour;
    private $promotion;
    private $category_id;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Getters e Setters
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getFreelancerId() {
        return $this->freelancer_id;
    }

    public function setFreelancerId($freelancer_id) {
        $this->freelancer_id = $freelancer_id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getDuration() {
        return $this->duration;
    }

    public function setDuration($duration) {
        $this->duration = $duration;
    }

    public function getIsActive() {
        return $this->is_active;
    }

    public function setIsActive($is_active) {
        $this->is_active = $is_active;
    }

    public function getPricePerHour() {
        return $this->price_per_hour;
    }

    public function setPricePerHour($price_per_hour) {
        $this->price_per_hour = $price_per_hour;
    }

    public function getPromotion() {
        return $this->promotion;
    }

    public function setPromotion($promotion) {
        $this->promotion = $promotion;
    }

    public function getCategoryId() {
        return $this->category_id;
    }

    public function setCategoryId($category_id) {
        $this->category_id = $category_id;
    }

    // Método para carregar um serviço pelo ID
    public function readOne() {
        $query = "SELECT * FROM Service_ WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->freelancer_id = $row['freelancer_id'];
            $this->name = $row['name_'];
            $this->description = $row['description_'];
            $this->duration = $row['duration'];
            $this->is_active = $row['is_active'];
            $this->price_per_hour = $row['price_per_hour'];
            $this->promotion = $row['promotion'];
            $this->category_id = $row['category_id'];
            return true;
        }
        return false;
    }

    // Método para listar todos os serviços
    public function readAll() {
        $query = "SELECT * FROM Service_ WHERE is_active = 1 ORDER BY name_";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Método para pesquisar serviços por termo
    public function search($keyword) {
        $query = "SELECT * FROM Service_ 
                  WHERE is_active = 1 AND 
                  (name_ LIKE ? OR description_ LIKE ?) 
                  ORDER BY name_";
        
        $keyword = "%{$keyword}%";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $keyword);
        $stmt->bindParam(2, $keyword);
        $stmt->execute();

        return $stmt;
    }

    // Método para buscar sugestões de serviços baseado no termo
    public function getSuggestions($keyword) {
        $query = "SELECT id, name_ FROM Service_ 
                  WHERE is_active = 1 AND name_ LIKE ? 
                  ORDER BY name_ LIMIT 5";
        
        $keyword = "%{$keyword}%";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $keyword);
        $stmt->execute();

        return $stmt;
    }

    // Método para buscar serviços por categoria
    public function getByCategory($category_id) {
        $query = "SELECT * FROM Service_ 
                  WHERE is_active = 1 AND category_id = ? 
                  ORDER BY name_";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $category_id);
        $stmt->execute();

        return $stmt;
    }

    // Método para filtrar serviços com várias condições
    public function filter($category_id = null, $min_price = null, $max_price = null, $keyword = null) {
        $conditions = ["is_active = 1"];
        $params = [];
        
        if ($category_id) {
            $conditions[] = "category_id = ?";
            $params[] = $category_id;
        }
        
        if ($min_price !== null) {
            $conditions[] = "price_per_hour >= ?";
            $params[] = $min_price;
        }
        
        if ($max_price !== null) {
            $conditions[] = "price_per_hour <= ?";
            $params[] = $max_price;
        }
        
        if ($keyword) {
            $conditions[] = "(name_ LIKE ? OR description_ LIKE ?)";
            $keyword = "%{$keyword}%";
            $params[] = $keyword;
            $params[] = $keyword;
        }
        
        $query = "SELECT * FROM Service_ WHERE " . implode(" AND ", $conditions) . " ORDER BY name_";
        $stmt = $this->conn->prepare($query);
        
        for ($i = 0; $i < count($params); $i++) {
            $stmt->bindParam($i + 1, $params[$i]);
        }
        
        $stmt->execute();
        return $stmt;
    }
}
?>
