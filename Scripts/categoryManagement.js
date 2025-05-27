// Script para melhorar a experiência do utilizador na gestão de categorias

document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.add-category-form');
    const fileInput = document.getElementById('category_image');
    const submitButton = document.querySelector('.submit-button');
    
    // Preview da imagem selecionada
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validar tipo de ficheiro
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Tipo de ficheiro não permitido. Use JPEG, PNG, GIF ou WebP.');
                    this.value = '';
                    removeImagePreview();
                    return;
                }
                
                // Validar tamanho (5MB máximo)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ficheiro muito grande. Tamanho máximo: 5MB.');
                    this.value = '';
                    removeImagePreview();
                    return;
                }
                
                // Criar preview da imagem
                createImagePreview(file);
            } else {
                removeImagePreview();
            }
        });
    }
    
    // Adicionar loading state ao formulário
    if (form) {
        form.addEventListener('submit', function(e) {
            const nameInput = document.getElementById('category_name');
            const imageInput = document.getElementById('category_image');
            
            // Validações básicas
            if (!nameInput.value.trim()) {
                e.preventDefault();
                alert('Por favor, insira o nome da categoria.');
                nameInput.focus();
                return false;
            }
            
            if (!imageInput.files[0]) {
                e.preventDefault();
                alert('Por favor, selecione uma imagem para a categoria.');
                imageInput.focus();
                return false;
            }
            
            // Adicionar loading state
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.classList.add('loading');
                submitButton.textContent = 'A processar...';
            }
            
            // Permitir que o formulário seja submetido
            return true;
        });
    }
    
    // Confirmação para remoção de categorias
    const removeButtons = document.querySelectorAll('form[onsubmit*="confirm"]');
    removeButtons.forEach(form => {
        form.addEventListener('submit', function(e) {
            const categoryName = this.closest('tr')?.querySelector('td:nth-child(3)')?.textContent || 'esta categoria';
            const serviceCount = this.closest('tr')?.querySelector('td:nth-child(4)')?.textContent || '0';
            
            let message = `Tem certeza que deseja remover a categoria "${categoryName}"?`;
            if (parseInt(serviceCount) > 0) {
                message += `\n\nATENÇÃO: Esta categoria tem ${serviceCount} serviço(s) associado(s)`;
            }
            
            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }
        });
    });
    
    // Função para criar preview da imagem
    function createImagePreview(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Remover preview anterior se existir
            removeImagePreview();
            
            // Criar container do preview
            const previewContainer = document.createElement('div');
            previewContainer.className = 'image-preview-container';
            previewContainer.style.cssText = `
                margin-top: 15px;
                padding: 15px;
                border: 2px solid #e9ecef;
                border-radius: 8px;
                background-color: #f8f9fa;
                text-align: center;
                position: relative;
            `;
            
            // Criar imagem de preview
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'image-preview';
            img.style.cssText = `
                max-width: 200px;
                max-height: 150px;
                border-radius: 6px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                object-fit: cover;
            `;
            
            // Criar texto informativo
            const info = document.createElement('div');
            info.style.cssText = `
                margin-top: 10px;
                font-size: 0.85rem;
                color: #6c757d;
            `;
            info.innerHTML = `
                <strong>${file.name}</strong><br>
                Tamanho: ${formatFileSize(file.size)}<br>
                <small>A imagem será redimensionada automaticamente para 800x600px</small>
            `;
            
            // Botão para remover
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.textContent = '×';
            removeBtn.className = 'remove-preview-btn';
            removeBtn.style.cssText = `
                position: absolute;
                top: 5px;
                right: 5px;
                background: #dc3545;
                color: white;
                border: none;
                border-radius: 50%;
                width: 25px;
                height: 25px;
                cursor: pointer;
                font-size: 16px;
                line-height: 1;
                display: flex;
                align-items: center;
                justify-content: center;
            `;
            
            removeBtn.addEventListener('click', function() {
                fileInput.value = '';
                removeImagePreview();
            });
            
            // Montar o preview
            previewContainer.appendChild(img);
            previewContainer.appendChild(info);
            previewContainer.appendChild(removeBtn);
            
            // Inserir depois do input de ficheiro
            fileInput.parentNode.appendChild(previewContainer);
        };
        reader.readAsDataURL(file);
    }
    
    // Função para remover preview da imagem
    function removeImagePreview() {
        const existingPreview = document.querySelector('.image-preview-container');
        if (existingPreview) {
            existingPreview.remove();
        }
    }
    
    // Função para formatar tamanho do ficheiro
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Melhorar a experiência visual das miniaturas na tabela
    const thumbnails = document.querySelectorAll('.category-thumbnail');
    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
            showImageModal(this.src, this.alt);
        });
        
        // Adicionar tooltip
        thumb.title = 'Clique para ver a imagem em tamanho maior';
    });
    
    // Função para mostrar modal com a imagem
    function showImageModal(src, alt) {
        // Criar modal
        const modal = document.createElement('div');
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            cursor: pointer;
        `;
        
        // Criar imagem ampliada
        const img = document.createElement('img');
        img.src = src;
        img.alt = alt;
        img.style.cssText = `
            max-width: 90%;
            max-height: 90%;
            border-radius: 8px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        `;
        
        // Fechar modal ao clicar
        modal.addEventListener('click', function() {
            document.body.removeChild(modal);
        });
        
        // Fechar com ESC
        const closeOnEsc = function(e) {
            if (e.key === 'Escape') {
                document.body.removeChild(modal);
                document.removeEventListener('keydown', closeOnEsc);
            }
        };
        document.addEventListener('keydown', closeOnEsc);
        
        modal.appendChild(img);
        document.body.appendChild(modal);
    }
    
    // Auto-hide das mensagens de alerta após 5 segundos
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert.parentNode) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 500);
            }
        }, 5000);
    });
});

// Função para drag & drop (funcionalidade extra)
function initDragAndDrop() {
    const fileInput = document.getElementById('category_image');
    const formGroup = fileInput?.closest('.form-group');
    
    if (!formGroup) return;
    
    // Adicionar estilos para drag & drop
    formGroup.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.backgroundColor = '#e3f2fd';
        this.style.borderColor = '#2196f3';
    });
    
    formGroup.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.style.backgroundColor = '';
        this.style.borderColor = '';
    });
    
    formGroup.addEventListener('drop', function(e) {
        e.preventDefault();
        this.style.backgroundColor = '';
        this.style.borderColor = '';
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            // Disparar evento change
            const event = new Event('change', { bubbles: true });
            fileInput.dispatchEvent(event);
        }
    });
}

// Inicializar drag & drop quando a página carregar
document.addEventListener('DOMContentLoaded', initDragAndDrop);