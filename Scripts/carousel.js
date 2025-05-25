document.addEventListener('DOMContentLoaded', function() {
    const slider = document.querySelector(".items");
    const slides = document.querySelectorAll(".item");
    const buttons = document.querySelectorAll(".button");
    const totalSlides = slides.length;
    
    let current = 0;
    let prev = totalSlides - 1;
    let prev2 = totalSlides - 2;
    let next = 1;
    let next2 = 2;
    
    // Adiciona os event listeners aos botões
    for (let i = 0; i < buttons.length; i++) {
        buttons[i].addEventListener("click", () => i == 0 ? gotoPrev() : gotoNext());
    }
    
    // Função para ir para o slide anterior
    const gotoPrev = () => {
        current = current > 0 ? current - 1 : totalSlides - 1;
        updateSlides();
    };
    
    // Função para ir para o próximo slide
    const gotoNext = () => {
        current = current < totalSlides - 1 ? current + 1 : 0;
        updateSlides();
    };
    
    // Atualiza as classes dos slides
    const updateSlides = () => {
        // Calcula os índices dos slides anterior e próximo
        prev = (current - 1 + totalSlides) % totalSlides;
        prev2 = (current - 2 + totalSlides) % totalSlides;
        next = (current + 1) % totalSlides;
        next2 = (current + 2) % totalSlides;
        
        // Remove todas as classes dos slides
        for (let i = 0; i < totalSlides; i++) {
            slides[i].classList.remove("active", "prev", "next", "prev-2", "next-2");
        }
        
        // Adiciona as classes apropriadas
        slides[current].classList.add("active");
        slides[prev].classList.add("prev");
        slides[next].classList.add("next");
        slides[prev2].classList.add("prev-2");
        slides[next2].classList.add("next-2");
    };
    
    // Inicializa o carrossel
    updateSlides();
    
    // Adiciona suporte para swipe em dispositivos móveis
    let touchStartX = 0;
    let touchEndX = 0;
    
    slider.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
    });
    
    slider.addEventListener('touchend', e => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });
    
    const handleSwipe = () => {
        if (touchEndX < touchStartX) {
            gotoNext(); // Swipe para a esquerda, vai para o próximo slide
        } else if (touchEndX > touchStartX) {
            gotoPrev(); // Swipe para a direita, vai para o slide anterior
        }
    };
});