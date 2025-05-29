<?php
require_once(dirname(__FILE__) . '/../Models/Feedback.php');
require_once(dirname(__FILE__) . '/../Database/connection.php');
require_once(dirname(__FILE__) . '/../Utils/session.php');

/**
 * Cria um novo feedback.
 *
 * @param int $userId ID do utilizador.
 * @param int $serviceId ID do serviço.
 * @param string $title Título do feedback.
 * @param string $description Descrição do feedback.
 * @param float $evaluation Avaliação de 0 a 5.
 * @return bool|Feedback Objeto criado ou false em caso de falha.
 */
function createFeedback($userId, $serviceId, $title, $description, $evaluation) {
    $db = getDatabaseConnection();

    $feedback = new Feedback($db);
    $feedback->setUserId($userId);
    $feedback->setServiceId($serviceId);
    $feedback->setTitle(trim($title));
    $feedback->setDescription(trim($description));
    $feedback->setEvaluation($evaluation);

    return $feedback->save() ? $feedback : false;
}

/**
 * Obtém um feedback pelo ID.
 *
 * @param int $feedbackId ID do feedback.
 * @return Feedback|null Objeto encontrado ou null.
 */
function getFeedbackById($feedbackId) {
    $db = getDatabaseConnection();
    return Feedback::findById($db, $feedbackId);
}

/**
 * Obtém todos os feedbacks de um serviço.
 *
 * @param int $serviceId ID do serviço.
 * @return array Lista de feedbacks.
 */
function getServiceFeedbacks($serviceId) {
    $db = getDatabaseConnection();
    return Feedback::getByServiceId($db, $serviceId);
}

/**
 * Obtém todos os feedbacks de um utilizador.
 *
 * @param int $userId ID do utilizador.
 * @return array Lista de feedbacks do utilizador.
 */
function getUserFeedbacks($userId) {
    $db = getDatabaseConnection();
    return Feedback::getByUserId($db, $userId);
}

/**
 * Verifica se um utilizador já deixou feedback para um serviço.
 *
 * @param int $userId ID do utilizador.
 * @param int $serviceId ID do serviço.
 * @return bool True se já existe feedback, false caso contrário.
 */
function hasUserFeedback($userId, $serviceId) {
    $db = getDatabaseConnection();
    return Feedback::userHasFeedback($db, $userId, $serviceId);
}

/**
 * Verifica se um utilizador contratou um serviço específico.
 *
 * @param int $userId ID do utilizador.
 * @param int $serviceId ID do serviço.
 * @return bool True se contratou, false caso contrário.
 */
function hasUserContractedService($userId, $serviceId) {
    try {
        $db = getDatabaseConnection();
        
        // Verificar se existe alguma Service_Data com status 'completed' ou 'paid'
        // para este utilizador e serviço
        $stmt = $db->prepare("
            SELECT COUNT(*) as count 
            FROM Service_Data sd
            JOIN Request r ON r.service_data_id = sd.id
            JOIN Message_ m ON r.message_id = m.id
            WHERE sd.service_id = :service_id 
              AND m.sender_id = :user_id 
              AND r.status_ IN ('completed', 'paid')
        ");
        
        $stmt->execute([
            ':service_id' => $serviceId,
            ':user_id' => $userId
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
        
    } catch (PDOException $e) {
        error_log("Erro ao verificar se utilizador contratou serviço: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtém a avaliação média de um serviço.
 *
 * @param int $serviceId ID do serviço.
 * @return array Array com 'average' e 'count'.
 */
function getServiceRating($serviceId) {
    $db = getDatabaseConnection();
    return Feedback::getServiceRating($db, $serviceId);
}

/**
 * Obtém os melhores feedbacks da plataforma.
 *
 * @param int $limit Limite de resultados.
 * @return array Lista dos melhores feedbacks.
 */
function getTopRatedFeedbacks($limit = 10) {
    $db = getDatabaseConnection();
    return Feedback::getTopRatedFeedbacks($db, $limit);
}

/**
 * Atualiza um feedback existente.
 *
 * @param int $feedbackId ID do feedback.
 * @param int $userId ID do utilizador (para verificação de permissão).
 * @param string $title Novo título.
 * @param string $description Nova descrição.
 * @param float $evaluation Nova avaliação.
 * @return bool True se atualizado com sucesso.
 */
function updateFeedback($feedbackId, $userId, $title, $description, $evaluation) {
    $feedback = getFeedbackById($feedbackId);
    
    if (!$feedback || $feedback->getUserId() !== $userId) {
        return false;
    }
    
    $feedback->setTitle(trim($title));
    $feedback->setDescription(trim($description));
    $feedback->setEvaluation($evaluation);
    
    return $feedback->save();
}

/**
 * Remove um feedback.
 *
 * @param int $feedbackId ID do feedback.
 * @param int $userId ID do utilizador (para verificação de permissão).
 * @return bool True se removido com sucesso.
 */
function deleteFeedback($feedbackId, $userId) {
    $feedback = getFeedbackById($feedbackId);
    
    if (!$feedback || $feedback->getUserId() !== $userId) {
        return false;
    }
    
    return $feedback->delete();
}

/**
 * Valida os dados do feedback.
 *
 * @param array $data Dados a validar.
 * @return array Array com 'valid' e 'errors'.
 */
function validateFeedbackData($data) {
    $errors = [];
    
    // Validar título
    if (empty($data['title']) || strlen(trim($data['title'])) < 5) {
        $errors[] = 'Título deve ter pelo menos 5 caracteres.';
    } elseif (strlen(trim($data['title'])) > 255) {
        $errors[] = 'Título não pode ter mais de 255 caracteres.';
    }
    
    // Validar avaliação
    if (!isset($data['evaluation']) || $data['evaluation'] === '') {
        $errors[] = 'Avaliação é obrigatória.';
    } else {
        $evaluation = floatval($data['evaluation']);
        if ($evaluation < 0.5 || $evaluation > 5) {
            $errors[] = 'Avaliação deve estar entre 0.5 e 5.';
        } elseif (($evaluation * 2) != (int)($evaluation * 2)) {
            $errors[] = 'Avaliação deve ser em incrementos de 0.5.';
        }
    }
    
    // Validar descrição (opcional mas com limite)
    if (!empty($data['description']) && strlen(trim($data['description'])) > 1000) {
        $errors[] = 'Descrição não pode ter mais de 1000 caracteres.';
    }
    
    // Validar service_id
    if (empty($data['service_id']) || !is_numeric($data['service_id'])) {
        $errors[] = 'Serviço inválido.';
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

/**
 * Processa estatísticas de feedbacks para um serviço.
 *
 * @param int $serviceId ID do serviço.
 * @return array Estatísticas detalhadas.
 */
function getServiceFeedbackStats($serviceId) {
    try {
        $db = getDatabaseConnection();
        
        // Obter distribuição de avaliações
        $stmt = $db->prepare("
            SELECT 
                evaluation,
                COUNT(*) as count
            FROM Feedback 
            WHERE service_id = :service_id
            GROUP BY evaluation
            ORDER BY evaluation DESC
        ");
        $stmt->execute([':service_id' => $serviceId]);
        $distribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Obter rating médio e total
        $rating = getServiceRating($serviceId);
        
        return [
            'average' => $rating['average'],
            'total_count' => $rating['count'],
            'distribution' => $distribution
        ];
        
    } catch (PDOException $e) {
        error_log("Erro ao obter estatísticas do feedback: " . $e->getMessage());
        return [
            'average' => 0,
            'total_count' => 0,
            'distribution' => []
        ];
    }
}

// ===== PROCESSAMENTO DE REQUISIÇÕES HTTP =====

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    session_start();
    $action = $_POST['action'];

    switch ($action) {
        case 'create_feedback':
            handleCreateFeedback();
            break;
        case 'update_feedback':
            handleUpdateFeedback();
            break;
        case 'delete_feedback':
            handleDeleteFeedback();
            break;
        default:
            $_SESSION['error'] = 'Ação não reconhecida.';
            header("Location: /Views/mainPage.php");
            exit();
    }
}

/**
 * Handler para criação de feedback.
 */
function handleCreateFeedback() {
    // Verificar autenticação
    if (!isUserLoggedIn()) {
        $_SESSION['error'] = 'Deve fazer login para deixar feedback.';
        header("Location: /Views/auth.php");
        exit();
    }

    $userId = getCurrentUserId();
    $serviceId = intval($_POST['service_id'] ?? 0);

    // Validar dados
    $validation = validateFeedbackData($_POST);
    if (!$validation['valid']) {
        $_SESSION['error'] = implode('<br>', $validation['errors']);
        $_SESSION['feedback_form_data'] = $_POST;
        header("Location: /Views/feedback/createFeedback.php?service_id=" . $serviceId);
        exit();
    }

    // Verificar se o serviço existe
    require_once(dirname(__FILE__) . '/serviceController.php');
    $service = getServiceById($serviceId);
    if (!$service) {
        $_SESSION['error'] = 'Serviço não encontrado.';
        header("Location: /Views/mainPage.php");
        exit();
    }

    // Verificar se já deixou feedback
    if (hasUserFeedback($userId, $serviceId)) {
        $_SESSION['error'] = 'Já deixou feedback para este serviço.';
        header("Location: /Views/product.php?id=" . $serviceId);
        exit();
    }

    // Verificar se contratou o serviço
    if (!hasUserContractedService($userId, $serviceId)) {
        $_SESSION['error'] = 'Só pode deixar feedback para serviços que contratou.';
        header("Location: /Views/product.php?id=" . $serviceId);
        exit();
    }

    // Criar feedback
    $result = createFeedback(
        $userId,
        $serviceId,
        $_POST['title'],
        $_POST['description'] ?? '',
        floatval($_POST['evaluation'])
    );

    if ($result) {
        $_SESSION['success'] = 'Feedback enviado com sucesso! Obrigado pela sua avaliação.';
        header("Location: /Views/product.php?id=" . $serviceId);
    } else {
        $_SESSION['error'] = 'Erro ao enviar o feedback. Tente novamente.';
        $_SESSION['feedback_form_data'] = $_POST;
        header("Location: /Views/feedback/createFeedback.php?service_id=" . $serviceId);
    }
    exit();
}

/**
 * Handler para atualização de feedback.
 */
function handleUpdateFeedback() {
    if (!isUserLoggedIn()) {
        $_SESSION['error'] = 'Deve fazer login.';
        header("Location: /Views/auth.php");
        exit();
    }

    $userId = getCurrentUserId();
    $feedbackId = intval($_POST['feedback_id'] ?? 0);

    // Validar dados
    $validation = validateFeedbackData($_POST);
    if (!$validation['valid']) {
        $_SESSION['error'] = implode('<br>', $validation['errors']);
        header("Location: /Views/feedback/editFeedback.php?id=" . $feedbackId);
        exit();
    }

    // Atualizar feedback
    $result = updateFeedback(
        $feedbackId,
        $userId,
        $_POST['title'],
        $_POST['description'] ?? '',
        floatval($_POST['evaluation'])
    );

    if ($result) {
        $_SESSION['success'] = 'Feedback atualizado com sucesso!';
    } else {
        $_SESSION['error'] = 'Erro ao atualizar o feedback ou não tem permissão.';
    }

    $serviceId = intval($_POST['service_id'] ?? 0);
    if ($serviceId > 0) {
        header("Location: /Views/product.php?id=" . $serviceId);
    } else {
        header("Location: /Views/profile/profile.php");
    }
    exit();
}

/**
 * Handler para eliminação de feedback.
 */
function handleDeleteFeedback() {
    if (!isUserLoggedIn()) {
        $_SESSION['error'] = 'Deve fazer login.';
        header("Location: /Views/auth.php");
        exit();
    }

    $userId = getCurrentUserId();
    $feedbackId = intval($_POST['feedback_id'] ?? 0);

    $result = deleteFeedback($feedbackId, $userId);

    if ($result) {
        $_SESSION['success'] = 'Feedback removido com sucesso.';
    } else {
        $_SESSION['error'] = 'Erro ao remover o feedback ou não tem permissão.';
    }

    $serviceId = intval($_POST['service_id'] ?? 0);
    if ($serviceId > 0) {
        header("Location: /Views/product.php?id=" . $serviceId);
    } else {
        header("Location: /Views/profile/profile.php");
    }
    exit();
}

// ===== PROCESSAMENTO DE REQUISIÇÕES AJAX =====

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    header('Content-Type: application/json');

    $action = $_GET['action'];

    switch ($action) {
        case 'get_service_rating':
            $serviceId = intval($_GET['service_id'] ?? 0);
            if ($serviceId > 0) {
                $rating = getServiceRating($serviceId);
                echo json_encode($rating);
            } else {
                echo json_encode(['error' => 'ID de serviço inválido']);
            }
            break;

        case 'get_service_feedbacks':
            $serviceId = intval($_GET['service_id'] ?? 0);
            if ($serviceId > 0) {
                $feedbacks = getServiceFeedbacks($serviceId);
                $feedbacksArray = array_map(function($feedback) {
                    $data = $feedback->toArray();
                    $data['user_name'] = $feedback->userName ?? 'Utilizador';
                    $data['formatted_date'] = $feedback->getFormattedDateTime();
                    $data['stars_html'] = $feedback->getStarsHtml();
                    return $data;
                }, $feedbacks);
                echo json_encode($feedbacksArray);
            } else {
                echo json_encode(['error' => 'ID de serviço inválido']);
            }
            break;

        case 'get_feedback_stats':
            $serviceId = intval($_GET['service_id'] ?? 0);
            if ($serviceId > 0) {
                $stats = getServiceFeedbackStats($serviceId);
                echo json_encode($stats);
            } else {
                echo json_encode(['error' => 'ID de serviço inválido']);
            }
            break;

        case 'check_user_feedback':
            session_start();
            if (!isUserLoggedIn()) {
                echo json_encode(['error' => 'Não autenticado']);
                break;
            }

            $userId = getCurrentUserId();
            $serviceId = intval($_GET['service_id'] ?? 0);
            
            if ($serviceId > 0) {
                $hasFeedback = hasUserFeedback($userId, $serviceId);
                $hasContracted = hasUserContractedService($userId, $serviceId);
                echo json_encode([
                    'has_feedback' => $hasFeedback,
                    'has_contracted' => $hasContracted,
                    'can_leave_feedback' => $hasContracted && !$hasFeedback
                ]);
            } else {
                echo json_encode(['error' => 'ID de serviço inválido']);
            }
            break;

        default:
            echo json_encode(['error' => 'Ação não reconhecida']);
    }
    exit();
}
?>
