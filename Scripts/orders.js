// orders.js - JavaScript consolidado para páginas de pedidos e serviços

/* ==========================================
   FUNÇÕES COMUNS
========================================== */
document.getElementById("orderBtn").addEventListener("click", (event) => {
    const formData = new FormData();
  
    const productId = event.target.getAttribute("data-id");
    const title = document.querySelector(".product-info h2")?.textContent.trim();

    const price = event.target.getAttribute("data-price") || "0";

    const image = document.querySelector(".product-image-img")?.src;
    const seller = document.querySelector(".product-advertiser")?.textContent.trim().replace("Utilizador", "").trim();
  
    formData.append("id", productId);
    formData.append("title", title);
    formData.append("price", price);
    formData.append("image", image);
    formData.append("seller", seller);
  
    fetch("/Controllers/add_to_cart.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === "success") {
        const cartBadge = document.getElementById("cart-badge");
        if (cartBadge) {
          cartBadge.textContent = data.total;
          cartBadge.style.display = "flex";
          cartBadge.classList.remove("animate");
          void cartBadge.offsetWidth;
          cartBadge.classList.add("animate");
        }
      } else {
        alert("Erro ao adicionar ao carrinho.");
      }
    })
    .catch(() => alert("Erro de rede."));
  });

// Cálculo automático de preços
function setupPriceCalculation() {
    const priceInput = document.getElementById('price_per_hour');
    const durationInput = document.getElementById('duration');
    const promotionInput = document.getElementById('promotion');
    const basePriceDisplay = document.getElementById('base-price');
    const discountedPriceDisplay = document.getElementById('discounted-price');
    const discountedSection = document.getElementById('discounted-section');
    
    if (!priceInput || !durationInput || !promotionInput) return;
    
    function calculatePrices() {
        const pricePerHour = parseFloat(priceInput.value) || 0;
        const duration = parseInt(durationInput.value) || 0;
        const promotion = parseInt(promotionInput.value) || 0;
        
        const basePrice = (pricePerHour * duration) / 60;
        const discountedPrice = basePrice * (1 - promotion / 100);
        
        if (basePriceDisplay) {
            basePriceDisplay.textContent = '€' + basePrice.toFixed(2);
        }
        
        if (discountedSection && discountedPriceDisplay) {
            if (promotion > 0) {
                discountedPriceDisplay.textContent = '€' + discountedPrice.toFixed(2);
                discountedSection.style.display = 'flex';
            } else {
                discountedSection.style.display = 'none';
            }
        }
    }
    
    priceInput.addEventListener('input', calculatePrices);
    durationInput.addEventListener('input', calculatePrices);
    promotionInput.addEventListener('input', calculatePrices);
    
    calculatePrices(); // Calcular inicialmente
}

// Contador de caracteres
function setupCharacterCounter() {
    const descriptionInput = document.getElementById('description');
    const charCountSpan = document.getElementById('char-count');
    
    if (descriptionInput && charCountSpan) {
        descriptionInput.addEventListener('input', function() {
            charCountSpan.textContent = this.value.length;
        });
    }
}

/* ==========================================
   GESTÃO DE IMAGENS
========================================== */

function setupImagePreview() {
    const imageInput = document.getElementById('images');
    const preview = document.getElementById('image-preview');
    
    if (!imageInput || !preview) return;
    
    imageInput.addEventListener('change', function(e) {
        preview.innerHTML = '';
        
        Array.from(e.target.files).forEach((file, index) => {
            if (index >= 5) return; // Máximo 5 imagens
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const imgContainer = document.createElement('div');
                imgContainer.className = 'preview-image-container';
                imgContainer.setAttribute('data-index', index);
                
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'preview-image';
                
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'remove-image-btn';
                removeBtn.innerHTML = '×';
                removeBtn.onclick = function() {
                    removeImage(index);
                };
                
                imgContainer.appendChild(img);
                imgContainer.appendChild(removeBtn);
                preview.appendChild(imgContainer);
            };
            reader.readAsDataURL(file);
        });
    });
}

function removeImage(indexToRemove) {
    const fileInput = document.getElementById('images');
    const dt = new DataTransfer();
    
    // Recriar a lista de arquivos sem o arquivo removido
    Array.from(fileInput.files).forEach((file, index) => {
        if (index !== indexToRemove) {
            dt.items.add(file);
        }
    });
    
    fileInput.files = dt.files;
    updateImagePreview();
}

function updateImagePreview() {
    const fileInput = document.getElementById('images');
    const preview = document.getElementById('image-preview');
    
    if (!fileInput || !preview) return;
    
    preview.innerHTML = '';
    
    Array.from(fileInput.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const imgContainer = document.createElement('div');
            imgContainer.className = 'preview-image-container';
            
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'preview-image';
            
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-image-btn';
            removeBtn.innerHTML = '×';
            removeBtn.onclick = function() {
                removeImage(index);
            };
            
            imgContainer.appendChild(img);
            imgContainer.appendChild(removeBtn);
            preview.appendChild(imgContainer);
        };
        reader.readAsDataURL(file);
    });
}

/* ==========================================
   VALIDAÇÃO DE FORMULÁRIOS
========================================== */

function setupFormValidation() {
    const form = document.querySelector('.create-service-form');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        let isValid = true;
        let errorMessages = [];
        
        // Validar nome
        const name = document.getElementById('name');
        if (name && name.value.trim().length < 3) {
            errorMessages.push('Nome deve ter pelo menos 3 caracteres');
            isValid = false;
        }
        
        // Validar descrição
        const description = document.getElementById('description');
        if (description && description.value.trim().length < 10) {
            errorMessages.push('Descrição deve ter pelo menos 10 caracteres');
            isValid = false;
        }
        
        // Validar categoria
        const categoryId = document.getElementById('category_id');
        if (categoryId && !categoryId.value) {
            errorMessages.push('Deve selecionar uma categoria');
            isValid = false;
        }
        
        // Validar preço
        const price = document.getElementById('price_per_hour');
        if (price) {
            const priceValue = parseFloat(price.value);
            if (priceValue < 5 || priceValue > 500) {
                errorMessages.push('Preço deve estar entre €5.00 e €500.00');
                isValid = false;
            }
        }
        
        // Validar duração
        const duration = document.getElementById('duration');
        if (duration) {
            const durationValue = parseInt(duration.value);
            if (durationValue < 15 || durationValue > 480) {
                errorMessages.push('Duração deve estar entre 15 minutos e 8 horas');
                isValid = false;
            }
        }
        
        // Validar promoção
        const promotion = document.getElementById('promotion');
        if (promotion) {
            const promotionValue = parseInt(promotion.value) || 0;
            if (promotionValue < 0 || promotionValue > 50) {
                errorMessages.push('Desconto deve estar entre 0% e 50%');
                isValid = false;
            }
        }
        
        if (!isValid) {
            e.preventDefault();
            alert('Erros encontrados:\n\n' + errorMessages.join('\n'));
            return false;
        }
        
        // Confirmar alterações se for edição
        if (form.querySelector('input[name="action"][value="update_service"]')) {
            if (!confirm('Tem certeza que deseja guardar as alterações?')) {
                e.preventDefault();
                return false;
            }
        }
    });
}

/* ==========================================
   GESTÃO DE AVALIAÇÕES (FEEDBACK)
========================================== */

function openFeedbackModal(orderId) {
    const modal = document.getElementById('feedbackModal');
    const orderIdInput = document.getElementById('feedbackOrderId');
    
    if (modal && orderIdInput) {
        orderIdInput.value = orderId;
        modal.style.display = 'block';
    }
}

function closeFeedbackModal() {
    const modal = document.getElementById('feedbackModal');
    const form = document.getElementById('feedbackForm');
    
    if (modal) modal.style.display = 'none';
    if (form) form.reset();
    
    // Reset rating stars
    document.querySelectorAll('.star').forEach(star => {
        star.classList.remove('active');
    });
}

function setupRatingStars() {
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('ratingValue');
    
    if (!stars.length || !ratingInput) return;
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');
            ratingInput.value = rating;
            
            // Reset all stars
            stars.forEach(s => s.classList.remove('active'));
            
            // Highlight selected stars
            for (let i = 0; i < rating; i++) {
                stars[i].classList.add('active');
            }
        });
        
        star.addEventListener('mouseover', function() {
            const rating = this.getAttribute('data-rating');
            
            // Highlight stars on hover
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.add('hover');
                } else {
                    s.classList.remove('hover');
                }
            });
        });
    });
    
    const ratingContainer = document.querySelector('.rating-stars');
    if (ratingContainer) {
        ratingContainer.addEventListener('mouseleave', function() {
            stars.forEach(s => s.classList.remove('hover'));
        });
    }
}

function setupFeedbackForm() {
    const feedbackForm = document.getElementById('feedbackForm');
    if (!feedbackForm) return;
    
    feedbackForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Aqui implementaria o envio para o servidor
        // Por agora, apenas simular sucesso
        alert('Avaliação enviada com sucesso!');
        closeFeedbackModal();
    });
}

/* ==========================================
   FUNÇÕES DE NAVEGAÇÃO E AÇÕES
========================================== */

function openAddServiceModal() {
    window.location.href = '/Views/orders/createService.php';
}

function editService(serviceId) {
    window.location.href = '/Views/orders/editService.php?id=' + encodeURIComponent(serviceId);
}

function viewOrderDetails(orderId) {
    alert('Funcionalidade de detalhes do pedido em desenvolvimento');
}

function viewServiceStats(serviceId) {
    alert('Funcionalidade de estatísticas do serviço em desenvolvimento. ID: ' + serviceId);
}

/* ==========================================
   CARRINHO DE COMPRAS
========================================== */

function addToCart(orderId) {
    const formData = new FormData();
    formData.append('action', 'get_order_for_cart');
    formData.append('order_id', orderId);
    
    // Mostrar loading no botão
    const button = document.querySelector(`button[onclick="addToCart(${orderId})"]`);
    if (!button) return;
    
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adicionando...';
    button.disabled = true;
    
    // Enviar requisição para o serviceController
    fetch('/Controllers/serviceController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            alert('Pedido adicionado ao carrinho com sucesso!');
            updateCartCounter(result.total);
        } else {
            alert('Erro ao adicionar ao carrinho: ' + (result.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        console.error('Erro ao adicionar ao carrinho:', error);
        alert('Erro inesperado ao adicionar ao carrinho.');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function updateCartCounter(total) {
    const cartCounter = document.querySelector('.cart-counter');
    if (cartCounter) {
        cartCounter.textContent = total;
        cartCounter.style.display = total > 0 ? 'block' : 'none';
    }
}

/* ==========================================
   EVENTOS GLOBAIS
========================================== */

// Fechar modal clicando fora dele
window.addEventListener('click', function(event) {
    const modal = document.getElementById('feedbackModal');
    if (modal && event.target === modal) {
        closeFeedbackModal();
    }
});

// Inicialização quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    // Configurar funcionalidades baseadas na página atual
    setupPriceCalculation();
    setupCharacterCounter();
    setupImagePreview();
    setupFormValidation();
    setupRatingStars();
    setupFeedbackForm();
});