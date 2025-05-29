<?php
require_once(dirname(__FILE__)."/../Models/Service.php");
require_once(dirname(__FILE__)."/../Database/connection.php");
require_once(dirname(__FILE__)."/../Utils/session.php");
require_once(dirname(__FILE__)."/categoriesController.php");

// ===== PROCESSAMENTO DE REQUISIÇÕES =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    switch ($action) {
        case 'create_service':
            handleCreateService();
            break;
        case 'toggle_status':
            handleToggleServiceStatus();
            break;
        case 'update_service':
            handleUpdateService();
            break;
        case 'get_order_for_cart':
            handleGetOrderForCart();
            break;
        default:
            $_SESSION['error'] = 'Ação não reconhecida.';
            header("Location: /Views/mainPage.php");
            exit();
    }
}

// ===== HANDLER PARA OBTER DADOS DO PEDIDO PARA CARRINHO =====
function handleGetOrderForCart() {
    header('Content-Type: application/json');
    
    if (!isUserLoggedIn()) {
        echo json_encode(["status" => "error", "message" => "Utilizador não autenticado"]);
        exit();
    }
    
    $orderId = intval($_POST['order_id'] ?? 0);
    $currentUserId = getCurrentUserId();
    
    if ($orderId <= 0) {
        echo json_encode(["status" => "error", "message" => "ID do pedido inválido"]);
        exit();
    }
    
    $orderData = getOrderForCart($orderId, $currentUserId);
    
    if (!$orderData) {
        echo json_encode(["status" => "error", "message" => "Pedido não encontrado ou sem permissão"]);
        exit();
    }
    
    // Adicionar ao carrinho
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Verificar se já existe no carrinho (usar order_id para esta verificação)
    $existsInCart = false;
    foreach ($_SESSION['cart'] as $item) {
        if (isset($item['order_id']) && $item['order_id'] == $orderId && $item['type'] == 'order') {
            $existsInCart = true;
            break;
        }
    }
    
    if ($existsInCart) {
        echo json_encode(["status" => "error", "message" => "Este pedido já está no carrinho"]);
        exit();
    }
    
    $product = [
        "id" => intval($orderData['service_id']), // USAR O service_id da base de dados
        "order_id" => $orderId,
        "type" => "order",
        "title" => $orderData['service_name'],
        "price" => floatval($orderData['final_price']),
        "image" => getFirstImageForService($orderData['service_id']),
        "seller" => $orderData['freelancer_name'],
        "category" => $orderData['category_name'],
        "duration" => $orderData['duration'],
        "date_" => $orderData['date_'],
        "time_" => $orderData['time_']
    ];
    
    $_SESSION['cart'][] = $product;
    
    echo json_encode([
        "status" => "success", 
        "message" => "Pedido adicionado ao carrinho com sucesso!",
        "total" => count($_SESSION['cart'])
    ]);
    exit();
}

// ===== FUNÇÃO PARA OBTER DADOS DO PEDIDO PARA CARRINHO =====
function getOrderForCart($orderId, $userId) {
    $db = getDatabaseConnection();
    
    $query = "
        SELECT 
            sd.id as order_id,
            sd.date_,
            sd.time_,
            sd.travel_fee,
            sd.final_price,
            sd.service_id,
            r.status_,
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
        JOIN Request r ON r.service_data_id = sd.id
        JOIN Message_ m ON r.message_id = m.id
        WHERE sd.id = :order_id 
          AND m.sender_id = :user_id
          AND r.status_ = 'accepted'
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute([':order_id' => $orderId, ':user_id' => $userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// ===== HANDLERS DE AÇÕES =====

function handleCreateService() {
    // Verificar autenticação
    if (!isUserLoggedIn()) {
        $_SESSION['error'] = 'Deve fazer login para criar um serviço.';
        header("Location: /Views/auth.php");
        exit();
    }
    
    $currentUserId = getCurrentUserId();
    
    // Preparar dados do serviço
    $serviceData = [
        'freelancer_id' => $currentUserId,
        'name' => trim($_POST['name'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'duration' => intval($_POST['duration'] ?? 0),
        'price_per_hour' => floatval($_POST['price_per_hour'] ?? 0),
        'promotion' => intval($_POST['promotion'] ?? 0),
        'category_id' => intval($_POST['category_id'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];
    
    // Debug temporário - remover depois
    error_log("DEBUG - Descrição recebida: '" . ($_POST['description'] ?? 'VAZIO') . "'");
    error_log("DEBUG - Descrição após trim: '" . $serviceData['description'] . "'");
    error_log("DEBUG - Tamanho da descrição: " . strlen($serviceData['description']));
    
    // Validar dados
    $validation = validateServiceData($serviceData);
    
    if (!$validation['valid']) {
        $_SESSION['error'] = implode('<br>', $validation['errors']);
        header("Location: /Views/orders/createService.php");
        exit();
    }
    
    // Criar o serviço
    $serviceId = createService($serviceData);
    
    if (!$serviceId) {
        $_SESSION['error'] = 'Erro ao criar o serviço. Tente novamente.';
        header("Location: /Views/orders/createService.php");
        exit();
    }
    
    // Atualizar status para freelancer automaticamente
    updateFreelancerStatus($currentUserId, true);
    
    // Processar upload de imagens se houver
    if (!empty($_FILES['images']['tmp_name'][0])) {
        $uploadResult = uploadServiceImages($serviceId, $_FILES['images']);
        if (!$uploadResult['success']) {
            $_SESSION['warning'] = 'Serviço criado com sucesso, mas houve problemas no upload das imagens.';
        }
    }
    
    // Definir mensagem de sucesso
    if (!isset($_SESSION['warning'])) {
        $_SESSION['success'] = 'Serviço criado com sucesso! Agora é um prestador de serviços.';
    }
    
    // Redirecionar para a página do produto criado
    header("Location: /Views/product.php?id=" . $serviceId);
    exit();
}

function handleToggleServiceStatus() {
    if (!isUserLoggedIn()) {
        $_SESSION['error'] = 'Deve fazer login.';
        header("Location: /Views/auth.php");
        exit();
    }
    
    $serviceId = intval($_POST['service_id'] ?? 0);
    $currentUserId = getCurrentUserId();
    
    $result = toggleServiceStatus($serviceId, $currentUserId);
    
    if ($result) {
        $_SESSION['success'] = 'Estado do serviço atualizado com sucesso!';
    } else {
        $_SESSION['error'] = 'Erro ao atualizar o serviço ou não tem permissão.';
    }
    
    header("Location: /Views/orders/myServices.php");
    exit();
}

// ===== FUNÇÕES DE GESTÃO DE SERVIÇOS =====

/**
 * Obter todos os serviços ativos
 */
function getAllServices() {
    $db = getDatabaseConnection();
    $query = "SELECT * FROM Service_ WHERE is_active = 1 ORDER BY name_";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    return formatResults($stmt);
}

/**
 * Obter serviço por ID
 */
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

/**
 * Pesquisar serviços por termo
 */
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

/**
 * Obter serviços por categoria
 */
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

/**
 * Filtrar serviços com várias condições
 */
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

/**
 * Cria um novo serviço
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
 */
function validateServiceData($data) {
    $errors = [];
    
    // Validar nome
    if (empty($data['name']) || strlen(trim($data['name'])) < 3) {
        $errors[] = 'Nome do serviço deve ter pelo menos 3 caracteres.';
    }
    
    // Validar descrição
    $description = trim($data['description'] ?? '');
    if (empty($description) || strlen($description) < 10) {
        $errors[] = 'Descrição deve ter pelo menos 10 caracteres. (Atual: ' . strlen($description) . ' caracteres)';
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
 * Atualiza o status de freelancer de um utilizador
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

// ===== FUNÇÕES DE UTILIZADOR FREELANCER =====

/**
 * Obter serviços de um freelancer específico
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
            COUNT(r.id) as total_orders,
            COUNT(CASE WHEN r.status_ = 'completed' THEN 1 END) as completed_orders,
            COALESCE(SUM(CASE WHEN r.status_ IN ('paid', 'completed') THEN r.price ELSE 0 END), 0) as total_earned
        FROM Service_ s
        JOIN Service_Data sd ON s.id = sd.service_id
        JOIN Request r ON r.service_data_id = sd.id
        WHERE s.freelancer_id = :user_id
    ";
    
    $stmt = $db->prepare($orderQuery);
    $stmt->execute([':user_id' => $userId]);
    $orderStats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return array_merge($serviceStats, $orderStats);
}

/**
 * Alternar o estado ativo/inativo de um serviço
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

// ===== FUNÇÕES DE PEDIDOS =====

/**
 * Obter pedidos de um utilizador específico
 */
function getUserOrders($userId) {
    $db = getDatabaseConnection();
    
    $query = "
        SELECT 
            sd.id as order_id,
            sd.service_id,
            sd.date_,
            sd.time_,
            sd.travel_fee,
            sd.final_price,
            r.status_,
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
        JOIN Request r ON r.service_data_id = sd.id
        JOIN Message_ m ON r.message_id = m.id
        WHERE m.sender_id = :user_id
        ORDER BY sd.date_ DESC, sd.time_ DESC
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute([':user_id' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


/**
 * Obter estatísticas dos pedidos de um utilizador
 */
function getOrderStats($userId) {
    $db = getDatabaseConnection();

    $query = "
        SELECT 
            COUNT(*) AS total_orders,
            COUNT(CASE WHEN r.status_ = 'completed' THEN 1 END) AS completed_orders,
            COUNT(CASE WHEN r.status_ = 'accepted' THEN 1 END) AS accepted_orders,
            COUNT(CASE WHEN r.status_ = 'paid' THEN 1 END) AS paid_orders,
            COALESCE(SUM(CASE WHEN r.status_ IN ('paid', 'completed') THEN r.price ELSE 0 END), 0) AS total_spent
        FROM Request r
        INNER JOIN Message_ m ON r.message_id = m.id
        WHERE m.sender_id = :user_id
          AND m.body_ LIKE 'Pedido:%'
    ";

    $stmt = $db->prepare($query);
    $stmt->execute([':user_id' => $userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


// ===== FUNÇÕES AUXILIARES =====

/**
 * Formatar resultados em um array
 */
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

/**
 * Calcular preço com desconto
 */
function calculateDiscountedPrice($price, $promotion) {
    if ($promotion > 0) {
        return $price - ($price * ($promotion / 100));
    }
    return $price;
}

/**
 * Calcular preço total baseado na duração
 */
function calculateTotalPrice($price_per_hour, $promotion, $duration) {
    $discounted_price = calculateDiscountedPrice($price_per_hour, $promotion);
    return $discounted_price * ($duration / 60); // Convertendo duração de minutos para horas
}

function handleUpdateService() {
    if (!isUserLoggedIn()) {
        $_SESSION['error'] = 'Deve fazer login para editar um serviço.';
        header("Location: /Views/auth.php");
        exit();
    }

    $currentUserId = getCurrentUserId();
    $serviceId = intval($_POST['service_id'] ?? 0);
    $existingService = getServiceById($serviceId);

    if (!$existingService || $existingService['freelancer_id'] !== $currentUserId) {
        $_SESSION['error'] = 'Serviço não encontrado ou não tem permissão.';
        header("Location: /Views/orders/myServices.php");
        exit();
    }

    $serviceData = [
        'id' => $serviceId,
        'freelancer_id' => $currentUserId,
        'name' => trim($_POST['name'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'duration' => intval($_POST['duration'] ?? 0),
        'price_per_hour' => floatval($_POST['price_per_hour'] ?? 0),
        'promotion' => intval($_POST['promotion'] ?? 0),
        'category_id' => intval($_POST['category_id'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];

    $validation = validateServiceData($serviceData);

    if (!$validation['valid']) {
        $_SESSION['error'] = implode('<br>', $validation['errors']);
        header("Location: /Views/orders/editService.php?id=" . $serviceId);
        exit();
    }

    $updated = updateService($serviceData);

    if (!$updated) {
        $_SESSION['error'] = 'Erro ao atualizar o serviço.';
        header("Location: /Views/orders/editService.php?id=" . $serviceId);
        exit();
    }

    $_SESSION['success'] = 'Serviço atualizado com sucesso.';
    header("Location: /Views/product.php?id=" . $serviceId);
    exit();
}

function updateService($data) {
    $db = getDatabaseConnection();

    try {
        $query = "UPDATE Service_ SET 
                    name_ = :name, 
                    description_ = :description, 
                    duration = :duration, 
                    price_per_hour = :price_per_hour, 
                    promotion = :promotion, 
                    category_id = :category_id, 
                    is_active = :is_active 
                  WHERE id = :id AND freelancer_id = :freelancer_id";
        
        $stmt = $db->prepare($query);
        return $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':duration' => $data['duration'],
            ':price_per_hour' => $data['price_per_hour'],
            ':promotion' => $data['promotion'],
            ':category_id' => $data['category_id'],
            ':is_active' => $data['is_active'],
            ':id' => $data['id'],
            ':freelancer_id' => $data['freelancer_id']
        ]);
    } catch (PDOException $e) {
        error_log("Erro ao atualizar serviço: " . $e->getMessage());
        return false;
    }
}

function getFirstImageForService($serviceId) {
    $directory = __DIR__ . '/../../Images/services/';
    $webPathPrefix = '/Images/services/';

    // Procurar ficheiros que comecem com service_<id>_
    $pattern = $directory . 'service_' . $serviceId . '_*.*';
    $files = glob($pattern);

    if ($files && count($files) > 0) {
        // Obter só o nome do ficheiro
        $fileName = basename($files[0]);
        return $webPathPrefix . $fileName;
    }

    // Se não encontrar nenhuma imagem, usar o placeholder
    return $webPathPrefix . 'placeholder.jpg';
}
?>