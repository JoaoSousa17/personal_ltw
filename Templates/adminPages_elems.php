<!----------------------------
Usados na página Admin Pannel
----------------------------->

<?php
/**
 * Desenha um cartão do painel de administração com ícone, título e link de redirecionamento.
 * Permite personalização do layout com largura total e tamanhos predefinidos (wide ou narrow).
 *
 * @param string $title      Título do cartão (e texto alternativo do ícone).
 * @param string $icon       Nome do ficheiro do ícone (para completar o caminho da imagem).
 * @param string $link       URL para onde o cartão redireciona ao ser clicado.
 * @param bool $fullWidth    Booleano que define se o cartão deve ocupar toda a largura disponível (por defeito: false).
 * @param string $size       Tamanho personalizado do cartão, com opções: 'wide', 'narrow' ou vazio.
 */
function drawAdminCard($title, $icon, $link, $fullWidth = false, $size = '') {
    /* Lógica para Gestão do tamanho/layout dos cartões na página */
    $className = "admin-card";
    
    if ($fullWidth) {   /* Largura total do container pai */
        $className .= " full-width";
    }
    
    if ($size === 'wide') { /* Largura de, aproximadamente, 2/3 do container pai */
        $className .= " wide-card";
    } else if ($size === 'narrow') {  /* Largura de, aproximadamente, 1/3 do container pai */
        $className .= " narrow-card";
    }?>

    <!-- Desenho efetivo do cartão -->
    <div class="<?php echo $className; ?>">
        <a href="<?php echo $link; ?>">
            <!-- Ícone -->
            <div class="card-icon">
                <img src="/Images/site/admin/<?php echo $icon; ?>.png" alt="<?php echo $title; ?>">
            </div>

            <!-- Título do cartão -->
            <h3><?php echo $title; ?></h3>
        </a>
    </div>
<?php } ?>

<!------------------------------------
Usados na página de Controlo de Users
------------------------------------->

<?php
/**
 * Desenha uma barra de pesquisa para procurar utilizadores por email ou username.
 *
 * @param string $searchTerm Termo de pesquisa atual.
 */
function drawSearchBar($searchTerm) { ?>
    <div class="search-container">
        <form action="" method="GET" class="search-form">
            <div class="search-input-wrapper">
                <!-- Secção Input dados para Pesquisa -->
                <input type="text" name="search" placeholder="Pesquisar por email ou username..." 
                    value="<?php echo htmlspecialchars($searchTerm); ?>" class="search-input">

                <!-- Ícone Lupa/Pesquisa -->
                <button type="submit" class="search-button">
                    <img src="/Images/site/header/search-icon.png" alt="Pesquisar" class="search-icon">
                </button>
            </div>
        </form>
    </div>
<?php }

/**
 * Exibe uma mensagem informativa com o termo pesquisado, incluindo um link para limpar a pesquisa.
 *
 * @param string $searchTerm Termo de pesquisa atual.
 */
function drawSearchInfoMessageSection($searchTerm) { ?>
    <?php if (!empty($searchTerm)): ?>
        <div class="search-results-info">
            <p>Resultados para: <strong><?php echo htmlspecialchars($searchTerm); ?></strong></p>   <!-- Se acontecer desformatação, remover parágrafo -->
            <a href="users.php" class="clear-search">Limpar pesquisa</a>
        </div>
    <?php endif;
}

/**
 * Gera uma tabela com a lista completa de utilizadores do site. Contempla colunas de dados como id, username, nome, email e data de criação da conta.
 * Adicionalmente, possuí colunas com botões para as seguintes ações: Aceder ao link disponibilizado pelo user (se definido), bloqueá-lo, ou apagar a conta.
 * Possuí tambem 3 colunas que indicam se se trata de um freelancer ou admin, e se se encontra bloqueado atualmente.
 * Também exibe mensagens apropriadas caso não haja resultados.
 *
 * @param string $searchTerm Termo de pesquisa atual.
 * @param array $users       Lista de objetos User, retornado por consulta da DB.
 */
function drawUsersTable($searchTerm, $users) { ?>
    <div class="users-table-container">
        <!-- Caso não exista qualquer utilizador registado na plataforma -->
        <?php if (empty($users)): ?>
            <div class="no-users">
                <?php if (!empty($searchTerm)): ?>
                    <p>Nenhum utilizador encontrado para a pesquisa.</p>
                <?php else: ?>
                    <p>Não existem utilizadores no sistema.</p>
                <?php endif; ?>
            </div>

        <!-- Caso de existência de utilizadores registados -->
        <?php else: ?>
            <table class="users-table">
                <!-- Nomes das colunas da tabela -->
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Data de Registro</th>
                        <th>Link</th>
                        <th>Freelancer</th>
                        <th>Admin</th>
                        <th>Bloqueado</th>
                        <th>Ações</th>
                        <th>Apagar</th>
                    </tr>
                </thead>

                <!-- Preenchimento do corpo da tabela, com os dados apropriados -->
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <!-- Gestão dos dados dos utilizadores -->
                            <td><?php echo htmlspecialchars($user->getId()); ?></td>
                            <td><?php echo htmlspecialchars($user->getUsername()); ?></td>
                            <td><?php echo htmlspecialchars($user->getName()); ?></td>
                            <td><?php echo htmlspecialchars($user->getEmail()); ?></td>
                            <td><?php echo htmlspecialchars($user->getRegisterDate()); ?></td>

                            <!-- Botão de acesso ao link disponibilizado no perfil do utilizador -->
                            <td>
                                <?php
                                    $webLink = $user->getWebLink();
                                    if (!empty($webLink)) {
                                        echo '<a href="' . htmlspecialchars($webLink) . '" target="_blank" class="web-link">';
                                        echo '<img src="/Images/site/admin/site.png" alt="Link externo" class="link-icon"></a>';
                                    } else {
                                        echo '<span class="empty-field">N/A</span>';
                                    }
                                ?>
                            </td>

                            <!-- Ícone indicador se se trata de um utilizador com anúncios publicados (freelancer) -->
                            <td>
                                <?php if ($user->getIsFreelancer()): ?>
                                    <span class="icon-yes"><img src="/Images/site/admin/right.png" alt="Sim" class="status-icon"></span>
                                <?php else: ?>
                                    <span class="icon-no"><img src="/Images/site/admin/wrong.png" alt="Não" class="status-icon"></span>
                                <?php endif; ?>
                            </td>

                            <!-- Ícone indicador se se trata de um admin -->
                            <td>
                                <?php if ($user->getIsAdmin()): ?>
                                    <span class="icon-yes"><img src="/Images/site/admin/right.png" alt="Sim" class="status-icon"></span>
                                <?php else: ?>
                                    <span class="icon-no"><img src="/Images/site/admin/wrong.png" alt="Não" class="status-icon"></span>
                                <?php endif; ?>
                            </td>

                            <!-- Ícone indicador se o utilizador se encontra bloqueado -->
                            <td>
                                <?php if ($user->getIsBlocked()): ?>
                                    <span class="icon-blocked"><img src="/Images/site/admin/right.png" alt="Bloqueado" class="status-icon"></span>
                                <?php else: ?>
                                    <span class="icon-active"><img src="/Images/site/admin/wrong.png" alt="Ativo" class="status-icon"></span>
                                <?php endif; ?>
                            </td>

                            <!-- Botão para bloquear ou desbloquear um utilizador, mediante o estado em que este se encontre -->
                            <td>
                                <!-- Caso de Utilizador Bloqueado -->
                                <?php if ($user->getIsBlocked()): ?>
                                    <form method="post">
                                        <input type="hidden" name="user_id" value="<?php echo $user->getId(); ?>">
                                        <input type="hidden" name="action" value="unblock_user">
                                        <button type="submit" class="action-button unblock-button">Desbloquear</button>
                                    </form>

                                <!-- Caso de Utilizador Não Bloqueado -->
                                <?php else: ?>
                                    <button type="button" class="action-button block-button"
                                        onclick="openBlockModal(<?php echo $user->getId(); ?>, '<?php echo addslashes($user->getUsername()); ?>')">Bloquear</button>
                                <?php endif; ?>
                            </td>
                            
                            <!-- Botão para apagar conta de Utilizador, a partir do Controller -->
                            <td>
                                <form method="post"
                                    onsubmit="return confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.');">
                                    <input type="hidden" name="user_id" value="<?php echo $user->getId(); ?>">
                                    <input type="hidden" name="action" value="delete_user">
                                    <button type="submit" class="action-button delete-button">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Seleção de Motivo e registo do Bloqueio de um user-->
    <div id="blockModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeBlockModal()">&times;</span>

            <!-- Título do cartão de Bloqueio -->
            <h2>Bloquear Usuário</h2>
            <p id="blockModalUsername"></p>

            <form id="blockForm" method="post">
                <input type="hidden" id="block_user_id" name="user_id" value="">
                <input type="hidden" name="action" value="block_user">

                <!-- Secção de Seleção do Motivo de Bloqueio  -->
                <div class="form-group">
                    <label for="block_reason">Motivo do Bloqueio:</label>
                    <select id="block_reason" name="block_reason" required class="form-input">
                        <option value="">Selecione um motivo</option>
                        <option value="Comportamento abusivo ou inapropriado">Comportamento abusivo ou inapropriado</option>
                        <option value="Atividades fraudulentas">Atividades fraudulentas</option>
                        <option value="Violação das regras da plataforma">Violação das regras da plataforma</option>
                        <option value="Problemas recorrentes em serviços">Problemas recorrentes em serviços</option>
                        <option value="Violação de privacidade ou segurança">Violação de privacidade ou segurança</option>
                        <option value="Inatividade prolongada com indícios de abandono">Inatividade prolongada com indícios de abandono</option>
                        <option value="Violação dos termos de serviço">Violação dos termos de serviço</option>
                        <option value="Outra">Outra</option>
                    </select>
                </div>

                <!-- Secção para acrescentar Informações Adicionais (opcional) que complementem a informação do motivo de Bloqueio -->
                <div class="form-group">
                    <label for="block_extra_info">Informações Adicionais:</label>
                    <textarea id="block_extra_info" name="block_extra_info" class="form-input" rows="4"
                        placeholder="Forneça mais detalhes sobre o motivo do bloqueio..."></textarea>
                </div>

                <!-- Botões para confirmar ou Cancelar ação -->
                <div class="form-actions">
                    <button type="button" class="cancel-button" onclick="closeBlockModal()">Cancelar</button>
                    <button type="submit" class="block-confirm-button">Confirmar Bloqueio</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Código JS para gestão do desenho do cartão de bloqueio de utilizador, num estilo pop-up -->
    <script src="/Scripts/userBlockCard.js"></script>
<?php } ?>

<!----------------------------------------------
Usados na página de Controlo de Users Bloqueados
------------------------------------------------>

<?php
/**
 * Desenha uma tabela com a lista de utilizadores bloqueados, com campos com id, username, email, nome motivo do bloqueio e informações adicionais.
 *
 * @param array $blockedUsers Lista de utilizadores bloqueados, objetos User, retornado por consulta da DB.
 */
function drawBlockedUsersTable($blockedUsers) { ?>
    <div class="blocked-users-table-container">
        <!-- Caso não exista qualquer utilizador bloqueado na plataforma -->
        <?php if (empty($blockedUsers)): ?>
            <div class="no-blocked-users">
                <p>Não existem utilizadores bloqueados no momento.</p>
            </div>

        <!-- Caso de existência de utilizadores bloqueados -->
        <?php else: ?>
            <table class="blocked-users-table">
                <!-- Nomes das colunas da tabela -->
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome de Utilizador</th>
                        <th>Email</th>
                        <th>Nome</th>
                        <th>Motivo do Bloqueio</th>
                        <th>Estado</th>
                    </tr>
                </thead>

                <!-- Preenchimento do corpo da tabela, com os dados apropriados -->
                <tbody>
                    <?php foreach ($blockedUsers as $user): 
                        $blockReason = getBlockReason($user->getId());
                    ?>
                        <tr>
                            <!-- Gestão dos dados dos utilizadores -->
                            <td><?php echo htmlspecialchars($user->getId()); ?></td>
                            <td><?php echo htmlspecialchars($user->getUsername()); ?></td>
                            <td><?php echo htmlspecialchars($user->getEmail()); ?></td>
                            <td><?php echo htmlspecialchars($user->getName()); ?></td>

                            <!-- Motivo de Bloqueio + Informações Adicionais (inicialmente escondidas) -->
                            <td>
                                <!-- Caso exista um motivo de Bloqueio registado -->
                                <?php if ($blockReason): ?>
                                    <div class="block-reason-display">
                                        <!-- Exibição da Razão -->
                                        <div class="reason-type">
                                            <?php echo htmlspecialchars($blockReason->getReason()); ?>
                                        </div>

                                        <!-- Exibição das Informações Adicionais, caso existam -->
                                        <?php if ($blockReason->getExtraInfo()): ?>
                                            <div class="reason-info-toggle" onclick="toggleExtraInfo(this)">+</div> <!-- Botão para exibir informação extra -->
                                            <div class="reason-extra-info" style="display: none;">
                                                <?php echo nl2br(htmlspecialchars($blockReason->getExtraInfo())); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                <!-- Caso o motivo de Bloqueio não esteja registado -->
                                <?php else: ?>
                                    <span class="empty-field">Não especificado</span>
                                <?php endif; ?>
                            </td>

                            <!-- Coluna estática, confirmando visualmente o estatuto de bloqueado do utilizador -->
                            <td><span class="status-blocked">Bloqueado</span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Código JS para exibir dinamicamente as informações adicionais de um dado Bloqueio -->
    <script src='/Scripts/aditionalInfoBlockedTable.js'></script>
<?php } ?>

<!-----------------------------------------
Usados na página de Controlo de Newsletter
------------------------------------------>

<?php
/**
 * Desenha uma tabela com as inscrições atuais na newsletter, com os dados id, email registado e um botão,
 * permitindo ao administrador anular cada uma individualmente.
 *
 * @param array $subscriptions Lista de inscrições, retornado por consulta da DB.
 */
function drawNewsletterTable($subscriptions) { ?>
    <div class="newsletter-table-container">
        <!-- Caso não exista qualquer registo da newsletter -->
        <?php if (empty($subscriptions)): ?>
            <div class="no-subscriptions">
                <p>Não existem inscrições na newsletter.</p>
            </div>

        <!-- Caso de existência de registos da newsletter -->
        <?php else: ?>
            <table class="newsletter-table">
                <!-- Nomes das colunas da tabela -->
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <!-- Preenchimento do corpo da tabela, com os dados apropriados -->
                <tbody>
                    <?php foreach ($subscriptions as $subscription): ?>
                        <tr>
                            <!-- Gestão dos dados dos registos -->
                            <td><?php echo htmlspecialchars($subscription['id']); ?></td>
                            <td><?php echo htmlspecialchars($subscription['email']); ?></td>

                            <!-- Botão para remover registo da Newsletter -->
                            <td>
                                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>"
                                      onsubmit="return confirm('Tem certeza que deseja anular esta inscrição?');">
                                    <input type="hidden" name="remove_id" value="<?php echo $subscription['id']; ?>">
                                    <button type="submit" class="delete-button">Anular Inscrição</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php } ?>

<!-----------------------------------------
Usados na página de Controlo de Categorias
------------------------------------------>

<?php
/**
 * Gera uma tabela com as categorias registadas no sistema,
 * incluindo imagem (se existir), número de serviços associados e botão de remoção.
 *
 * @param array $categories Lista de categorias, retornado por consulta da DB.
 */
function drawCategoriesTable($categories) { ?>
    <div class="category-table-container">
        <!-- Caso não exista qualquer categoria registada -->
        <?php if (empty($categories)): ?>
            <div class="no-categories">
                <p>Não existem categorias no sistema.</p>
            </div>

        <!-- Caso de existência de categorias registadas -->
        <?php else: ?>
            <table class="category-table">
                <!-- Nomes das colunas da tabela -->
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagem</th>
                        <th>Nome</th>
                        <th>Nº de Serviços</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <!-- Preenchimento do corpo da tabela, com os dados apropriados -->
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <!-- Coluna de id -->
                            <td><?php echo htmlspecialchars($category['id']); ?></td>

                            <!-- Coluna da foto representativa da categoria -->
                            <td>
                                <?php if (!empty($category['photo_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($category['photo_url']); ?>" 
                                        alt="<?php echo htmlspecialchars($category['name']); ?>" 
                                        class="category-thumbnail">
                                <?php else: ?>
                                    <span class="no-image">Sem imagem</span>
                                <?php endif; ?>
                            </td>

                            <!-- Colunas de dados Nome e contagem de Serviços disponíveis na Categoria -->
                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                            <td><?php echo getServiceCountByCategory($category['id']); ?></td>

                            <!-- Botão para apagar a categoria. Esta ação carece de dupla confirmação -->
                            <td>
                                <form method="POST" action=""
                                      onsubmit="return confirm('Tem certeza que deseja remover esta categoria? Todos os serviços relacionados serão afetados.');">
                                    <input type="hidden" name="remove_category" value="<?php echo $category['id']; ?>">
                                    <button type="submit" class="remove-button">Remover</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php }

/**
 * Desenha o formulário para adicionar uma nova categoria, requerendo nome da categoria e upload de uma imagem.
 */
function drawAddCategoryForm() { ?>
    <div class="add-category-form-container">
        <form method="POST" action="" class="add-category-form" enctype="multipart/form-data">
            <!-- Seleção Nome da Categoria -->
            <div class="form-group">
                <label for="category_name">Nome da Categoria:</label>
                <input type="text" id="category_name" name="category_name" required class="form-input" maxlength="100">
                <small>Insira o nome da nova categoria (máximo 100 caracteres)</small>
            </div>

            <!-- Upload da Imagem -->
            <div class="form-group">
                <label for="category_image">Imagem da Categoria:</label>
                <input type="file" id="category_image" name="category_image" required class="form-input" accept="image/*">
                <small>Selecione uma imagem para a categoria (JPEG, PNG, GIF ou WebP, máximo 5MB)</small>
            </div>

            <!-- Botão de Submissão do Formulário de Criação da Categoria -->
            <div class="form-actions">
                <button type="submit" name="add_category" class="submit-button">Adicionar Categoria</button>
            </div>
        </form>
    </div>
<?php } ?>

<!------------------------------------
Usados na página de Controlo de Admins
------------------------------------->

<?php
/**
 * Desenha a tabela com a lista de administradores registados no sistema,exibindo dados como id, username, nome, email, data de registo e 
 * estado (ativo/bloqueado) de cada um.
 *
 * @param array $admins Lista de objetos User com privilégios de administrador, retornado por consulta da DB.
 */
function drawAdminTable($admins) { ?>
    <div class="users-table-container">
        <!-- Caso não exista qualquer administrador registado -->
        <?php if (empty($admins)): ?>
            <div class="no-users">
                <p>Não existem administradores no sistema.</p>
            </div>

        <!-- Caso de existência de administradores registados -->
        <?php else: ?>
            <table class="users-table">
                <!-- Nomes das colunas da tabela -->
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Data de Registro</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <!-- Preenchimento do corpo da tabela, com os dados apropriados -->
                <tbody>
                    <?php foreach ($admins as $admin): ?>
                        <tr>
                            <!-- Colunas de id, username, nome, email e data de criação -->
                            <td><?php echo htmlspecialchars($admin->getId()); ?></td>
                            <td><?php echo htmlspecialchars($admin->getUsername()); ?></td>
                            <td><?php echo htmlspecialchars($admin->getName()); ?></td>
                            <td><?php echo htmlspecialchars($admin->getEmail()); ?></td>
                            <td><?php echo htmlspecialchars($admin->getRegisterDate()); ?></td>

                            <!-- Ícone representativo de se o administrador se encontra bloqueado (ou não) -->
                            <td>
                                <?php if ($admin->getIsBlocked()): ?>
                                    <span class="icon-blocked">
                                        <img src="/Images/site/admin/wrong.png" alt="Bloqueado" class="status-icon">
                                    </span>
                                <?php else: ?>
                                    <span class="icon-active">
                                        <img src="/Images/site/admin/right.png" alt="Ativo" class="status-icon">
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php }

/**
 * Exibe uma tabela com os resultados de uma pesquisa de utilizadores, com opção de promover a administrador.
 *
 * @param string $searchTerm     Termo de pesquisa utilizado.
 * @param array $searchResults   Lista de utilizadores correspondentes ao termo, retornado por consulta da DB.
 */
function drawSearchResultTable($searchTerm, $searchResults) { ?>
    <?php if (!empty($searchTerm)): 
        drawSearchInfoMessageSection($searchTerm)?>
        <!-- Caso não exista qualquer resultado para a pesquisa -->
        <?php if (empty($searchResults)): ?>
            <div class="no-users">
                <p>Nenhum usuário encontrado para a pesquisa.</p>
            </div>

        <!-- Caso de existência de resultados fruto da pesquisa -->
        <?php else: ?>
            <div class="users-table-container">
                <table class="users-table">
                    <!-- Nomes das colunas da tabela -->
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Admin</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <!-- Preenchimento do corpo da tabela, com os dados apropriados -->
                    <tbody>
                        <?php foreach ($searchResults as $user): ?>
                            <tr>
                                <!-- Colunas de id, username, nome, email -->
                                <td><?php echo htmlspecialchars($user->getId()); ?></td>
                                <td><?php echo htmlspecialchars($user->getUsername()); ?></td>
                                <td><?php echo htmlspecialchars($user->getName()); ?></td>
                                <td><?php echo htmlspecialchars($user->getEmail()); ?></td>

                                <!-- Ícone representativo de se o utilizador já é admin -->
                                <td>
                                    <?php if ($user->getIsAdmin()): ?>
                                        <span class="icon-yes">
                                            <img src="/Images/site/admin/right.png" alt="Sim" class="status-icon">
                                        </span>
                                    <?php else: ?>
                                        <span class="icon-no">
                                            <img src="/Images/site/admin/wrong.png" alt="Não" class="status-icon">
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Botão para promover um user a admin. Esta ação carece de dupla confirmação -->
                                <td>
                                    <?php if (!$user->getIsAdmin()): ?>
                                        <form method="POST" action=""
                                              onsubmit="return confirm('Tem certeza que deseja promover este usuário a administrador?');">
                                            <input type="hidden" name="promote_id" value="<?php echo $user->getId(); ?>">
                                            <button type="submit" class="promote-button">Promover a Admin</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="already-admin">Já é Administrador</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php } ?>

<?php
/*!-----------------------------------------
Usados na página de Controlo de Contactos
------------------------------------------>

/**
 * Desenha uma tabela com os contactos recebidos, mostrando informações detalhadas
 * e permitindo ao administrador marcar como lido ou eliminar cada mensagem.
 *
 * @param array $contacts Lista de contactos, objetos Contact retornados por consulta da DB.
 */
function drawContactsTable($contacts) { ?>
    <div class="contacts-table-container">
        <!-- Caso não exista qualquer contacto -->
        <?php if (empty($contacts)): ?>
            <div class="no-contacts">
                <p>📧 Não existem mensagens de contacto.</p>
            </div>

        <!-- Caso de existência de contactos -->
        <?php else: ?>
            <table class="contacts-table">
                <!-- Nomes das colunas da tabela -->
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Assunto</th>
                        <th>Mensagem</th>
                        <th>Data/Hora</th>
                        <th>Estado</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <!-- Preenchimento do corpo da tabela, com os dados apropriados -->
                <tbody>
                    <?php foreach ($contacts as $contact): ?>
                        <tr class="<?php echo $contact->getIsRead() ? '' : 'unread-contact'; ?>">
                            <!-- ID -->
                            <td><?php echo htmlspecialchars($contact->getId()); ?></td>
                            
                            <!-- Nome -->
                            <td>
                                <strong><?php echo htmlspecialchars($contact->getName()); ?></strong>
                            </td>
                            
                            <!-- Email -->
                            <td>
                                <a href="mailto:<?php echo htmlspecialchars($contact->getEmail()); ?>" 
                                   style="color: var(--primary-color); text-decoration: none;">
                                    <?php echo htmlspecialchars($contact->getEmail()); ?>
                                </a>
                            </td>
                            
                            <!-- Telefone -->
                            <td>
                                <?php 
                                $phone = $contact->getPhone();
                                if (!empty($phone)) {
                                    echo '<a href="tel:' . htmlspecialchars($phone) . '" style="color: var(--primary-color); text-decoration: none;">' . htmlspecialchars($phone) . '</a>';
                                } else {
                                    echo '<span class="empty-field">N/A</span>';
                                }
                                ?>
                            </td>
                            
                            <!-- Assunto -->
                            <td>
                                <span title="<?php echo htmlspecialchars($contact->getSubject()); ?>">
                                    <?php 
                                    $subject = $contact->getSubject();
                                    echo strlen($subject) > 25 ? htmlspecialchars(substr($subject, 0, 25)) . '...' : htmlspecialchars($subject);
                                    ?>
                                </span>
                            </td>
                            
                            <!-- Mensagem (preview) -->
                            <td class="message-preview">
                                <?php 
                                $message = $contact->getMessage();
                                $truncated = strlen($message) > 50 ? substr($message, 0, 50) . '...' : $message;
                                echo '<span title="' . htmlspecialchars($message) . '">' . htmlspecialchars($truncated) . '</span>';
                                ?>
                            </td>
                            
                            <!-- Data e Hora -->
                            <td>
                                <div style="font-size: 0.9em;">
                                    <div><?php echo htmlspecialchars(date('d/m/Y', strtotime($contact->getCreatedAt()))); ?></div>
                                    <div style="color: #666; font-size: 0.85em;">
                                        <?php echo htmlspecialchars(date('H:i', strtotime($contact->getCreatedTime()))); ?>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Estado -->
                            <td>
                                <?php if ($contact->getIsRead()): ?>
                                    <span class="status-read">Lida</span>
                                <?php else: ?>
                                    <span class="status-unread">Não Lida</span>
                                <?php endif; ?>
                            </td>

                            <!-- Botões de ação -->
                            <td class="contact-actions">
                                <!-- Botão para marcar como lida (apenas se não estiver lida) -->
                                <?php if (!$contact->getIsRead()): ?>
                                    <form method="POST" action="/Controllers/contactController.php" style="display: inline;">
                                        <input type="hidden" name="action" value="mark_read">
                                        <input type="hidden" name="contact_id" value="<?php echo $contact->getId(); ?>">
                                        <button type="submit" class="mark-read-button" title="Marcar como lida">
                                            ✓
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <!-- Botão para eliminar contacto -->
                                <form method="POST" action="/Controllers/contactController.php" style="display: inline;"
                                      onsubmit="return confirm('Tem certeza que deseja eliminar este contacto?\n\nDe: <?php echo addslashes($contact->getName()); ?>\nAssunto: <?php echo addslashes($contact->getSubject()); ?>');">
                                    <input type="hidden" name="action" value="delete_contact">
                                    <input type="hidden" name="contact_id" value="<?php echo $contact->getId(); ?>">
                                    <button type="submit" class="delete-button">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php } ?>
