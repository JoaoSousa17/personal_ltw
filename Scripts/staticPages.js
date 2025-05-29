/*======================================
   JAVASCRIPT PARA PÁGINAS ESTÁTICAS
======================================*/

// Aguardar carregamento do DOM
document.addEventListener('DOMContentLoaded', function() {
    
    /*------------------
    FAQ Toggle Animation
    ------------------*/
    function initFAQToggle() {
        const faqItems = document.querySelectorAll('.faq-item');

        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            const answer = item.querySelector('.faq-answer');
            const toggleIcon = item.querySelector('.toggle-icon');

            if (question && answer && toggleIcon) {
                question.addEventListener('click', function() {
                    const isActive = item.classList.contains('active');

                    // Fechar todos os outros itens FAQ
                    faqItems.forEach(otherItem => {
                        if (otherItem !== item) {
                            otherItem.classList.remove('active');
                            const otherAnswer = otherItem.querySelector('.faq-answer');
                            const otherIcon = otherItem.querySelector('.toggle-icon');
                            if (otherAnswer) otherAnswer.style.maxHeight = null;
                            if (otherIcon) otherIcon.textContent = '+';
                        }
                    });

                    // Toggle do item atual
                    if (isActive) {
                        item.classList.remove('active');
                        answer.style.maxHeight = null;
                        toggleIcon.textContent = '+';
                    } else {
                        item.classList.add('active');
                        answer.style.maxHeight = answer.scrollHeight + 'px';
                        toggleIcon.textContent = '−';
                    }
                });
            }
        });
    }

    /*------------------
    Smooth Scroll para Links Internos
    ------------------*/
    function initSmoothScroll() {
        const internalLinks = document.querySelectorAll('a[href^="#"]');
        
        internalLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    const headerOffset = 80; // Offset para header fixo
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    /*------------------
    Validação de Formulário de Contacto
    ------------------*/
    function initContactFormValidation() {
        const contactForm = document.querySelector('.contact-form');
        
        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                let isValid = true;
                const requiredFields = this.querySelectorAll('[required]');
                
                // Remover mensagens de erro anteriores
                const existingErrors = this.querySelectorAll('.field-error');
                existingErrors.forEach(error => error.remove());
                
                requiredFields.forEach(field => {
                    const value = field.value.trim();
                    
                    if (!value) {
                        showFieldError(field, 'Este campo é obrigatório.');
                        isValid = false;
                    } else if (field.type === 'email' && !isValidEmail(value)) {
                        showFieldError(field, 'Por favor, insira um email válido.');
                        isValid = false;
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    // Scroll para o primeiro erro
                    const firstError = this.querySelector('.field-error');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        }
    }

    /*------------------
    Mostrar Erro do Campo
    ------------------*/
    function showFieldError(field, message) {
        const formGroup = field.closest('.form-group');
        if (formGroup) {
            const errorElement = document.createElement('div');
            errorElement.className = 'field-error';
            errorElement.textContent = message;
            errorElement.style.color = '#e74c3c';
            errorElement.style.fontSize = '0.9rem';
            errorElement.style.marginTop = '5px';
            
            formGroup.appendChild(errorElement);
            field.style.borderColor = '#e74c3c';
            
            // Remover erro quando o campo for corrigido
            field.addEventListener('input', function() {
                if (this.value.trim()) {
                    errorElement.remove();
                    this.style.borderColor = '';
                }
            });
        }
    }

    /*------------------
    Validar Email
    ------------------*/
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    /*------------------
    Animação de Entrada para Elementos
    ------------------*/
    function initScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);

        // Observar elementos que devem ser animados
        const animatedElements = document.querySelectorAll(
            '.value-card, .team-member, .service-item, .feature-item, .contact-info-item, .schedule-card'
        );
        
        animatedElements.forEach(el => {
            el.classList.add('animate-on-scroll');
            observer.observe(el);
        });
    }

    /*------------------
    Auto-Dismiss para Alertas
    ------------------*/
    function initAlertAutoDismiss() {
        const alerts = document.querySelectorAll('.alert');
        
        alerts.forEach(alert => {
            // Auto-dismiss após 5 segundos
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }, 5000);

            // Adicionar botão de fechar se não existir
            if (!alert.querySelector('.alert-close')) {
                const closeBtn = document.createElement('button');
                closeBtn.className = 'alert-close';
                closeBtn.innerHTML = '×';
                closeBtn.style.cssText = `
                    position: absolute;
                    top: 10px;
                    right: 15px;
                    background: none;
                    border: none;
                    font-size: 20px;
                    cursor: pointer;
                    opacity: 0.7;
                `;
                
                closeBtn.addEventListener('click', () => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => alert.remove(), 300);
                });
                
                alert.style.position = 'relative';
                alert.appendChild(closeBtn);
            }
        });
    }

    /*------------------
    Melhorar UX dos Links Externos
    ------------------*/
    function initExternalLinks() {
        const externalLinks = document.querySelectorAll('a[href^="http"]:not([href*="' + window.location.hostname + '"])');
        
        externalLinks.forEach(link => {
            link.setAttribute('target', '_blank');
            link.setAttribute('rel', 'noopener noreferrer');
            
            // Adicionar ícone visual para links externos (opcional)
            if (!link.querySelector('.external-icon')) {
                const icon = document.createElement('span');
                icon.className = 'external-icon';
                icon.innerHTML = ' ↗';
                icon.style.fontSize = '0.8em';
                link.appendChild(icon);
            }
        });
    }

    /*------------------
    Inicializar todas as funcionalidades
    ------------------*/
    initFAQToggle();
    initSmoothScroll();
    initContactFormValidation();
    initScrollAnimations();
    initAlertAutoDismiss();
    initExternalLinks();

    // Log para debug (remover em produção)
    console.log('Static pages JavaScript initialized');
});

/*------------------
CSS para Animações (adicionar ao staticPages.css)
------------------*/
const animationStyles = `
    .animate-on-scroll {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }
    
    .animate-on-scroll.animate-in {
        opacity: 1;
        transform: translateY(0);
    }
    
    .alert {
        transition: opacity 0.3s ease-out, transform 0.3s ease-out;
    }
    
    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
    }
    
    .faq-item.active .faq-answer {
        transition: max-height 0.4s ease-out;
    }
    
    .field-error {
        animation: fadeInUp 0.3s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;

// Adicionar estilos ao documento
if (!document.querySelector('#static-pages-styles')) {
    const styleSheet = document.createElement('style');
    styleSheet.id = 'static-pages-styles';
    styleSheet.textContent = animationStyles;
    document.head.appendChild(styleSheet);
}