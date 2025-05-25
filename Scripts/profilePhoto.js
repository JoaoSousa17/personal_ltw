/**
 * Script para gestão de fotos de perfil
 * Funcionalidades: upload, preview e eliminação de fotos
 */

document.addEventListener('DOMContentLoaded', function() {
    const photoUpload = document.getElementById('photo-upload');
    const deletePhotoBtn = document.getElementById('delete-photo-btn');
    const photoPreview = document.getElementById('profile-photo-preview');
    const photoMessage = document.getElementById('photo-message');
    
    // URL da foto atual para fallback
    const currentPhotoUrl = photoPreview ? photoPreview.src : '/Images/site/header/genericProfile.png';
    
    // Obter dados do utilizador (definidos na página PHP)
    const isOwnProfile = window.profilePageData ? window.profilePageData.isOwnProfile : true;
    const targetUserId = window.profilePageData ? window.profilePageData.targetUserId : null;
    
    /**
     * Mostra mensagens relacionadas com a foto de perfil
     * @param {string} message - Mensagem a exibir
     * @param {string} type - Tipo da mensagem (success, error, info, warning)
     */
    function showPhotoMessage(message, type) {
        if (!photoMessage) return;
        
        // Mapear tipos para classes CSS
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
        
        // Remover mensagem após 5 segundos (exceto para erros críticos)
        if (type !== 'error') {
            setTimeout(() => {
                photoMessage.innerHTML = '';
            }, 5000);
        }
    }
    
    /**
     * Valida o ficheiro selecionado antes do upload
     * @param {File} file - Ficheiro a validar
     * @returns {object} - Resultado da validação
     */
    function validateImageFile(file) {
        // Verificar se é um ficheiro
        if (!file) {
            return { valid: false, message: 'Nenhum ficheiro selecionado.' };
        }
        
        // Verificar tipo de ficheiro
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            return { 
                valid: false, 
                message: 'Tipo de ficheiro não permitido. Use JPEG, PNG, GIF ou WebP.' 
            };
        }
        
        // Verificar tamanho (5MB máximo)
        const maxSize = 5 * 1024 * 1024; // 5MB em bytes
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
     * @param {File} file - Ficheiro a fazer upload
     */
    function uploadPhoto(file) {
        const formData = new FormData();
        formData.append('photo', file);
        formData.append('action', 'upload_photo');
        
        // Adicionar ID do utilizador target se não for o próprio perfil
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
                
                // Atualizar preview
                if (data.path && photoPreview) {
                    photoPreview.src = '/' + data.path;
                    photoPreview.style.border = '3px solid #4a90e2';
                }
                
                // Mostrar botão de eliminar se não estiver visível
                if (deletePhotoBtn) {
                    deletePhotoBtn.style.display = 'inline-block';
                }
                
                // Recarregar página após 2 segundos para sincronizar tudo
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                showPhotoMessage(data.message, 'error');
                // Reverter preview para a foto original
                if (photoPreview) {
                    photoPreview.src = currentPhotoUrl;
                }
            }
        })
        .catch(error => {
            console.error('Erro no upload:', error);
            showPhotoMessage('Erro ao carregar a foto. Tente novamente.', 'error');
            
            // Reverter preview
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
        
        // Adicionar ID do utilizador target se não for o próprio perfil
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
                
                // Atualizar preview para foto padrão
                if (photoPreview) {
                    photoPreview.src = '/Images/site/header/genericProfile.png';
                    photoPreview.style.border = '3px solid #ccc';
                }
                
                // Esconder botão de eliminar
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
     * Cria preview da imagem antes do upload
     * @param {File} file - Ficheiro para preview
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
    
    // Upload de foto
    if (photoUpload) {
        photoUpload.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (!file) return;
            
            // Validar ficheiro
            const validation = validateImageFile(file);
            if (!validation.valid) {
                showPhotoMessage(validation.message, 'error');
                this.value = ''; // Limpar input
                return;
            }
            
            // Criar preview
            createImagePreview(file);
            
            // Fazer upload
            uploadPhoto(file);
            
            // Limpar input para permitir selecionar o mesmo ficheiro novamente
            this.value = '';
        });
    }
    
    // Eliminar foto
    if (deletePhotoBtn) {
        deletePhotoBtn.addEventListener('click', function(e) {
            e.preventDefault();
            deletePhoto();
        });
    }
    
    // Drag and Drop (funcionalidade extra)
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
                
                // Validar ficheiro
                const validation = validateImageFile(file);
                if (!validation.valid) {
                    showPhotoMessage(validation.message, 'error');
                    return;
                }
                
                // Criar preview e fazer upload
                createImagePreview(file);
                uploadPhoto(file);
            }
        });
        
        // Adicionar cursor pointer para indicar que é clicável
        photoPreview.style.cursor = 'pointer';
        photoPreview.addEventListener('click', function() {
            if (photoUpload) {
                photoUpload.click();
            }
        });
    }
    
    // Função global para uso externo (se necessário)
    window.profilePhotoManager = {
        upload: uploadPhoto,
        delete: deletePhoto,
        showMessage: showPhotoMessage
    };
});
