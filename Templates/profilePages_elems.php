<?php
// profilePages_elems.php - Funções para páginas de perfil

/**
 * Desenha a área principal do perfil com foto, info e ações
 */
function drawProfileMain($userData, $profilePhotoUrl, $isOwnProfile, $isAdmin, $profileUserId) {
    ?>
    <div class="profile-main">
        <div class="profile-image-container">
            <div class="profile-image" style="background-image: url('<?php echo $profilePhotoUrl ?: '/Images/site/header/genericProfile.png'; ?>'); background-size: cover; background-position: center;">
                <?php if (!$profilePhotoUrl): ?>
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #ccc; font-size: 3rem;">
                        <i class="fas fa-user"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="profile-info">
            <h1><?php echo htmlspecialchars($userData['name_']); ?></h1>
            <p class="profile-username">@<?php echo htmlspecialchars($userData['username']); ?></p>
            
            <?php drawProfileBadges($userData); ?>
        </div>

        <div class="profile-actions">
            <?php drawProfileActions($isOwnProfile, $isAdmin, $userData, $profileUserId); ?>
        </div>
    </div>
    <?php
}

/**
 * Desenha os badges do perfil
 */
function drawProfileBadges($userData) {
    ?>
    <div class="profile-badges">
        <?php if ($userData['is_admin']): ?>
            <span class="badge badge-admin">
                <i class="fas fa-crown" style="margin-right: 5px;"></i>
                Administrador
            </span>
        <?php endif; ?>
        
        <?php if ($userData['is_freelancer']): ?>
            <span class="badge badge-freelancer">
                <i class="fas fa-briefcase" style="margin-right: 5px;"></i>
                Freelancer
            </span>
        <?php endif; ?>
        
        <?php if ($userData['is_blocked']): ?>
            <span class="badge badge-blocked">
                <i class="fas fa-ban" style="margin-right: 5px;"></i>
                Bloqueado
            </span>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Desenha as ações do perfil
 */
function drawProfileActions($isOwnProfile, $isAdmin, $userData, $profileUserId) {
    if ($isOwnProfile): ?>
        <a href="/Views/profile/editProfile.php" class="edit-profile-btn">
            <i class="fas fa-edit" style="margin-right: 5px;"></i>
            Editar Perfil
        </a>
        <form method="post" style="display: inline;">
            <button type="submit" name="logout" class="logout-btn">
                <i class="fas fa-sign-out-alt" style="margin-right: 5px;"></i>
                Terminar Sessão
            </button>
        </form>
    <?php elseif ($isAdmin && !$isOwnProfile): ?>
        <form method="post" style="display: inline;">
            <?php if ($userData['is_blocked']): ?>
                <input type="hidden" name="admin_action" value="unblock">
                <button type="submit" class="edit-profile-btn">
                    <i class="fas fa-unlock" style="margin-right: 5px;"></i>
                    Desbloquear
                </button>
            <?php else: ?>
                <input type="hidden" name="admin_action" value="block">
                <button type="submit" class="logout-btn" onclick="return confirm('Tem certeza que deseja bloquear este utilizador?')">
                    <i class="fas fa-ban" style="margin-right: 5px;"></i>
                    Bloquear
                </button>
            <?php endif; ?>
        </form>
        <a href="/Views/profile/editProfile.php?id=<?php echo $profileUserId; ?>" class="edit-profile-btn">
            <i class="fas fa-edit" style="margin-right: 5px;"></i>
            Editar Perfil
        </a>
    <?php endif;
}

/**
 * Desenha a seção de informações de contato
 */
function drawContactInfo($userData, $isOwnProfile, $currencyName) {
    ?>
    <div class="profile-contact">
        <h2>
            <i class="fas fa-address-card" style="margin-right: 10px; color: var(--primary-color);"></i>
            Informações <?php echo $isOwnProfile ? 'de Contato' : 'Públicas'; ?>
        </h2>
        
        <?php if ($isOwnProfile): ?>
            <div class="contact-item">
                <div class="contact-icon">
                    <img src="/Images/site/footer/mail.png" alt="Email">
                </div>
                <div class="contact-text">
                    <p class="contact-label">Email</p>
                    <p class="contact-value"><?php echo htmlspecialchars($userData['email']); ?></p>
                </div>
            </div>
            
            <?php if (!empty($userData['phone_number'])): ?>
            <div class="contact-item">
                <div class="contact-icon">
                    <img src="/Images/site/footer/phone.png" alt="Telefone">
                </div>
                <div class="contact-text">
                    <p class="contact-label">Telefone</p>
                    <p class="contact-value"><?php echo htmlspecialchars($userData['phone_number']); ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="contact-item">
                <div class="contact-icon">
                <img src="/Images/site/footer/dollar.png" alt="Moeda">
                </div>
                <div class="contact-text">
                    <p class="contact-label">Moeda Preferida</p>
                    <p class="contact-value"><?php echo htmlspecialchars($currencyName); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($userData['web_link']): ?>
        <div class="contact-item">
            <div class="contact-icon">
                <i class="fas fa-globe" style="color: white; font-size: 20px;"></i>
            </div>
            <div class="contact-text">
                <p class="contact-label">Website</p>
                <p class="contact-value">
                    <a href="<?php echo htmlspecialchars($userData['web_link']); ?>" target="_blank" style="color: var(--primary-color); text-decoration: none;">
                        <i class="fas fa-external-link-alt" style="margin-right: 5px;"></i>
                        <?php echo htmlspecialchars($userData['web_link']); ?>
                    </a>
                </p>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (!$isOwnProfile && !empty($userData['phone_number'])): ?>
        <div class="contact-item">
            <div class="contact-icon">
                <img src="/Images/site/footer/phone.png" alt="Telefone" width="16">
            </div>
            <div class="contact-text">
                <p class="contact-label">Telefone</p>
                <p class="contact-value"><?php echo htmlspecialchars($userData['phone_number']); ?></p>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (!$isOwnProfile && !$userData['web_link'] && empty($userData['phone_number'])): ?>
        <div class="bio-empty" style="text-align: center; padding: 20px;">
            <i class="fas fa-info-circle" style="font-size: 2rem; color: #ccc; margin-bottom: 10px;"></i>
            <p>Não existem informações públicas disponíveis.</p>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Desenha a seção de detalhes do perfil
 */
function drawProfileDetails($userData, $isOwnProfile, $currencyName) {
    ?>
    <div class="profile-details">
        <h2>
            <i class="fas fa-info-circle" style="margin-right: 10px; color: var(--primary-color);"></i>
            Informações Adicionais
        </h2>
        
        <div class="bio-section">
            <h3>
                <i class="fas fa-user-cog" style="margin-right: 8px; color: var(--primary-color);"></i>
                Detalhes da Conta
            </h3>
            <div class="bio-content">
                <p>
                    <i class="fas fa-calendar-alt" style="margin-right: 8px; color: var(--primary-color);"></i>
                    <strong>Membro desde:</strong> <?php echo date('d/m/Y', strtotime($userData['creation_date'])); ?>
                </p>
                
                <p>
                    <i class="fas fa-user-tag" style="margin-right: 8px; color: var(--primary-color);"></i>
                    <strong>Tipo de conta:</strong> 
                    <?php 
                    $accountTypes = [];
                    if ($userData['is_admin']) $accountTypes[] = 'Administrador';
                    if ($userData['is_freelancer']) $accountTypes[] = 'Freelancer';
                    if (empty($accountTypes)) $accountTypes[] = 'Utilizador Regular';
                    echo implode(', ', $accountTypes);
                    ?>
                </p>
                
                <?php if ($isOwnProfile): ?>
                <p>
                    <i class="fas fa-coins" style="margin-right: 8px; color: var(--primary-color);"></i>
                    <strong>Moeda preferida:</strong> <?php echo htmlspecialchars($currencyName); ?>
                </p>
                <p>
                    <i class="fas fa-moon" style="margin-right: 8px; color: var(--primary-color);"></i>
                    <strong>Modo noturno:</strong> 
                    <?php echo $userData['night_mode'] ? 'Ativado' : 'Desativado'; ?>
                </p>
                <?php endif; ?>
                
                <?php if ($userData['is_blocked']): ?>
                <p style="color: #dc3545;">
                    <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>
                    <strong>Estado:</strong> Conta bloqueada
                </p>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($userData['web_link']): ?>
        <div class="website-section">
            <h3>
                <i class="fas fa-globe" style="margin-right: 8px; color: var(--primary-color);"></i>
                <?php echo $isOwnProfile ? 'O meu website' : 'Website'; ?>
            </h3>
            <a href="<?php echo htmlspecialchars($userData['web_link']); ?>" target="_blank" class="website-link">
                <i class="fas fa-external-link-alt" style="margin-right: 8px;"></i>
                Visitar website
            </a>
        </div>
        <?php endif; ?>

        <?php if ($userData['is_freelancer']): ?>
        <div class="stats-section" style="margin-top: 30px;">
            <h3>
                <i class="fas fa-chart-bar" style="margin-right: 8px; color: var(--primary-color);"></i>
                Estatísticas
            </h3>
            <div class="bio-content">
                <p>
                    <i class="fas fa-briefcase" style="margin-right: 8px; color: var(--primary-color);"></i>
                    <strong>Serviços publicados:</strong> Em desenvolvimento
                </p>
                <p>
                    <i class="fas fa-star" style="margin-right: 8px; color: var(--primary-color);"></i>
                    <strong>Avaliação média:</strong> Em desenvolvimento
                </p>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Desenha a seção de foto de perfil (para edição)
 */
function drawPhotoSection($currentPhotoUrl) {
    ?>
    <div class="photo-section">
        <h3>Foto de Perfil</h3>
        <div class="current-photo">
            <?php if ($currentPhotoUrl): ?>
                <img id="profile-photo-preview" src="<?php echo htmlspecialchars($currentPhotoUrl); ?>" 
                     alt="Foto de perfil atual" 
                     title="Clique para alterar ou arraste uma nova imagem">
            <?php else: ?>
                <img id="profile-photo-preview" src="/Images/site/header/genericProfile.png" 
                     alt="Foto de perfil padrão" 
                     title="Clique para adicionar uma foto ou arraste uma imagem">
            <?php endif; ?>
        </div>
        
        <div class="photo-actions">
            <label for="photo-upload" class="btn-photo">
                <i class="fas fa-camera"></i>
                Escolher Nova Foto
            </label>
            <input type="file" id="photo-upload" name="photo" accept="image/jpeg,image/png,image/gif,image/webp" style="display: none;">
            
            <?php if ($currentPhotoUrl): ?>
                <button type="button" id="delete-photo-btn" class="btn-delete">
                    <i class="fas fa-trash"></i>
                    Eliminar Foto
                </button>
            <?php else: ?>
                <button type="button" id="delete-photo-btn" class="btn-delete" style="display: none;">
                    <i class="fas fa-trash"></i>
                    Eliminar Foto
                </button>
            <?php endif; ?>
        </div>
        
        <p class="photo-hint">
            <i class="fas fa-info-circle"></i>
            Formatos aceites: JPEG, PNG, GIF, WebP. Tamanho máximo: 5MB.<br>
            Dica: Clique na foto ou arraste uma imagem para fazer upload.
        </p>
        
        <div id="photo-message"></div>
    </div>
    <?php
}

/**
 * Desenha o formulário de dados pessoais
 */
function drawPersonalInfoForm($userData, $availableCurrencies, $isOwnProfile, $editUserId) {
    ?>
    <div class="form-section">
        <h3>Informações Pessoais</h3>
        <form method="post" action="/Controllers/userController.php" class="edit-form">
            <input type="hidden" name="action" value="update_profile">
            <?php if (!$isOwnProfile): ?>
                <input type="hidden" name="target_user_id" value="<?php echo $editUserId; ?>">
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Nome Completo</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($userData['name_']); ?>" required>
                    <p class="form-hint">O seu nome completo como aparecerá no perfil</p>
                </div>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($userData['username']); ?>" required>
                    <p class="form-hint">Este será o identificador único no site (mínimo 3 caracteres)</p>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                    <p class="form-hint">Email para login e comunicações importantes</p>
                </div>
                
                <div class="form-group">
                    <label for="phone_number">Telefone</label>
                    <input type="tel" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($userData['phone_number'] ?? ''); ?>" placeholder="+351 123 456 789">
                    <p class="form-hint">Número de telefone para contacto (mínimo 9 dígitos)</p>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="currency">Moeda Preferida</label>
                    <select id="currency" name="currency">
                        <?php foreach ($availableCurrencies as $code => $name): ?>
                            <option value="<?php echo $code; ?>" 
                                    <?php echo ($userData['currency'] === $code) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="form-hint">Moeda utilizada para exibir preços e valores no site</p>
                </div>
                
                <div class="form-group">
                    <label for="web_link">Website Pessoal</label>
                    <input type="url" id="web_link" name="web_link" value="<?php echo htmlspecialchars($userData['web_link'] ?? ''); ?>" placeholder="https://exemplo.com">
                    <p class="form-hint">Link para website pessoal, portfolio ou perfil profissional</p>
                </div>
            </div>
            
            <?php if ($isOwnProfile): ?>
            <div class="form-group">
                <label for="night_mode">Modo Noturno</label>
                <select id="night_mode" name="night_mode">
                    <option value="0" <?php echo !$userData['night_mode'] ? 'selected' : ''; ?>>Desativado</option>
                    <option value="1" <?php echo $userData['night_mode'] ? 'selected' : ''; ?>>Ativado</option>
                </select>
                <p class="form-hint">Ativar tema escuro para melhor experiência noturna</p>
            </div>
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Nova Password</label>
                    <input type="password" id="password" name="password" minlength="6">
                    <p class="form-hint">Deixe em branco para manter a password atual (mínimo 6 caracteres)</p>
                </div>
                
                <div class="form-group">
                    <label for="password_confirm">Confirmar Nova Password</label>
                    <input type="password" id="password_confirm" name="password_confirm" minlength="6">
                    <p class="form-hint">Digite novamente a nova password para confirmação</p>
                </div>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn-submit">Guardar Alterações</button>
                <a href="/Views/profile/profile.php<?php echo $isOwnProfile ? '' : '?id=' . $editUserId; ?>" class="btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>
    <?php
}

/**
 * Desenha o formulário de morada
 */
function drawAddressForm($userAddress, $portugueseDistricts, $isOwnProfile, $editUserId) {
    ?>
    <div class="form-section address-section">
        <div class="section-header-with-action">
            <h3>Morada</h3>
            <?php if ($userAddress): ?>
                <form method="post" action="/Controllers/adressController.php" style="display: inline;">
                    <input type="hidden" name="action" value="delete_address">
                    <?php if (!$isOwnProfile): ?>
                        <input type="hidden" name="target_user_id" value="<?php echo $editUserId; ?>">
                    <?php endif; ?>
                    <button type="submit" class="btn-delete-small" onclick="return confirm('Tem certeza que deseja remover a morada?')">
                        <i class="fas fa-trash"></i> Remover Morada
                    </button>
                </form>
            <?php endif; ?>
        </div>
        
        <p class="section-description">
            <?php echo $userAddress ? 'Atualize a sua morada atual:' : 'Adicione uma morada à sua conta:'; ?>
        </p>
        
        <form method="post" action="/Controllers/adressController.php" class="edit-form address-form">
            <input type="hidden" name="action" value="update_address">
            <?php if (!$isOwnProfile): ?>
                <input type="hidden" name="target_user_id" value="<?php echo $editUserId; ?>">
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group flex-2">
                    <label for="street">Rua *</label>
                    <input type="text" id="street" name="street" 
                           value="<?php echo $userAddress ? htmlspecialchars($userAddress->getStreet()) : ''; ?>" 
                           placeholder="Ex: Rua da Constituição" required>
                </div>
                
                <div class="form-group">
                    <label for="door_num">Nº da Porta *</label>
                    <input type="text" id="door_num" name="door_num" 
                           value="<?php echo $userAddress ? htmlspecialchars($userAddress->getDoorNum()) : ''; ?>" 
                           placeholder="Ex: 123" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="floor">Andar</label>
                    <input type="text" id="floor" name="floor" 
                           value="<?php echo $userAddress ? htmlspecialchars($userAddress->getFloor() ?? '') : ''; ?>" 
                           placeholder="Ex: 2º, R/C">
                </div>
                
                <div class="form-group flex-2">
                    <label for="extra">Informações Adicionais</label>
                    <input type="text" id="extra" name="extra" 
                           value="<?php echo $userAddress ? htmlspecialchars($userAddress->getExtra() ?? '') : ''; ?>" 
                           placeholder="Ex: Apartamento B, Bloco 2">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="district">Distrito *</label>
                    <select id="district" name="district" required>
                        <option value="">Selecione o distrito</option>
                        <?php foreach ($portugueseDistricts as $district): ?>
                            <option value="<?php echo $district; ?>" 
                                    <?php echo ($userAddress && $userAddress->getDistrict() === $district) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($district); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="municipality">Município *</label>
                    <input type="text" id="municipality" name="municipality" 
                           value="<?php echo $userAddress ? htmlspecialchars($userAddress->getMunicipality()) : ''; ?>" 
                           placeholder="Ex: Porto" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="zip_code">Código Postal *</label>
                <input type="text" id="zip_code" name="zip_code" 
                       value="<?php echo $userAddress ? htmlspecialchars($userAddress->getZipCode()) : ''; ?>" 
                       placeholder="XXXX-XXX" pattern="\d{4}-\d{3}" required>
                <p class="form-hint">Formato: XXXX-XXX (ex: 4000-001)</p>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn-submit">
                    <?php echo $userAddress ? 'Atualizar Morada' : 'Adicionar Morada'; ?>
                </button>
            </div>
        </form>
    </div>
    <?php
}
?>
