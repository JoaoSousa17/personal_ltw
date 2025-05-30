<?php
require_once(dirname(__FILE__) . '/../Database/connection.php');
require_once(dirname(__FILE__) . '/../Controllers/serviceController.php');
require_once(dirname(__FILE__) . '/../Controllers/distancesCalculationController.php');

/**
 * Processa um checkout completo
 */
function processCheckout($userId, $cartItems, $customerData) {
    try {
        $db = getDatabaseConnection();
        $db->beginTransaction();
        
        $processedOrders = [];
        
        foreach ($cartItems as $item) {
            $result = processCartItem($db, $userId, $item, $customerData);
            if ($result['success']) {
                $processedOrders[] = $result['data'];
            } else {
                throw new Exception("Erro ao processar item: " . $result['message']);
            }
        }
        
        $db->commit();
        
        return [
            'success' => true,
            'message' => "Encomenda processada com sucesso! Total de " . count($processedOrders) . " serviço(s) adquirido(s).",
            'data' => [
                'customer_data' => $customerData,
                'processed_orders' => $processedOrders,
                'total_items' => count($processedOrders)
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
 * Processa um item individual do carrinho
 */
function processCartItem($db, $userId, $item, $customerData) {
    try {
        // Verificar se é um serviço normal ou um pedido aceito
        if (isset($item['type']) && $item['type'] === 'order' && isset($item['order_id'])) {
            // É um pedido aceito (já existe Service_Data)
            return processAcceptedOrder($db, $userId, $item, $customerData);
        } else {
            // É um serviço normal (novo)
            return processNewService($db, $userId, $item, $customerData);
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Processa um pedido que já foi aceito (já tem Service_Data)
 */
function processAcceptedOrder($db, $userId, $item, $customerData) {
    try {
        // Verificar se o Service_Data existe e pertence ao utilizador
        $stmt = $db->prepare("
            SELECT sd.*, r.id as request_id, r.status_ as request_status
            FROM Service_Data sd
            LEFT JOIN Request r ON r.service_data_id = sd.id
            WHERE sd.id = :order_id AND sd.user_id = :user_id
        ");
        $stmt->execute([
            ':order_id' => $item['order_id'],
            ':user_id' => $userId
        ]);
        
        $serviceData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$serviceData) {
            throw new Exception("Pedido não encontrado ou sem permissão");
        }
        
        // Atualizar o status do Request para 'paid'
        if ($serviceData['request_id']) {
            $updateStmt = $db->prepare("UPDATE Request SET status_ = 'paid' WHERE id = :request_id");
            $updateStmt->execute([':request_id' => $serviceData['request_id']]);
        }
        
        // Atualizar o status do Service_Data para 'paid'
        $updateDataStmt = $db->prepare("UPDATE Service_Data SET status_ = 'paid' WHERE id = :service_data_id");
        $updateDataStmt->execute([':service_data_id' => $item['order_id']]);
        
        return [
            'success' => true,
            'data' => [
                'type' => 'accepted_order',
                'service_data_id' => $item['order_id'],
                'request_id' => $serviceData['request_id'],
                'service_name' => $item['title'],
                'price' => $item['price'],
                'status' => 'paid'
            ]
        ];
        
    } catch (Exception $e) {
        throw new Exception("Erro ao processar pedido aceito: " . $e->getMessage());
    }
}

/**
 * Processa um serviço novo (criar Service_Data e Request)
 */
function processNewService($db, $userId, $item, $customerData) {
    try {
        // Obter informações do serviço
        $serviceId = $item['id'];
        $service = getServiceById($serviceId);
        
        if (!$service) {
            throw new Exception("Serviço não encontrado: ID " . $serviceId);
        }
        
        // Calcular duração em minutos (assumir 1 hora se não especificado)
        $durationMinutes = isset($item['duration']) ? $item['duration'] : 60;
        
        // Converter preço de volta para EUR (base da BD)
        $priceEUR = convertCurrencyToEUR($item['price'], $customerData['currency_code']);
        
        // Criar Service_Data
        $stmt = $db->prepare("
            INSERT INTO Service_Data (user_id, service_id, travel_fee, final_price, status_) 
            VALUES (:user_id, :service_id, :travel_fee, :final_price, 'paid')
        ");
        
        $stmt->execute([
            ':user_id' => $userId,
            ':service_id' => $serviceId,
            ':travel_fee' => 0, // Por agora assumir 0
            ':final_price' => $priceEUR
        ]);
        
        $serviceDataId = $db->lastInsertId();
        
        // Criar Request sem message_id (NULL) e com status 'paid'
        $requestStmt = $db->prepare("
            INSERT INTO Request (service_data_id, message_id, title, price, duration, status_) 
            VALUES (:service_data_id, NULL, :title, :price, :duration, 'paid')
        ");
        
        $requestStmt->execute([
            ':service_data_id' => $serviceDataId,
            ':title' => 'Pedido: ' . $service['name'],
            ':price' => $priceEUR,
            ':duration' => $durationMinutes,
        ]);
        
        $requestId = $db->lastInsertId();
        
        return [
            'success' => true,
            'data' => [
                'type' => 'new_service',
                'service_data_id' => $serviceDataId,
                'request_id' => $requestId,
                'service_name' => $service['name'],
                'price' => $item['price'],
                'currency' => $customerData['currency_code'],
                'status' => 'paid'
            ]
        ];
        
    } catch (Exception $e) {
        throw new Exception("Erro ao processar novo serviço: " . $e->getMessage());
    }
}

/**
 * Converte um preço de qualquer moeda de volta para EUR
 */
function convertCurrencyToEUR($amount, $fromCurrency) {
    if (strtolower($fromCurrency) === 'eur') {
        return $amount;
    }
    
    $rates = [
        'eur' => 1.0,
        'usd' => 1.10,
        'gbp' => 0.85,
        'brl' => 6.20
    ];
    
    $fromCurrencyLower = strtolower($fromCurrency);
    
    if (!isset($rates[$fromCurrencyLower])) {
        return $amount; // Fallback
    }
    
    // Converter de volta para EUR dividindo pela taxa
    return $amount / $rates[$fromCurrencyLower];
}

/**
 * Obtém dados de checkout de sucesso da sessão
 */
function getCheckoutSuccessData() {
    if (!isset($_SESSION['checkout_success'])) {
        return null;
    }
    
    $data = $_SESSION['checkout_success'];
    // Limpar dados da sessão após uso
    unset($_SESSION['checkout_success']);
    
    return $data;
}