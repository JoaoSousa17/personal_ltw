<?php
require_once(dirname(__FILE__) . '/../Models/Contact.php');
require_once(dirname(__FILE__) . '/../Database/connection.php');

/**
 * Processa uma nova mensagem de contacto.
 *
 * @param string $name Nome do remetente.
 * @param string $email Email do remetente.
 * @param string $phone Telefone do remetente (opcional).
 * @param string $subject Assunto da mensagem.
 * @param string $message Conteúdo da mensagem.
 * @return bool True se guardado com sucesso, False caso contrário.
 */
function submitContactForm($name, $email, $phone, $subject, $message) {
    $db = getDatabaseConnection();
    
    $contact = new Contact($db);
    $contact->setName(trim($name));
    $contact->setEmail(trim($email));
    $contact->setPhone(trim($phone));
    $contact->setSubject(trim($subject));
    $contact->setMessage(trim($message));
    
    return $contact->save();
}

/**
 * Obtém todas as mensagens de contacto.
 *
 * @return array Lista de objetos Contact.
 */
function getAllContacts() {
    $db = getDatabaseConnection();
    return Contact::getAllContacts($db);
}

/**
 * Obtém mensagens de contacto não lidas.
 *
 * @return array Lista de objetos Contact não lidos.
 */
function getUnreadContacts() {
    $db = getDatabaseConnection();
    return Contact::getUnreadContacts($db);
}

/**
 * Obtém uma mensagem de contacto pelo ID.
 *
 * @param int $contactId ID da mensagem.
 * @return Contact|null Objeto Contact ou null se não encontrado.
 */
function getContactById($contactId) {
    $db = getDatabaseConnection();
    return Contact::findById($db, $contactId);
}

/**
 * Marca uma mensagem como lida.
 *
 * @param int $contactId ID da mensagem.
 * @return bool True se marcado com sucesso, False caso contrário.
 */
function markContactAsRead($contactId) {
    $contact = getContactById($contactId);
    return $contact ? $contact->markAsRead() : false;
}

/**
 * Adiciona uma resposta de administrador a uma mensagem.
 *
 * @param int $contactId ID da mensagem.
 * @param string $response Resposta do administrador.
 * @return bool True se resposta adicionada com sucesso, False caso contrário.
 */
function addAdminResponse($contactId, $response) {
    $contact = getContactById($contactId);
    return $contact ? $contact->addAdminResponse($response) : false;
}

/**
 * Remove uma mensagem de contacto.
 *
 * @param int $contactId ID da mensagem.
 * @return bool True se removido com sucesso, False caso contrário.
 */
function deleteContact($contactId) {
    $contact = getContactById($contactId);
    return $contact ? $contact->delete() : false;
}

/**
 * Conta o número total de mensagens.
 *
 * @return int Número total de mensagens.
 */
function countTotalContacts() {
    $db = getDatabaseConnection();
    return Contact::countContacts($db);
}

/**
 * Conta o número de mensagens não lidas.
 *
 * @return int Número de mensagens não lidas.
 */
function countUnreadContacts() {
    $db = getDatabaseConnection();
    return Contact::countUnreadContacts($db);
}

/**
 * Valida os dados do formulário de contacto.
 *
 * @param array $data Dados do formulário.
 * @return array Array com 'valid' (bool) e 'errors' (array).
 */
function validateContactForm($data) {
    $errors = [];
    
    // Validar nome
    if (empty($data['name']) || strlen(trim($data['name'])) < 2) {
        $errors[] = 'Nome deve ter pelo menos 2 caracteres.';
    }
    
    // Validar email
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email inválido.';
    }
    
    // Validar assunto
    if (empty($data['subject']) || strlen(trim($data['subject'])) < 5) {
        $errors[] = 'Assunto deve ter pelo menos 5 caracteres.';
    }
    
    // Validar mensagem
    if (empty($data['message']) || strlen(trim($data['message'])) < 10) {
        $errors[] = 'Mensagem deve ter pelo menos 10 caracteres.';
    }
    
    // Validar telefone (se fornecido)
    if (!empty($data['phone']) && strlen(trim($data['phone'])) < 9) {
        $errors[] = 'Número de telefone deve ter pelo menos 9 dígitos.';
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

/**
 * Envia email de confirmação ao remetente (funcionalidade futura).
 *
 * @param string $email Email do remetente.
 * @param string $name Nome do remetente.
 * @return bool True se email enviado com sucesso.
 */
function sendConfirmationEmail($email, $name) {
    // Implementação futura para envio de emails
    // Por agora, apenas simula o envio
    return true;
}

/**
 * Envia notificação aos administradores sobre nova mensagem (funcionalidade futura).
 *
 * @param Contact $contact Objeto da mensagem.
 * @return bool True se notificação enviada com sucesso.
 */
function notifyAdminsNewContact($contact) {
    // Implementação futura para notificação de admins
    // Por agora, apenas simula a notificação
    return true;
}

// Processamento de requisições HTTP
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    
    // Obter dados do formulário
    $formData = [
        'name' => $_POST['name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'subject' => $_POST['subject'] ?? '',
        'message' => $_POST['message'] ?? ''
    ];
    
    // Verificar se todos os campos obrigatórios estão preenchidos
    if (empty($formData['name']) || empty($formData['email']) || 
        empty($formData['subject']) || empty($formData['message'])) {
        $_SESSION['contact_error'] = 'Por favor, preencha todos os campos obrigatórios.';
        header("Location: /Views/staticPages/contact.php");
        exit;
    }
    
    // Validar dados do formulário
    $validation = validateContactForm($formData);
    
    if (!$validation['valid']) {
        $_SESSION['contact_error'] = implode('<br>', $validation['errors']);
        $_SESSION['contact_form_data'] = $formData; // Preservar dados do formulário
        header("Location: /Views/staticPages/contact.php");
        exit;
    }
    
    // Tentar guardar a mensagem
    $success = submitContactForm(
        $formData['name'],
        $formData['email'],
        $formData['phone'],
        $formData['subject'],
        $formData['message']
    );
    
    if ($success) {
        $_SESSION['contact_success'] = 'Mensagem enviada com sucesso! Entraremos em contacto brevemente.';
        unset($_SESSION['contact_form_data']); // Limpar dados do formulário
        
        // Enviar email de confirmação (funcionalidade futura)
        // sendConfirmationEmail($formData['email'], $formData['name']);
        
        // Notificar administradores (funcionalidade futura)
        // notifyAdminsNewContact($contact);
    } else {
        $_SESSION['contact_error'] = 'Erro ao enviar mensagem. Por favor, tente novamente.';
        $_SESSION['contact_form_data'] = $formData; // Preservar dados do formulário
    }
    
    header("Location: /Views/staticPages/contact.php");
    exit;
}

// Processar requisições para marcar como lida (AJAX ou Admin)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'mark_read') {
    session_start();
    
    // Verificar se é administrador
    if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
        http_response_code(403);
        echo json_encode(['error' => 'Acesso negado']);
        exit;
    }
    
    $contactId = $_GET['contact_id'] ?? 0;
    $success = markContactAsRead($contactId);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => $success]);
    exit;
}

// Processar requisições para adicionar resposta de admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'admin_response') {
    session_start();
    
    // Verificar se é administrador
    if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
        $_SESSION['error'] = 'Acesso negado.';
        header("Location: /Views/mainPage.php");
        exit;
    }
    
    $contactId = $_POST['contact_id'] ?? 0;
    $response = $_POST['response'] ?? '';
    
    if ($contactId && !empty($response)) {
        $success = addAdminResponse($contactId, $response);
        
        if ($success) {
            $_SESSION['success'] = 'Resposta adicionada com sucesso.';
        } else {
            $_SESSION['error'] = 'Erro ao adicionar resposta.';
        }
    } else {
        $_SESSION['error'] = 'Dados inválidos.';
    }
    
    header("Location: /Views/admin/contacts.php");
    exit;
}
?>