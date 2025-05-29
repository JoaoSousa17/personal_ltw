// search.js - JavaScript consolidado para páginas de pesquisa

/* ==========================================
   FILTROS DE PESQUISA
========================================== */

// Configurar auto-submit de filtros (opcional)
function setupFilterAutoSubmit() {
    const categorySelect = document.getElementById('category');
    const filterForm = document.getElementById('filter-form');
    
    if (!categorySelect || !filterForm) return;
    
    // Opcional: submeter automaticamente quando categoria muda
    // categorySelect.addEventListener('change', function() {
    //     filterForm.submit();
    // });
}

/* ==========================================
   PAGINAÇÃO DE CATEGORIAS
========================================== */

// Configurar navegação de paginação para categorias
function setupCategoriesPagination() {
    const paginationBtns = document.querySelectorAll('.pagination-btn');
    
    paginationBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.disabled) return;
            
            // Implementar lógica de paginação aqui
            // Por enquanto apenas placeholder
            console.log('Pagination clicked');
        });
    });
}

/* ==========================================
   ANIMAÇÕES E EFEITOS
========================================== */

// Configurar efeitos visuais nos cards
function setupCardEffects() {
    const serviceCards = document.querySelectorAll('.service-card');
    const categoryCards = document.querySelectorAll('.category-card');
    
    // Efeitos para cards de serviços
    serviceCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '';
        });
    });
    
    // Efeitos para cards de categorias
    categoryCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
            this.style.boxShadow = '0 6px 20px rgba(0,0,0,0.1)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '';
        });
    });
}

/* ==========================================
   VALIDAÇÃO DE FILTROS
========================================== */

// Validar campos de preço
function setupPriceValidation() {
    const minPriceInput = document.getElementById('min_price');
    const maxPriceInput = document.getElementById('max_price');
    
    if (!minPriceInput || !maxPriceInput) return;
    
    function validatePriceRange() {
        const minPrice = parseFloat(minPriceInput.value) || 0;
        const maxPrice = parseFloat(maxPriceInput.value) || Infinity;
        
        if (minPrice > maxPrice && maxPrice !== Infinity) {
            maxPriceInput.setCustomValidity('O preço máximo deve ser maior que o mínimo');
        } else {
            maxPriceInput.setCustomValidity('');
        }
    }
    
    minPriceInput.addEventListener('input', validatePriceRange);
    maxPriceInput.addEventListener('input', validatePriceRange);
}

/* ==========================================
   LAZY LOADING DE IMAGENS
========================================== */

// Configurar lazy loading para imagens
function setupLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    } else {
        // Fallback para browsers sem suporte
        images.forEach(img => {
            img.src = img.dataset.src;
        });
    }
}

/* ==========================================
   FUNCIONALIDADES DE PESQUISA AVANÇADA
========================================== */

// Configurar limpeza de filtros
function setupFilterReset() {
    const resetBtn = document.getElementById('reset-filters');
    const filterForm = document.getElementById('filter-form');
    
    if (!resetBtn || !filterForm) return;
    
    resetBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Limpar todos os campos exceto a query
        const inputs = filterForm.querySelectorAll('input:not([name="query"]), select');
        inputs.forEach(input => {
            if (input.type === 'number') {
                input.value = '';
            } else if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
            }
        });
        
        // Submeter o formulário limpo
        filterForm.submit();
    });
}

/* ==========================================
   NAVEGAÇÃO E HISTÓRICO
========================================== */

// Configurar navegação com histórico do browser
function setupBrowserHistory() {
    const filterForm = document.getElementById('filter-form');
    
    if (!filterForm) return;
}

/* ==========================================
   INICIALIZAÇÃO
========================================== */

// Inicializar todas as funcionalidades quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    setupFilterAutoSubmit();
    setupCategoriesPagination();
    setupCardEffects();
    setupPriceValidation();
    setupLazyLoading();
    setupFilterReset();
    setupBrowserHistory();
});