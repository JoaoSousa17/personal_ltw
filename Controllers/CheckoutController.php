<?php
session_start();
require_once(dirname(__FILE__) . '/../Controllers/userController.php');
require_once(dirname(__FILE__) . '/../Controllers/serviceController.php');
require_once(dirname(__FILE__) . '/../Controllers/distancesCalculationController.php');
require_once(dirname(__FILE__) . '/../Controllers/orderProcessingController.php');
require_once(dirname(__FILE__) . '/../Utils/session.php');
require_once(dirname(__FILE__) . '/../Database/connection.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Método não permitido.";
    exit;
}

// Validação dos campos obrigatórios
if (
    empty($_POST['name']) ||
    empty($_POST['email']) ||
    empty($_POST['terms']) ||
    !isset($_POST['total_price'], $_POST['amount_paid'])
) {
    $_SESSION['error'] = "Preencha todos os campos obrigatórios.";
    header("Location: ../Views/checkout.php");
    exit;
}

// Verificar se o utilizador está logado
if (!isUserLoggedIn()) {
    $_SESSION['error'] = "Deve fazer login para finalizar a compra.";
    header("Location: ../Views/auth.php");
    exit;
}

$userId = getCurrentUserId();

// Verificar se existe carrinho ou dados do carrinho
$cartData = null;
if (isset($_POST['cart_data'])) {
    $cartData = json_decode($_POST['cart_data'], true);
} elseif (isset($_SESSION['cart'])) {
    $cartData = $_SESSION['cart'];
}

if (!$cartData || empty($cartData)) {
    $_SESSION['error'] = "Carrinho vazio. Adicione itens antes de finalizar a compra.";
    header("Location: ../Views/cart.php");
    exit;
}

// Recolher dados do formulário
$customerData = [
    'name' => trim($_POST['name']),
    'email' => trim($_POST['email']),
    'phone' => trim($_POST['phone'] ?? ''),
    'address' => trim($_POST['address'] ?? ''),
    'total_price' => floatval($_POST['total_price']),
    'amount_paid' => floatval($_POST['amount_paid']),
    'currency_code' => $_POST['currency_code'] ?? 'eur',
    'currency_symbol' => $_POST['currency_symbol'] ?? '€',
    'notes' => trim($_POST['notes'] ?? '')
];

// Processar checkout
$result = processCartCheckout($userId, $cartData, $customerData);

if ($result['success']) {
    // Limpar o carrinho
    unset($_SESSION['cart']);
    
    // Guardar dados para a página de sucesso
    $_SESSION['checkout_success'] = $result['data'];
    $_SESSION['success'] = $result['message'];
    
    header("Location: ../Views/mainPage.php");
    exit;
} else {
    $_SESSION['error'] = $result['message'];
    header("Location: ../Views/checkout.php");
    exit;
}

/**
 * Processa o checkout do carrinho criando registos Request para cada item
 */
function processCartCheckout($userId, $cartItems, $customerData) {
    try {
        $db = getDatabaseConnection();
        $db->beginTransaction();
        
        $processedRequests = [];
        
        foreach ($cartItems as $item) {
            $result = processCartItemToRequest($db, $userId, $item, $customerData);
            if ($result['success']) {
                $processedRequests[] = $result['data'];
            } else {
                throw new Exception("Erro ao processar item: " . $result['message']);
            }
        }
        
        $db->commit();
        
        return [
            'success' => true,
            'message' => "Encomenda processada com sucesso! Total de " . count($processedRequests) . " serviço(s) adquirido(s).",
            'data' => [
                'customer_data' => $customerData,
                'processed_requests' => $processedRequests,
                'total_items' => count($processedRequests)
            ]
        ];
        
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        error_log("Erro no checkout: " . $e->getMessage());
        return [
            'success' => false,
            'message' => "Erro ao processar a encomenda: " . $e->getMessage()
        ];
    }
}

/**
 * Processa um item individual do carrinho criando um registo Request
 */
function processCartItemToRequest($db, $userId, $item, $customerData) {
    try {
        // Verificar se é um pedido aceito ou um serviço novo
        if (isset($item['type']) && $item['type'] === 'order' && isset($item['order_id'])) {
            // É um pedido aceito - atualizar status
            return processExistingOrder($db, $userId, $item, $customerData);
        } else {
            // É um serviço novo - criar novo Request
            return processNewServiceRequest($db, $userId, $item, $customerData);
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Processa um pedido já existente (aceito) - atualiza status para 'paid'
 */
function processExistingOrder($db, $userId, $item, $customerData) {
    try {
        $orderId = $item['order_id'];
        
        // Verificar se o pedido existe e pertence ao utilizador
        $stmt = $db->prepare("
            SELECT r.id, r.service_data_id, r.status_, sd.user_id, s.name_ as service_name
            FROM Request r
            JOIN Service_Data sd ON r.service_data_id = sd.id
            JOIN Service_ s ON sd.service_id = s.id
            WHERE r.id = :request_id AND sd.user_id = :user_id
        ");
        $stmt->execute([
            ':request_id' => $orderId,
            ':user_id' => $userId
        ]);
        
        $request = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$request) {
            throw new Exception("Pedido não encontrado ou sem permissão");
        }
        
        // Atualizar status do Request para 'paid'
        $updateStmt = $db->prepare("UPDATE Request SET status_ = 'paid' WHERE id = :request_id");
        $updateStmt->execute([':request_id' => $orderId]);
        
        // Atualizar status do Service_Data para 'paid'
        $updateDataStmt = $db->prepare("UPDATE Service_Data SET status_ = 'paid' WHERE id = :service_data_id");
        $updateDataStmt->execute([':service_data_id' => $request['service_data_id']]);
        
        return [
            'success' => true,
            'data' => [
                'type' => 'existing_order',
                'request_id' => $orderId,
                'service_data_id' => $request['service_data_id'],
                'service_name' => $request['service_name'],
                'price' => $item['price'],
                'status' => 'paid'
            ]
        ];
        
    } catch (Exception $e) {
        throw new Exception("Erro ao processar pedido existente: " . $e->getMessage());
    }
}

/**
 * Processa um serviço novo criando Service_Data e Request
 */
function processNewServiceRequest($db, $userId, $item, $customerData) {
    try {
        $serviceId = $item['id'];
        
        // Verificar se o serviço existe
        $stmt = $db->prepare("SELECT id, name_, price_per_hour, duration FROM Service_ WHERE id = :service_id");
        $stmt->execute([':service_id' => $serviceId]);
        $service = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$service) {
            throw new Exception("Serviço não encontrado: ID " . $serviceId);
        }
        
        // Converter preço de volta para EUR (base da BD)
        $priceEUR = convertCurrencyToEUR($item['price'], $customerData['currency_code']);
        
        // Ajustar o preço dividindo por 60 (conversão de minutos para horas)
        $adjustedPriceEUR = $priceEUR / 60;
        
        // Criar Service_Data
        $stmt = $db->prepare("
            INSERT INTO Service_Data (user_id, service_id, travel_fee, final_price, status_) 
            VALUES (:user_id, :service_id, :travel_fee, :final_price, 'paid')
        ");
        
        $stmt->execute([
            ':user_id' => $userId,
            ':service_id' => $serviceId,
            ':travel_fee' => 0,
            ':final_price' => $adjustedPriceEUR
        ]);
        
        $serviceDataId = $db->lastInsertId();
        
        // Criar Request com message_id NULL e status 'paid'
        $requestStmt = $db->prepare("
            INSERT INTO Request (service_data_id, message_id, title, price, duration, status_) 
            VALUES (:service_data_id, NULL, :title, :price, :duration, 'paid')
        ");
        
        $requestStmt->execute([
            ':service_data_id' => $serviceDataId,
            ':title' => 'Pedido: ' . $service['name_'],
            ':price' => $adjustedPriceEUR,
            ':duration' => $service['duration']
        ]);
        
        $requestId = $db->lastInsertId();
        
        return [
            'success' => true,
            'data' => [
                'type' => 'new_service',
                'request_id' => $requestId,
                'service_data_id' => $serviceDataId,
                'service_name' => $service['name_'],
                'price' => $item['price'],
                'currency' => $customerData['currency_code'],
                'status' => 'paid'
            ]
        ];
        
    } catch (Exception $e) {
        throw new Exception("Erro ao processar novo serviço: " . $e->getMessage());
    }
}
?>
