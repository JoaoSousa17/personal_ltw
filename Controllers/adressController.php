<?php
require_once(dirname(__FILE__) . '/../Models/Adress.php');
require_once(dirname(__FILE__) . '/../Database/connection.php');

/**
 * Obtém a morada de um utilizador pelo seu ID.
 *
 * @param int $userId ID do utilizador.
 * @return Address|null Objeto Address ou null se não encontrado.
 */
function getUserAddress($userId) {
    $db = getDatabaseConnection();
    return Address::findByUserId($db, $userId);
}

/**
 * Cria ou atualiza a morada de um utilizador.
 *
 * @param int $userId ID do utilizador.
 * @param array $addressData Dados da morada.
 * @return array Resultado da operação com status e mensagem.
 */
function createOrUpdateAddress($userId, $addressData) {
    try {
        $db = getDatabaseConnection();
        
        // Validar dados obrigatórios
        $validation = validateAddressData($addressData);
        if (!$validation['valid']) {
            return ['success' => false, 'message' => implode(' ', $validation['errors'])];
        }
        
        // Verificar se já existe uma morada para este utilizador
        $existingAddress = Address::findByUserId($db, $userId);
        
        if ($existingAddress) {
            // Atualizar morada existente
            $existingAddress->setStreet(trim($addressData['street']));
            $existingAddress->setDoorNum(trim($addressData['door_num']));
            $existingAddress->setFloor(trim($addressData['floor'] ?? ''));
            $existingAddress->setExtra(trim($addressData['extra'] ?? ''));
            $existingAddress->setDistrict(trim($addressData['district']));
            $existingAddress->setMunicipality(trim($addressData['municipality']));
            $existingAddress->setZipCode(Address::formatZipCode($addressData['zip_code']));
            
            if ($existingAddress->save()) {
                return ['success' => true, 'message' => 'Morada atualizada com sucesso!'];
            } else {
                return ['success' => false, 'message' => 'Erro ao atualizar a morada.'];
            }
        } else {
            // Criar nova morada
            $address = new Address($db);
            $address->setUserId($userId);
            $address->setStreet(trim($addressData['street']));
            $address->setDoorNum(trim($addressData['door_num']));
            $address->setFloor(trim($addressData['floor'] ?? ''));
            $address->setExtra(trim($addressData['extra'] ?? ''));
            $address->setDistrict(trim($addressData['district']));
            $address->setMunicipality(trim($addressData['municipality']));
            $address->setZipCode(Address::formatZipCode($addressData['zip_code']));
            
            if ($address->save()) {
                return ['success' => true, 'message' => 'Morada adicionada com sucesso!'];
            } else {
                return ['success' => false, 'message' => 'Erro ao criar a morada.'];
            }
        }
        
    } catch (Exception $e) {
        error_log("Erro ao criar/atualizar morada: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erro interno do sistema.'];
    }
}

/**
 * Remove a morada de um utilizador.
 *
 * @param int $userId ID do utilizador.
 * @return array Resultado da operação.
 */
function deleteUserAddress($userId) {
    try {
        $db = getDatabaseConnection();
        $address = Address::findByUserId($db, $userId);
        
        if ($address) {
            if ($address->delete()) {
                return ['success' => true, 'message' => 'Morada removida com sucesso!'];
            } else {
                return ['success' => false, 'message' => 'Erro ao remover a morada.'];
            }
        } else {
            return ['success' => false, 'message' => 'Nenhuma morada encontrada para remover.'];
        }
        
    } catch (Exception $e) {
        error_log("Erro ao remover morada: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erro interno do sistema.'];
    }
}

/**
 * Obtém todas as moradas de um utilizador.
 *
 * @param int $userId ID do utilizador.
 * @return array Lista de moradas do utilizador.
 */
function getAllUserAddresses($userId) {
    $db = getDatabaseConnection();
    return Address::getAllByUserId($db, $userId);
}

/**
 * Valida os dados da morada antes da criação/atualização.
 *
 * @param array $data Dados a serem validados.
 * @return array Resultado da validação.
 */
function validateAddressData($data) {
    $errors = [];
    
    // Validar rua
    if (empty($data['street']) || strlen(trim($data['street'])) < 3) {
        $errors[] = 'A rua deve ter pelo menos 3 caracteres.';
    }
    
    // Validar número da porta
    if (empty($data['door_num']) || strlen(trim($data['door_num'])) < 1) {
        $errors[] = 'O número da porta é obrigatório.';
    }
    
    // Validar distrito
    if (empty($data['district']) || strlen(trim($data['district'])) < 2) {
        $errors[] = 'O distrito deve ter pelo menos 2 caracteres.';
    }
    
    // Validar município
    if (empty($data['municipality']) || strlen(trim($data['municipality'])) < 2) {
        $errors[] = 'O município deve ter pelo menos 2 caracteres.';
    }
    
    // Validar código postal
    if (empty($data['zip_code'])) {
        $errors[] = 'O código postal é obrigatório.';
    } else {
        $formattedZip = Address::formatZipCode($data['zip_code']);
        if (!Address::validateZipCode($formattedZip)) {
            $errors[] = 'Código postal inválido. Use o formato XXXX-XXX.';
        }
    }
    
    // Validar andar (se fornecido)
    if (!empty($data['floor']) && strlen(trim($data['floor'])) > 10) {
        $errors[] = 'Andar não pode ter mais de 10 caracteres.';
    }
    
    // Validar informações extra (se fornecidas)
    if (!empty($data['extra']) && strlen(trim($data['extra'])) > 255) {
        $errors[] = 'Informações adicionais são muito extensas (máximo 255 caracteres).';
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

/**
 * Obtém lista de distritos portugueses.
 *
 * @return array Lista de distritos.
 */
function getPortugueseDistricts() {
    return [
        'Aveiro', 'Beja', 'Braga', 'Bragança', 'Castelo Branco', 'Coimbra',
        'Évora', 'Faro', 'Guarda', 'Leiria', 'Lisboa', 'Portalegre',
        'Porto', 'Santarém', 'Setúbal', 'Viana do Castelo', 'Vila Real',
        'Viseu', 'Açores', 'Madeira'
    ];
}

/**
 * Obtém municípios por distrito (versão simplificada).
 * Em implementação real, seria obtido de uma base de dados.
 *
 * @param string $district Nome do distrito.
 * @return array Lista de municípios do distrito.
 */
function getMunicipalitiesByDistrict($district) {
    // Implementação simplificada - em produção seria obtido da base de dados
    $municipalities = [
        'Porto' => ['Porto', 'Vila Nova de Gaia', 'Matosinhos', 'Maia', 'Gondomar', 'Valongo', 'Póvoa de Varzim', 'Vila do Conde'],
        'Lisboa' => ['Lisboa', 'Sintra', 'Cascais', 'Loures', 'Amadora', 'Oeiras', 'Odivelas', 'Almada'],
        'Braga' => ['Braga', 'Guimarães', 'Barcelos', 'Famalicão', 'Esposende', 'Póvoa de Lanhoso'],
        'Aveiro' => ['Aveiro', 'Águeda', 'Ovar', 'Ílhavo', 'Santa Maria da Feira', 'São João da Madeira'],
        'Coimbra' => ['Coimbra', 'Figueira da Foz', 'Cantanhede', 'Montemor-o-Velho', 'Penacova'],
        'Faro' => ['Faro', 'Portimão', 'Lagos', 'Olhão', 'Loulé', 'Albufeira', 'Vila Real de Santo António']
    ];
    
    return $municipalities[$district] ?? [];
}

/**
 * Verifica se um utilizador tem morada cadastrada.
 *
 * @param int $userId ID do utilizador.
 * @return bool True se tiver morada, false caso contrário.
 */
function userHasAddress($userId) {
    $address = getUserAddress($userId);
    return $address !== null;
}

/**
 * Obtém a morada formatada de um utilizador.
 *
 * @param int $userId ID do utilizador.
 * @return string|null Morada formatada ou null se não encontrada.
 */
function getFormattedUserAddress($userId) {
    $address = getUserAddress($userId);
    return $address ? $address->getFormattedAddress() : null;
}

// Processamento de requisições HTTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_address') {
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = 'Utilizador não autenticado.';
        header("Location: /Views/auth.php");
        exit;
    }

    $currentUserId = $_SESSION['user_id'];
    $isAdmin = $_SESSION['is_admin'] ?? false;
    $targetUserId = isset($_POST['target_user_id']) ? intval($_POST['target_user_id']) : $currentUserId;

    // Verificar permissões
    if ($targetUserId !== $currentUserId && !$isAdmin) {
        $_SESSION['error'] = 'Não tem permissão para editar esta morada.';
        header("Location: /Views/profile/profile.php");
        exit;
    }

    $addressData = [
        'street' => $_POST['street'] ?? '',
        'door_num' => $_POST['door_num'] ?? '',
        'floor' => $_POST['floor'] ?? '',
        'extra' => $_POST['extra'] ?? '',
        'district' => $_POST['district'] ?? '',
        'municipality' => $_POST['municipality'] ?? '',
        'zip_code' => $_POST['zip_code'] ?? ''
    ];

    // Filtrar campos vazios opcionais
    $addressData = array_filter($addressData, function($value, $key) {
        return $key === 'floor' || $key === 'extra' || !empty($value);
    }, ARRAY_FILTER_USE_BOTH);

    // Verificar se existem dados suficientes para criar/atualizar
    $requiredFields = ['street', 'door_num', 'district', 'municipality', 'zip_code'];
    $hasRequiredData = true;
    foreach ($requiredFields as $field) {
        if (empty($addressData[$field])) {
            $hasRequiredData = false;
            break;
        }
    }

    if ($hasRequiredData) {
        $result = createOrUpdateAddress($targetUserId, $addressData);
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
    } else {
        $_SESSION['error'] = 'Preencha todos os campos obrigatórios da morada.';
    }

    $redirectUrl = "/Views/profile/editProfile.php";
    if ($targetUserId !== $currentUserId) {
        $redirectUrl .= "?id=" . $targetUserId;
    }
    
    header("Location: " . $redirectUrl);
    exit;
}

// Processamento para remoção de morada
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_address') {
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = 'Utilizador não autenticado.';
        header("Location: /Views/auth.php");
        exit;
    }

    $currentUserId = $_SESSION['user_id'];
    $isAdmin = $_SESSION['is_admin'] ?? false;
    $targetUserId = isset($_POST['target_user_id']) ? intval($_POST['target_user_id']) : $currentUserId;

    // Verificar permissões
    if ($targetUserId !== $currentUserId && !$isAdmin) {
        $_SESSION['error'] = 'Não tem permissão para remover esta morada.';
        header("Location: /Views/profile/profile.php");
        exit;
    }

    $result = deleteUserAddress($targetUserId);
    
    if ($result['success']) {
        $_SESSION['success'] = $result['message'];
    } else {
        $_SESSION['error'] = $result['message'];
    }

    $redirectUrl = "/Views/profile/editProfile.php";
    if ($targetUserId !== $currentUserId) {
        $redirectUrl .= "?id=" . $targetUserId;
    }
    
    header("Location: " . $redirectUrl);
    exit;
}
?>