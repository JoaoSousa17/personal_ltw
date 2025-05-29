// profile.js - JavaScript consolidado para páginas de perfil

/* ==========================================
   GESTÃO DE FOTOS DE PERFIL
========================================== */

// Configurar gestão de fotos de perfil
function setupPhotoManagement() {
    const photoUpload = document.getElementById('photo-upload');
    const deletePhotoBtn = document.getElementById('delete-photo-btn');
    const photoPreview = document.getElementById('profile-photo-preview');
    const photoMessage = document.getElementById('photo-message');
    
    if (!photoPreview) return;
    
    // URL da foto atual para fallback
    const currentPhotoUrl = photoPreview.src || '/Images/site/header/genericProfile.png';
    
    // Obter dados do utilizador
    const isOwnProfile = window.profilePageData ? window.profilePageData.isOwnProfile : true;
    const targetUserId = window.profilePageData ? window.profilePageData.targetUserId : null;
    
    /**
     * Mostra mensagens relacionadas com a foto de perfil
     */
    function showPhotoMessage(message, type) {
        if (!photoMessage) return;
        
        const typeClasses = {
            'success': 'alert-success',
            'error': 'alert-error', 
            'info': 'alert-info',
            'warning': 'alert-warning'
        };
        
        const cssClass = typeClasses[type] || 'alert-info';
        
        photoMessage.innerHTML = `
            <div class="alert ${cssClass}" style="padding: 10px; border-radius: 5px; margin: 0; text-align: center;">
                ${message}
            </div>
        `;
        
        if (type !== 'error') {
            setTimeout(() => {
                photoMessage.innerHTML = '';
            }, 5000);
        }
    }
    
    /**
     * Valida o ficheiro selecionado
     */
    function validateImageFile(file) {
        if (!file) {
            return { valid: false, message: 'Nenhum ficheiro selecionado.' };
        }
        
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            return { 
                valid: false, 
                message: 'Tipo de ficheiro não permitido. Use JPEG, PNG, GIF ou WebP.' 
            };
        }
        
        const maxSize = 5 * 1024 * 1024; // 5MB
        if (file.size > maxSize) {
            return { 
                valid: false, 
                message: 'Ficheiro muito grande. Tamanho máximo: 5MB.' 
            };
        }
        
        return { valid: true };
    }
    
    /**
     * Faz o upload da foto via AJAX
     */
    function uploadPhoto(file) {
        const formData = new FormData();
        formData.append('photo', file);
        formData.append('action', 'upload_photo');
        
        if (!isOwnProfile && targetUserId) {
            formData.append('target_user_id', targetUserId);
        }
        
        showPhotoMessage('A carregar foto...', 'info');
        
        fetch('/Controllers/userController.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showPhotoMessage(data.message, 'success');
                
                if (data.path && photoPreview) {
                    photoPreview.src = '/' + data.path;
                    photoPreview.style.border = '3px solid #4a90e2';
                }
                
                if (deletePhotoBtn) {
                    deletePhotoBtn.style.display = 'inline-block';
                }
                
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                showPhotoMessage(data.message, 'error');
                if (photoPreview) {
                    photoPreview.src = currentPhotoUrl;
                }
            }
        })
        .catch(error => {
            console.error('Erro no upload:', error);
            showPhotoMessage('Erro ao carregar a foto. Tente novamente.', 'error');
            
            if (photoPreview) {
                photoPreview.src = currentPhotoUrl;
            }
        });
    }
    
    /**
     * Elimina a foto de perfil atual
     */
    function deletePhoto() {
        if (!confirm('Tem certeza que deseja eliminar a foto de perfil?')) {
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'delete_photo');
        
        if (!isOwnProfile && targetUserId) {
            formData.append('target_user_id', targetUserId);
        }
        
        showPhotoMessage('A eliminar foto...', 'info');
        
        fetch('/Controllers/userController.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showPhotoMessage(data.message, 'success');
                
                if (photoPreview) {
                    photoPreview.src = '/Images/site/header/genericProfile.png';
                    photoPreview.style.border = '3px solid #ccc';
                }
                
                if (deletePhotoBtn) {
                    deletePhotoBtn.style.display = 'none';
                }
            } else {
                showPhotoMessage(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Erro ao eliminar foto:', error);
            showPhotoMessage('Erro ao eliminar a foto. Tente novamente.', 'error');
        });
    }
    
    /**
     * Cria preview da imagem
     */
    function createImagePreview(file) {
        if (!photoPreview) return;
        
        const reader = new FileReader();
        reader.onload = function(e) {
            photoPreview.src = e.target.result;
            photoPreview.style.border = '3px solid #4a90e2';
        };
        reader.readAsDataURL(file);
    }
    
    // Event Listeners
    if (photoUpload) {
        photoUpload.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (!file) return;
            
            const validation = validateImageFile(file);
            if (!validation.valid) {
                showPhotoMessage(validation.message, 'error');
                this.value = '';
                return;
            }
            
            createImagePreview(file);
            uploadPhoto(file);
            this.value = '';
        });
    }
    
    if (deletePhotoBtn) {
        deletePhotoBtn.addEventListener('click', function(e) {
            e.preventDefault();
            deletePhoto();
        });
    }
    
    // Drag and Drop
    if (photoPreview) {
        photoPreview.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.opacity = '0.7';
            this.style.border = '3px dashed #4a90e2';
        });
        
        photoPreview.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.style.opacity = '1';
            this.style.border = '3px solid #4a90e2';
        });
        
        photoPreview.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.opacity = '1';
            this.style.border = '3px solid #4a90e2';
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                
                const validation = validateImageFile(file);
                if (!validation.valid) {
                    showPhotoMessage(validation.message, 'error');
                    return;
                }
                
                createImagePreview(file);
                uploadPhoto(file);
            }
        });
        
        photoPreview.style.cursor = 'pointer';
        photoPreview.addEventListener('click', function() {
            if (photoUpload) {
                photoUpload.click();
            }
        });
    }
    
    // Função global para uso externo
    window.profilePhotoManager = {
        upload: uploadPhoto,
        delete: deletePhoto,
        showMessage: showPhotoMessage
    };
}

/* ==========================================
   VALIDAÇÃO DE FORMULÁRIOS
========================================== */

// Configurar validação de passwords
function setupPasswordValidation() {
    const passwordField = document.getElementById('password');
    const confirmField = document.getElementById('password_confirm');
    const form = document.querySelector('.edit-form');
    
    if (!passwordField || !confirmField || !form) return;
    
    function validatePasswords() {
        if (passwordField.value && confirmField.value) {
            if (passwordField.value !== confirmField.value) {
                confirmField.setCustomValidity('As passwords não coincidem');
            } else {
                confirmField.setCustomValidity('');
            }
        } else {
            confirmField.setCustomValidity('');
        }
    }
    
    passwordField.addEventListener('input', validatePasswords);
    confirmField.addEventListener('input', validatePasswords);
    
    form.addEventListener('submit', function(e) {
        validatePasswords();
        if (!confirmField.checkValidity()) {
            e.preventDefault();
            alert('Por favor, corrija os erros no formulário antes de submeter.');
        }
    });
}

/* ==========================================
   FORMATAÇÃO DE CAMPOS
========================================== */

// Configurar formatação automática do código postal
function setupZipCodeFormatting() {
    const zipCodeInput = document.getElementById('zip_code');
    if (!zipCodeInput) return;
    
    zipCodeInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // Remove tudo que não é dígito
        if (value.length >= 4) {
            value = value.substring(0, 4) + '-' + value.substring(4, 7);
        }
        e.target.value = value;
    });
}

/* ==========================================
   EFEITOS VISUAIS
========================================== */

// Configurar efeitos visuais nos botões
function setupButtonEffects() {
    const buttons = document.querySelectorAll('.btn-photo, .btn-delete, .btn-submit, .btn-cancel');
    
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });
}

/* ==========================================
   CONFIGURAÇÃO DE DADOS GLOBAIS
========================================== */

// Configurar dados da página de perfil para scripts externos
function setupProfilePageData(isOwnProfile, targetUserId, currentPhotoUrl) {
    window.profilePageData = {
        isOwnProfile: isOwnProfile,
        targetUserId: targetUserId,
        currentPhotoUrl: currentPhotoUrl || "/Images/site/header/genericProfile.png"
    };
}

/* ==========================================
   INICIALIZAÇÃO
========================================== */

// Inicializar todas as funcionalidades quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    setupPhotoManagement();
    setupPasswordValidation();
    setupZipCodeFormatting();
    setupButtonEffects();
});
