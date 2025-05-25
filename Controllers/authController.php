<?php
require_once '../Database/connection.php';
require_once '../Utils/session.php';

function loginUser($email, $password) {
    $db = getDatabaseConnection();

    $stmt = $db->prepare("SELECT * FROM User_ WHERE email = :email");
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name_'];
        $_SESSION['is_admin'] = (bool)$user['is_admin'];
        
        // Usar a função do utils/session.php
        setCurrentUser($user['username']);

        $_SESSION['success'] = "Login feito com sucesso para: " . $user['name_'];

        header('Location: ../Views/mainPage.php');
        exit();
    } else {
        $_SESSION['error'] = 'Email ou senha incorretos';
        header('Location: ../Views/auth.php');
        exit();
    }
}

function registerUser($name, $email, $phone, $password, $password2) {
    $db = getDatabaseConnection();
    $username = explode('@', $email)[0];

    if ($password !== $password2) {
        $_SESSION['error'] = 'As senhas não coincidem';
        header('Location: ../Views/auth.php');
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Email inválido.';
        header('Location: ../Views/auth.php');
        exit();
    }

    if (strlen($phone) < 9) {
        $_SESSION['error'] = 'Número de telefone deve ter pelo menos 9 dígitos.';
        header('Location: ../Views/auth.php');
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $db->prepare("
            INSERT INTO User_ (name_, email, username, password_, phone_number)
            VALUES (:name, :email, :username, :password, :phone)
        ");
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':password', $hashedPassword);
        $stmt->bindValue(':phone', $phone);
        $stmt->execute();

        // Definir dados da sessão após registo bem-sucedido
        $userId = $db->lastInsertId();
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['is_admin'] = false; // Novos utilizadores não são admin por defeito
        
        // Usar a função do utils/session.php para definir o username
        setCurrentUser($username);

        $_SESSION['success'] = "Conta criada com sucesso!";
        header('Location: ../Views/mainPage.php');
        exit();
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), 'UNIQUE')) {
            $_SESSION['error'] = 'Email ou username já existente.';
        } else {
            $_SESSION['error'] = 'Erro: ' . $e->getMessage();
        }
        header('Location: ../Views/auth.php');
        exit();
    }
}

function logoutUser() {
    // Destruir todas as variáveis de sessão
    $_SESSION = array();

    // Se for para destruir a sessão completamente, também apagar o cookie de sessão
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Finalmente, destruir a sessão
    session_destroy();

    // Redirecionar para a página principal
    header('Location: ../Views/mainPage.php');
    exit();
}

// Processar requisições
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($action === 'login') {
        loginUser($email, $password);
    }

    if ($action === 'signup') {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password2 = trim($_POST['password2'] ?? '');
        registerUser($name, $email, $phone, $password, $password2);
    }
}

// Processar requisições GET (para logout)
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $action = $_GET['action'] ?? '';
    
    if ($action === 'logout') {
        logoutUser();
    }
}