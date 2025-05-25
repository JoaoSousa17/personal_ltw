<?php
require_once(dirname(__FILE__)."/../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../Controllers/userController.php");

// Verificar se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: /Pages/login.php");
        exit();
    }
}

// Obter os dados do utilizador
$userId = $_SESSION['user_id'];
$user = getUserById($userId);

if (!$user) {
    header("Location: /Pages/mainPage.php");
    exit();
}

// Processar formulário de edição
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [];
    
    // Verificar o que foi alterado
    if (!empty($_POST['name']) && $_POST['name'] !== $user->getName()) {
        $data['name'] = $_POST['name'];
    }
    
    if (!empty($_POST['email']) && $_POST['email'] !== $user->getEmail()) {
        // Verificar se o email já existe
        $existingUser = getUserByUsername($_POST['email']);
        if ($existingUser && $existingUser->getId() !== $userId) {
            $error = 'Este email já está em uso.';
        } else {
            $data['email'] = $_POST['email'];
        }
    }
    
    if (!empty($_POST['username']) && $_POST['username'] !== $user->getUsername()) {
        // Verificar se o username já existe
        $existingUser = getUserByUsername($_POST['username']);
        if ($existingUser && $existingUser->getId() !== $userId) {
            $error = 'Este username já está em uso.';
        } else {
            $data['username'] = $_POST['username'];
        }
    }
    
    // Só atualizar a password se for fornecida
    if (!empty($_POST['password'])) {
        $data['password'] = $_POST['password'];
    }
    
    // Se não houver erros, atualizar o utilizador
    if (empty($error) && !empty($data)) {
        if (updateUser($userId, $data)) {
            $success = 'Perfil atualizado com sucesso!';
            // Atualizar os dados do utilizador para refletir as mudanças
            $user = getUserById($userId);
            
            // Atualizar a sessão se o username foi alterado
            if (isset($data['username'])) {
                $_SESSION['username'] = $data['username'];
            }
        } else {
            $error = 'Erro ao atualizar o perfil.';
        }
    }
    
    // Processar biografia e link
    if (isset($_POST['bio']) || isset($_POST['webLink'])) {
        $db = getDatabaseConnection();
        $updateFields = [];
        $params = [':id' => $userId];
        
        if (isset($_POST['bio'])) {
            $updateFields[] = "bio = :bio";
            $params[':bio'] = $_POST['bio'];
        }
        
        if (isset($_POST['webLink'])) {
            $updateFields[] = "web_link = :webLink";
            $params[':webLink'] = $_POST['webLink'];
        }
        
        if (!empty($updateFields)) {
            $query = "UPDATE User_ SET " . implode(', ', $updateFields) . " WHERE id = :id";
            $stmt = $db->prepare($query);
            if ($stmt->execute($params)) {
                $success = 'Perfil atualizado com sucesso!';
                $user = getUserById($userId);
            } else {
                $error = 'Erro ao atualizar o perfil.';
            }
        }
    }
}

drawHeader("Handee - Editar Perfil", ["/Styles/profile.css"]);
?>

<main>
    <section class="section-header">
        <h2>Editar Perfil</h2>
        <p>Atualize as suas informações pessoais</p>
        <a href="/Pages/profile.php" id="link-back-button">
            <img src="/Images/site/otherPages/back-icon.png" alt="Go Back" class="back-button">
        </a>
    </section>

    <section class="profile-section">
        <div class="profile-container">
            <div class="profile-content">
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                
                <form method="post" class="edit-form">
                    <div class="form-group">
                        <label for="name">Nome</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user->getName()); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user->getUsername()); ?>" required>
                        <p class="form-hint">Este será o seu identificador único no site</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user->getEmail()); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Nova Password</label>
                        <input type="password" id="password" name="password">
                        <p class="form-hint">Deixe em branco para manter a password atual</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="bio">Biografia</label>
                        <textarea id="bio" name="bio" rows="5"><?php echo htmlspecialchars($user->getBio() ?? ''); ?></textarea>
                        <p class="form-hint">Conte-nos um pouco sobre si</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="webLink">Website</label>
                        <input type="url" id="webLink" name="webLink" value="<?php echo htmlspecialchars($user->getWebLink() ?? ''); ?>" placeholder="https://exemplo.com">
                        <p class="form-hint">Link para o seu website pessoal ou portfolio</p>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="submit" class="btn-submit">Guardar Alterações</button>
                        <a href="/Pages/profile.php" class="btn-cancel">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<?php drawFooter(); ?>