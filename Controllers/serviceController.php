<?php
require_once(dirname(__FILE__)."/../Models/Service.php");
require_once(dirname(__FILE__)."/../Database/connection.php");

// Obter todos os serviços ativos
function getAllServices() {
    $db = getDatabaseConnection();
    $query = "SELECT * FROM Service_ WHERE is_active = 1 ORDER BY name_";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    return formatResults($stmt);
}

// Obter serviço por ID
function getServiceById($id) {
    $db = getDatabaseConnection();
    $query = "SELECT * FROM Service_ WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $id);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        return [
            'id' => $row['id'],
            'freelancer_id' => $row['freelancer_id'],
            'name' => $row['name_'],
            'description' => $row['description_'],
            'duration' => $row['duration'],
            'is_active' => $row['is_active'],
            'price_per_hour' => $row['price_per_hour'],
            'promotion' => $row['promotion'],
            'category_id' => $row['category_id']
        ];
    }
    return null;
}

// Pesquisar serviços por termo
function searchServices($keyword) {
    $db = getDatabaseConnection();
    $query = "SELECT * FROM Service_ 
              WHERE is_active = 1 AND 
              (name_ LIKE ? OR description_ LIKE ?) 
              ORDER BY name_";
    
    $keyword = "%{$keyword}%";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $keyword);
    $stmt->bindParam(2, $keyword);
    $stmt->execute();

    return formatResults($stmt);
}

// Buscar sugestões de serviços baseado no termo
function getServiceSuggestions($keyword) {
    $db = getDatabaseConnection();
    $query = "SELECT id, name_ FROM Service_ 
              WHERE is_active = 1 AND name_ LIKE ? 
              ORDER BY name_ LIMIT 5";
    
    $keyword = "%{$keyword}%";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $keyword);
    $stmt->execute();

    return formatSuggestions($stmt);
}

// Obter serviços por categoria
function getServicesByCategory($category_id) {
    $db = getDatabaseConnection();
    $query = "SELECT * FROM Service_ 
              WHERE is_active = 1 AND category_id = ? 
              ORDER BY name_";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $category_id);
    $stmt->execute();

    return formatResults($stmt);
}

// Filtrar serviços com várias condições
function filterServices($category_id = null, $min_price = null, $max_price = null, $keyword = null) {
    $db = getDatabaseConnection();
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
    $stmt = $db->prepare($query);
    
    for ($i = 0; $i < count($params); $i++) {
        $stmt->bindParam($i + 1, $params[$i]);
    }
    
    $stmt->execute();
    return formatResults($stmt);
}

// Formatar resultados em um array
function formatResults($stmt) {
    $services = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $service_item = [
            'id' => $row['id'],
            'freelancer_id' => $row['freelancer_id'],
            'name' => $row['name_'],
            'description' => $row['description_'],
            'duration' => $row['duration'],
            'is_active' => $row['is_active'],
            'price_per_hour' => $row['price_per_hour'],
            'promotion' => $row['promotion'],
            'category_id' => $row['category_id']
        ];
        $services[] = $service_item;
    }
    return $services;
}

// Formatar sugestões em um array simples
function formatSuggestions($stmt) {
    $suggestions = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $suggestions[] = [
            'id' => $row['id'],
            'name' => $row['name_']
        ];
    }
    return $suggestions;
}

// Calcular preço com desconto
function calculateDiscountedPrice($price, $promotion) {
    if ($promotion > 0) {
        return $price - ($price * ($promotion / 100));
    }
    return $price;
}

// Calcular preço total baseado na duração
function calculateTotalPrice($price_per_hour, $promotion, $duration) {
    $discounted_price = calculateDiscountedPrice($price_per_hour, $promotion);
    return $discounted_price * ($duration / 60); // Convertendo duração de minutos para horas
}
?>

<?php
// Adicionar estas funções ao final do arquivo serviceController.php

/**
 * Obter pedidos de um utilizador específico
 * 
 * @param int $userId ID do utilizador
 * @return array Lista de pedidos do utilizador
 */
function getUserOrders($userId) {
    $db = getDatabaseConnection();
    
    $query = "
        SELECT 
            sd.id as order_id,
            sd.date_,
            sd.time_,
            sd.travel_fee,
            sd.final_price,
            sd.status_,
            s.name_ as service_name,
            s.description_,
            s.duration,
            s.price_per_hour,
            s.promotion,
            u.name_ as freelancer_name,
            u.username as freelancer_username,
            c.name_ as category_name
        FROM Service_Data sd
        JOIN Service_ s ON sd.service_id = s.id
        JOIN User_ u ON s.freelancer_id = u.id
        JOIN Category c ON s.category_id = c.id
        WHERE sd.user_id = :user_id
        ORDER BY sd.date_ DESC, sd.time_ DESC
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute([':user_id' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obter estatísticas dos pedidos de um utilizador
 * 
 * @param int $userId ID do utilizador
 * @return array Estatísticas dos pedidos
 */
function getOrderStats($userId) {
    $db = getDatabaseConnection();
    
    $query = "
        SELECT 
            COUNT(*) as total_orders,
            COUNT(CASE WHEN status_ = 'completed' THEN 1 END) as completed_orders,
            COUNT(CASE WHEN status_ = 'accepted' THEN 1 END) as accepted_orders,
            COUNT(CASE WHEN status_ = 'paid' THEN 1 END) as paid_orders,
            COALESCE(SUM(final_price), 0) as total_spent
        FROM Service_Data 
        WHERE user_id = :user_id
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute([':user_id' => $userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Obter serviços de um freelancer específico
 * 
 * @param int $userId ID do utilizador freelancer
 * @return array Lista de serviços do freelancer
 */
function getUserServices($userId) {
    $db = getDatabaseConnection();
    
    $query = "
        SELECT 
            s.id,
            s.name_,
            s.description_,
            s.duration,
            s.is_active,
            s.price_per_hour,
            s.promotion,
            c.name_ as category_name,
            COUNT(sd.id) as total_orders,
            COUNT(CASE WHEN sd.status_ = 'completed' THEN 1 END) as completed_orders,
            AVG(f.evaluation) as avg_rating,
            COUNT(f.id) as total_reviews
        FROM Service_ s
        LEFT JOIN Category c ON s.category_id = c.id
        LEFT JOIN Service_Data sd ON s.id = sd.service_id
        LEFT JOIN Feedback f ON s.id = f.service_id
        WHERE s.freelancer_id = :user_id
        GROUP BY s.id, s.name_, s.description_, s.duration, s.is_active, s.price_per_hour, s.promotion, c.name_
        ORDER BY s.is_active DESC, s.name_ ASC
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute([':user_id' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obter estatísticas dos serviços de um freelancer
 * 
 * @param int $userId ID do utilizador freelancer
 * @return array Estatísticas dos serviços
 */
function getServiceStats($userId) {
    $db = getDatabaseConnection();
    
    // Estatísticas dos serviços
    $query = "
        SELECT 
            COUNT(*) as total_services,
            COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_services,
            COUNT(CASE WHEN is_active = 0 THEN 1 END) as inactive_services
        FROM Service_ 
        WHERE freelancer_id = :user_id
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute([':user_id' => $userId]);
    $serviceStats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Estatísticas de pedidos
    $orderQuery = "
        SELECT 
            COUNT(sd.id) as total_orders,
            COUNT(CASE WHEN sd.status_ = 'completed' THEN 1 END) as completed_orders,
            COALESCE(SUM(sd.final_price), 0) as total_earned
        FROM Service_ s
        LEFT JOIN Service_Data sd ON s.id = sd.service_id
        WHERE s.freelancer_id = :user_id
    ";
    
    $stmt = $db->prepare($orderQuery);
    $stmt->execute([':user_id' => $userId]);
    $orderStats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return array_merge($serviceStats, $orderStats);
}

/**
 * Alternar o estado ativo/inativo de um serviço
 * 
 * @param int $serviceId ID do serviço
 * @param int $userId ID do utilizador (para verificar permissões)
 * @return bool True se a operação foi bem-sucedida
 */
function toggleServiceStatus($serviceId, $userId) {
    $db = getDatabaseConnection();
    
    // Verificar se o serviço pertence ao utilizador
    $checkQuery = "SELECT id FROM Service_ WHERE id = :service_id AND freelancer_id = :user_id";
    $stmt = $db->prepare($checkQuery);
    $stmt->execute([':service_id' => $serviceId, ':user_id' => $userId]);
    
    if ($stmt->fetch()) {
        // Alterar o estado do serviço
        $updateQuery = "UPDATE Service_ SET is_active = NOT is_active WHERE id = :service_id";
        $stmt = $db->prepare($updateQuery);
        return $stmt->execute([':service_id' => $serviceId]);
    }
    
    return false;
}

/**
 * Obter detalhes completos de um pedido específico
 * 
 * @param int $orderId ID do pedido
 * @param int $userId ID do utilizador (para verificar permissões)
 * @return array|null Detalhes do pedido ou null se não encontrado/sem permissão
 */
function getOrderDetails($orderId, $userId) {
    $db = getDatabaseConnection();
    
    $query = "
        SELECT 
            sd.*,
            s.name_ as service_name,
            s.description_ as service_description,
            s.duration,
            s.price_per_hour,
            s.promotion,
            u.name_ as freelancer_name,
            u.email as freelancer_email,
            u.phone_number as freelancer_phone,
            c.name_ as category_name
        FROM Service_Data sd
        JOIN Service_ s ON sd.service_id = s.id
        JOIN User_ u ON s.freelancer_id = u.id
        JOIN Category c ON s.category_id = c.id
        WHERE sd.id = :order_id AND sd.user_id = :user_id
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute([':order_id' => $orderId, ':user_id' => $userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Obter feedback/avaliações de um serviço específico
 * 
 * @param int $serviceId ID do serviço
 * @return array Lista de feedbacks do serviço
 */
function getServiceFeedback($serviceId) {
    $db = getDatabaseConnection();
    
    $query = "
        SELECT 
            f.*,
            u.name_ as user_name,
            u.username
        FROM Feedback f
        JOIN User_ u ON f.user_id = u.id
        WHERE f.service_id = :service_id
        ORDER BY f.date_ DESC, f.time_ DESC
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute([':service_id' => $serviceId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Criar um novo feedback/avaliação para um serviço
 * 
 * @param int $userId ID do utilizador que avalia
 * @param int $serviceId ID do serviço
 * @param float $evaluation Avaliação (0-5)
 * @param string $title Título da avaliação (opcional)
 * @param string $description Descrição da avaliação (opcional)
 * @return bool True se a avaliação foi criada com sucesso
 */
function createServiceFeedback($userId, $serviceId, $evaluation, $title = '', $description = '') {
    $db = getDatabaseConnection();
    
    $query = "
        INSERT INTO Feedback (user_id, service_id, evaluation, title, description_, date_, time_)
        VALUES (:user_id, :service_id, :evaluation, :title, :description, DATE('now'), TIME('now'))
    ";
    
    $stmt = $db->prepare($query);
    return $stmt->execute([
        ':user_id' => $userId,
        ':service_id' => $serviceId,
        ':evaluation' => $evaluation,
        ':title' => $title,
        ':description' => $description
    ]);
}

/**
 * Obter serviços mais populares (com mais pedidos)
 * 
 * @param int $limit Número máximo de serviços a retornar
 * @return array Lista de serviços populares
 */
function getPopularServices($limit = 10) {
    $db = getDatabaseConnection();
    
    $query = "
        SELECT 
            s.*,
            c.name_ as category_name,
            u.name_ as freelancer_name,
            COUNT(sd.id) as total_orders,
            AVG(f.evaluation) as avg_rating
        FROM Service_ s
        JOIN Category c ON s.category_id = c.id
        JOIN User_ u ON s.freelancer_id = u.id
        LEFT JOIN Service_Data sd ON s.id = sd.service_id
        LEFT JOIN Feedback f ON s.id = f.service_id
        WHERE s.is_active = 1
        GROUP BY s.id
        ORDER BY total_orders DESC, avg_rating DESC
        LIMIT :limit
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obter estatísticas gerais da plataforma
 * 
 * @return array Estatísticas gerais
 */
function getPlatformStats() {
    $db = getDatabaseConnection();
    
    $query = "
        SELECT 
            (SELECT COUNT(*) FROM Service_ WHERE is_active = 1) as total_active_services,
            (SELECT COUNT(*) FROM User_ WHERE is_freelancer = 1) as total_freelancers,
            (SELECT COUNT(*) FROM Service_Data) as total_orders,
            (SELECT COUNT(*) FROM Service_Data WHERE status_ = 'completed') as completed_orders,
            (SELECT AVG(evaluation) FROM Feedback) as platform_avg_rating
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<?php
// Adicionar estas funções ao final do arquivo serviceController.php

/**
 * Cria um novo serviço
 * 
 * @param array $serviceData Dados do serviço a ser criado
 * @return bool|int ID do serviço criado ou false em caso de erro
 */
function createService($serviceData) {
    $db = getDatabaseConnection();
    
    try {
        $query = "INSERT INTO Service_ (freelancer_id, name_, description_, duration, price_per_hour, promotion, category_id, is_active) 
                  VALUES (:freelancer_id, :name, :description, :duration, :price_per_hour, :promotion, :category_id, :is_active)";
        
        $stmt = $db->prepare($query);
        $result = $stmt->execute([
            ':freelancer_id' => $serviceData['freelancer_id'],
            ':name' => $serviceData['name'],
            ':description' => $serviceData['description'],
            ':duration' => $serviceData['duration'],
            ':price_per_hour' => $serviceData['price_per_hour'],
            ':promotion' => $serviceData['promotion'] ?? 0,
            ':category_id' => $serviceData['category_id'],
            ':is_active' => $serviceData['is_active'] ?? 1
        ]);
        
        if ($result) {
            return $db->lastInsertId();
        }
        return false;
        
    } catch (PDOException $e) {
        error_log("Erro ao criar serviço: " . $e->getMessage());
        return false;
    }
}

/**
 * Valida os dados de um serviço antes da criação/atualização
 * 
 * @param array $data Dados do serviço a validar
 * @return array Array com 'valid' (bool) e 'errors' (array)
 */
function validateServiceData($data) {
    $errors = [];
    
    // Validar nome
    if (empty($data['name']) || strlen(trim($data['name'])) < 3) {
        $errors[] = 'Nome do serviço deve ter pelo menos 3 caracteres.';
    }
    
    // Validar descrição
    if (empty($data['description']) || strlen(trim($data['description'])) < 10) {
        $errors[] = 'Descrição deve ter pelo menos 10 caracteres.';
    }
    
    // Validar duração
    if (!is_numeric($data['duration']) || $data['duration'] <= 0) {
        $errors[] = 'Duração deve ser um número positivo (em minutos).';
    }
    
    // Validar preço
    if (!is_numeric($data['price_per_hour']) || $data['price_per_hour'] <= 0) {
        $errors[] = 'Preço por hora deve ser um valor positivo.';
    }
    
    // Validar promoção
    if (isset($data['promotion']) && (!is_numeric($data['promotion']) || $data['promotion'] < 0 || $data['promotion'] > 100)) {
        $errors[] = 'Promoção deve ser um valor entre 0 e 100%.';
    }
    
    // Validar categoria
    if (empty($data['category_id']) || !is_numeric($data['category_id'])) {
        $errors[] = 'Categoria é obrigatória.';
    } else {
        // Verificar se a categoria existe
        $category = getCategoryById($data['category_id']);
        if (!$category) {
            $errors[] = 'Categoria selecionada não existe.';
        }
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

/**
 * Verifica se um utilizador é freelancer ou se pode criar serviços
 * 
 * @param int $userId ID do utilizador
 * @return bool True se pode criar serviços
 */
function canCreateServices($userId) {
    $db = getDatabaseConnection();
    
    try {
        $query = "SELECT is_freelancer, is_blocked FROM User_ WHERE id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->execute([':user_id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            return false;
        }
        
        // Pode criar serviços se for freelancer e não estiver bloqueado
        return $user['is_freelancer'] && !$user['is_blocked'];
        
    } catch (PDOException $e) {
        error_log("Erro ao verificar permissões: " . $e->getMessage());
        return false;
    }
}

/**
 * Atualiza o status de freelancer de um utilizador
 * 
 * @param int $userId ID do utilizador
 * @param bool $isFreelancer True para tornar freelancer
 * @return bool True se atualização foi bem-sucedida
 */
function updateFreelancerStatus($userId, $isFreelancer = true) {
    $db = getDatabaseConnection();
    
    try {
        $query = "UPDATE User_ SET is_freelancer = :is_freelancer WHERE id = :user_id";
        $stmt = $db->prepare($query);
        return $stmt->execute([
            ':is_freelancer' => $isFreelancer ? 1 : 0,
            ':user_id' => $userId
        ]);
        
    } catch (PDOException $e) {
        error_log("Erro ao atualizar status de freelancer: " . $e->getMessage());
        return false;
    }
}

/**
 * Processa o upload de imagens para um serviço
 * 
 * @param int $serviceId ID do serviço
 * @param array $files Array de ficheiros $_FILES
 * @return array Array com resultado da operação
 */
function uploadServiceImages($serviceId, $files) {
    $results = [];
    $uploadDir = dirname(__DIR__) . '/Images/services/';
    
    // Criar diretório se não existir
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            return ['success' => false, 'message' => 'Erro ao criar diretório de upload.'];
        }
    }
    
    foreach ($files['tmp_name'] as $index => $tmpName) {
        if ($files['error'][$index] !== UPLOAD_ERR_OK) {
            continue;
        }
        
        // Validar tipo de ficheiro
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($files['type'][$index], $allowedTypes)) {
            continue;
        }
        
        // Gerar nome único para o ficheiro
        $extension = pathinfo($files['name'][$index], PATHINFO_EXTENSION);
        $fileName = 'service_' . $serviceId . '_' . uniqid() . '.' . strtolower($extension);
        $filePath = $uploadDir . $fileName;
        $relativePath = 'Images/services/' . $fileName;
        
        if (move_uploaded_file($tmpName, $filePath)) {
            // Guardar na tabela Media
            $db = getDatabaseConnection();
            try {
                $stmt = $db->prepare("INSERT INTO Media (service_id, path_, title) VALUES (?, ?, ?)");
                $stmt->execute([$serviceId, $relativePath, 'Imagem do serviço']);
                $results[] = ['success' => true, 'path' => $relativePath];
            } catch (PDOException $e) {
                error_log("Erro ao guardar imagem na BD: " . $e->getMessage());
            }
        }
    }
    
    return [
        'success' => !empty($results),
        'uploaded' => count($results),
        'files' => $results
    ];
}
?>