<?php
require_once(dirname(__FILE__) . '/../Models/User.php');

/**
 * Regista um novo utilizador no sistema.
 *
 * @param string $username Username do utilizador.
 * @param string $email Email do utilizador.
 * @param string $password Palavra-passe do utilizador.
 * @param string $name Nome do utilizador.
 * @param bool $isAdmin Define se o utilizador é administrador (por omissão: false).
 * @return bool|User Objeto User se registado com sucesso, False caso contrário.
 */
function registerUser($username, $email, $password, $name, $isAdmin = false) {
    $db = getDatabaseConnection();

    if (User::findByUsername($db, $username) || User::findByEmail($db, $email)) {
        return false;
    }

    $user = new User($db);
    $user->setUsername($username);
    $user->setEmail($email);
    $user->setPassword($password);
    $user->setName($name);
    $user->setIsAdmin($isAdmin);
    $user->setIsBlocked(false);

    return $user->save() ? $user : false;
}

/**
 * Autentica um utilizador no sistema.
 *
 * @param string $username Username do utilizador.
 * @param string $password Palavra-passe.
 * @return bool|User Objeto User se autenticado com sucesso, False caso contrário.
 */
function loginUser($username, $password) {
    $db = getDatabaseConnection();
    $user = User::authenticate($db, $username, $password);

    if ($user && !$user->getIsBlocked()) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $_SESSION['user_id'] = $user->getId();
        $_SESSION['username'] = $user->getUsername();
        $_SESSION['is_admin'] = $user->getIsAdmin();

        return $user;
    }

    return false;
}

/**
 * Termina a sessão do utilizador atual.
 *
 * @return bool True se sessão terminada com sucesso.
 */
function logoutUser() {
    if (session_status() === PHP_SESSION_NONE) session_start();

    $_SESSION = [];
    session_destroy();
    return true;
}

/**
 * Obtém todos os utilizadores registados.
 *
 * @return array Lista de objetos User.
 */
function getAllUsers() {
    $db = getDatabaseConnection();
    return User::getAllUsers($db);
}

/**
 * Obtém todos os utilizadores bloqueados.
 *
 * @return array Lista de objetos User bloqueados.
 */
function getAllBlockedUsers() {
    $db = getDatabaseConnection();
    return User::getAllBlockedUsers($db);
}

/**
 * Obtém um utilizador através do seu ID.
 *
 * @param int $id ID do utilizador.
 * @return User|null Objeto User se encontrado, null caso contrário.
 */
function getUserById($id) {
    $db = getDatabaseConnection();
    return User::findById($db, $id);
}

/**
 * Obtém um utilizador através do seu username.
 *
 * @param string $username Username do utilizador.
 * @return User|null Objeto User se encontrado, null caso contrário.
 */
function getUserByUsername($username) {
    $db = getDatabaseConnection();
    return User::findByUsername($db, $username);
}

/**
 * Atualiza os dados de um utilizador.
 *
 * @param int $id ID do utilizador.
 * @param array $data Dados a atualizar.
 * @return bool True se atualizado com sucesso, False caso contrário.
 */
function updateUser($id, $data) {
    $db = getDatabaseConnection();
    $user = User::findById($db, $id);
    if (!$user) return false;

    if (isset($data['username'])) $user->setUsername($data['username']);
    if (isset($data['email'])) $user->setEmail($data['email']);
    if (isset($data['password'])) $user->setPassword($data['password']);
    if (isset($data['name'])) $user->setName($data['name']);

    return $user->save();
}

/**
 * Bloqueia um utilizador.
 *
 * @param int $id ID do utilizador.
 * @return bool True se bloqueado com sucesso, False caso contrário.
 */
function blockUser($id) {
    $db = getDatabaseConnection();
    $user = User::findById($db, $id);
    return $user ? $user->blockUser() : false;
}

/**
 * Desbloqueia um utilizador.
 *
 * @param int $id ID do utilizador.
 * @return bool True se desbloqueado com sucesso, False caso contrário.
 */
function unblockUser($id) {
    $db = getDatabaseConnection();
    $user = User::findById($db, $id);
    return $user ? $user->unblockUser() : false;
}

/**
 * Elimina um utilizador do sistema.
 *
 * @param int $id ID do utilizador.
 * @return bool True se eliminado com sucesso, False caso contrário.
 */
function deleteUser($id) {
    $db = getDatabaseConnection();
    $user = User::findById($db, $id);
    return $user ? $user->delete() : false;
}

/**
 * Pesquisa utilizadores por email ou username.
 *
 * @param string $term Termo de pesquisa.
 * @return array Lista de objetos User.
 */
function searchUsers($term) {
    $db = getDatabaseConnection();
    $term = "%$term%";

    $stmt = $db->prepare("SELECT * FROM User_ WHERE email LIKE :term OR username LIKE :term ORDER BY id ASC");
    $stmt->execute([':term' => $term]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $users = [];
    foreach ($results as $row) {
        $user = new User($db);
        $user->setId($row['id']);
        $user->setUsername($row['username']);
        $user->setEmail($row['email']);
        $user->setName($row['name_']);
        if (isset($row['creation_date'])) $user->setRegisterDate($row['creation_date']);
        if (isset($row['bio'])) $user->setBio($row['bio']);
        if (isset($row['web_link'])) $user->setWebLink($row['web_link']);
        if (isset($row['is_freelancer'])) $user->setIsFreelancer($row['is_freelancer']);
        $user->setIsAdmin($row['is_admin']);
        $user->setIsBlocked($row['is_blocked']);
        $users[] = $user;
    }

    return $users;
}
