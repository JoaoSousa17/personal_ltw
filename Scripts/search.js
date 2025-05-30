// search.js - Versão simples que funciona com qualquer CSS

document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando filtros...');
    
    // Configurar auto-submit para categoria
    const categorySelect = document.getElementById('category');
    const filterForm = document.getElementById('filter-form');
    
    if (categorySelect && filterForm) {
        categorySelect.addEventListener('change', function() {
            console.log('Categoria mudou para:', this.value);
            filterForm.submit();
        });
    }
    
    // Configurar botão de reset
    const resetBtn = document.getElementById('reset-filters');
    if (resetBtn && filterForm) {
        resetBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Limpar campos
            const categorySelect = document.getElementById('category');
            const minPrice = document.getElementById('min_price');
            const maxPrice = document.getElementById('max_price');
            
            if (categorySelect) categorySelect.selectedIndex = 0;
            if (minPrice) minPrice.value = '';
            if (maxPrice) maxPrice.value = '';
            
            // Submeter formulário
            filterForm.submit();
        });
    }
    
    // Auto-submit para preços (com delay)
    const minPriceInput = document.getElementById('min_price');
    const maxPriceInput = document.getElementById('max_price');
    let priceTimeout;
    
    function handlePriceChange() {
        clearTimeout(priceTimeout);
        priceTimeout = setTimeout(() => {
            if (filterForm) {
                filterForm.submit();
            }
        }, 1500); // 1.5 segundos de delay
    }
    
    if (minPriceInput) {
        minPriceInput.addEventListener('input', handlePriceChange);
    }
    
    if (maxPriceInput) {
        maxPriceInput.addEventListener('input', handlePriceChange);
    }
    
    // Validação simples de preços
    function validatePrices() {
        if (!minPriceInput || !maxPriceInput) return;
        
        const minPrice = parseFloat(minPriceInput.value) || 0;
        const maxPrice = parseFloat(maxPriceInput.value) || 0;
        
        if (minPrice < 0) {
            minPriceInput.setCustomValidity('Preço mínimo não pode ser negativo');
        } else {
            minPriceInput.setCustomValidity('');
        }
        
        if (maxPrice < 0) {
            maxPriceInput.setCustomValidity('Preço máximo não pode ser negativo');
        } else if (maxPrice > 0 && minPrice > maxPrice) {
            maxPriceInput.setCustomValidity('Preço máximo deve ser maior que o mínimo');
        } else {
            maxPriceInput.setCustomValidity('');
        }
    }
    
    if (minPriceInput && maxPriceInput) {
        minPriceInput.addEventListener('blur', validatePrices);
        maxPriceInput.addEventListener('blur', validatePrices);
    }
    
    // Efeitos simples nos cards (hover)
    const serviceCards = document.querySelectorAll('.service-card');
    serviceCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'transform 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    const categoryCards = document.querySelectorAll('.category-card');
    categoryCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
            this.style.transition = 'transform 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    console.log('Filtros inicializados com sucesso');
});
