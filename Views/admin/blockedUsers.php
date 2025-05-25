<?php
require_once(dirname(__FILE__)."/../../Templates/common_elems.php");
require_once(dirname(__FILE__)."/../../Templates/adminPages_elems.php");
require_once(dirname(__FILE__)."/../../Controllers/userController.php");
require_once(dirname(__FILE__)."/../../Controllers/UnblockAppealController.php");
require_once(dirname(__FILE__)."/../../Controllers/ReasonBlockController.php");

// Criar uma instância do controlador de usuários
$userController = new UserController();
// Chamar o método no objeto para obter usuários bloqueados
$blockedUsers = $userController->getAllBlockedUsers();

// Criar uma instância do controlador de pedidos de desbloqueio
$appealController = new UnblockAppealController();
// Obter todos os pedidos de desbloqueio
$appeals = $appealController->getAllAppeals();

drawHeader("Handee - Utilizadores Bloqueados", ["/Styles/admin.css", "/Styles/appeals.css"]);
?>
<main class="adminGeneral-container">
    <?php drawSectionHeader("Utilizadores Bloqueados", "Gerencia os utilizadores que estão bloqueados no sistema", true); ?>
    <?php drawBlockedUsersTable($blockedUsers) ?>

    <!-- Seção de Pedidos de Desbloqueio -->
    <?php drawSectionHeader("Pedidos de Desbloqueio", "Gerencie os pedidos de desbloqueio de usuários"); ?>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert-success">
            <?php if ($_GET['success'] === 'approved'): ?>
                Pedido aprovado com sucesso. Usuário foi desbloqueado.
            <?php elseif ($_GET['success'] === 'rejected'): ?>
                Pedido rejeitado com sucesso.
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert-error">
            Ocorreu um erro ao processar o pedido.
        </div>
    <?php endif; ?>
    
    <div class="appeal-filter-container">
        <button class="appeal-filter-btn active" data-filter="all">Todos os Pedidos</button>
        <button class="appeal-filter-btn" data-filter="pending">Pendentes</button>
        <button class="appeal-filter-btn" data-filter="approved">Aprovados</button>
        <button class="appeal-filter-btn" data-filter="rejected">Rejeitados</button>
    </div>
    
    <div class="appeals-container">
        <?php if (empty($appeals)): ?>
            <div class="no-appeals">
                <p>Não existem pedidos de desbloqueio no momento.</p>
            </div>
        <?php else: ?>
            <div class="appeals-grid">
                <?php foreach ($appeals as $appeal): ?>
                    <div class="appeal-card" data-status="<?php echo htmlspecialchars($appeal['status_']); ?>">
                        <div class="appeal-card-header">
                            <h3 class="appeal-title"><?php echo htmlspecialchars($appeal['title']); ?></h3>
                            <span class="appeal-status <?php echo htmlspecialchars($appeal['status_']); ?>">
                                <?php 
                                    $statusText = '';
                                    switch($appeal['status_']) {
                                        case 'pending':
                                            $statusText = 'Pendente';
                                            break;
                                        case 'approved':
                                            $statusText = 'Aprovado';
                                            break;
                                        case 'rejected':
                                            $statusText = 'Rejeitado';
                                            break;
                                    }
                                    echo $statusText;
                                ?>
                            </span>
                        </div>
                        <div class="appeal-card-body">
                            <div class="user-info">
                                <strong>Usuário:</strong> <?php echo htmlspecialchars($appeal['name_']); ?><br>
                                <strong>ID:</strong> <?php echo htmlspecialchars($appeal['user_id']); ?><br>
                                <strong>Username:</strong> <?php echo htmlspecialchars($appeal['username']); ?>
                            </div>
                            
                            <!-- Bloco de razão do bloqueio -->
                            <div class="block-reason">
                                <div class="reason-label">Razão do bloqueio:</div>
                                <div class="reason-content">
                                    <?php if (!empty($appeal['reason'])): ?>
                                        <div class="reason-main"><?php echo htmlspecialchars($appeal['reason']); ?></div>
                                        <?php if (!empty($appeal['extra_info'])): ?>
                                            <div class="reason-extra"><?php echo htmlspecialchars($appeal['extra_info']); ?></div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="reason-none">Não especificada</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="appeal-message">
                                <div class="message-label">Mensagem do usuário:</div>
                                <p><?php echo nl2br(htmlspecialchars($appeal['body_'])); ?></p>
                            </div>
                            <div class="appeal-date">
                                <small>Enviado em: <?php echo htmlspecialchars($appeal['date_']); ?> às <?php echo htmlspecialchars($appeal['time_']); ?></small>
                            </div>
                        </div>
                        <?php if ($appeal['status_'] === 'pending'): ?>
                            <div class="appeal-card-footer">
                                <form method="post" action="../../Controllers/UnblockAppealController.php">
                                    <input type="hidden" name="action" value="approve_appeal">
                                    <input type="hidden" name="appeal_id" value="<?php echo $appeal['id']; ?>">
                                    <button type="submit" class="appeal-btn approve">Aprovar</button>
                                </form>
                                <form method="post" action="../../Controllers/UnblockAppealController.php">
                                    <input type="hidden" name="action" value="reject_appeal">
                                    <input type="hidden" name="appeal_id" value="<?php echo $appeal['id']; ?>">
                                    <button type="submit" class="appeal-btn reject">Rejeitar</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<script src='/Scripts/blockAppealFilter.js'></script>

<?php drawFooter(); ?>