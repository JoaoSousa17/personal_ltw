/**
 * Lógica para controlo de comportamento interativo de uma pergunta da faq
 * Permite abrir/fechar respostas ao clicar na pergunta, garantindo que apenas uma está aberta de cada vez.
 */
document.addEventListener('DOMContentLoaded', function() {

    /* Seleciona todas as perguntas da secção de FAQ */
    const faqQuestions = document.querySelectorAll('.faq-question');

    /* Adiciona o evento de clique a cada pergunta */
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {

            /* Obtém o item (pai da pergunta) e verifica se já está ativo */
            const item = this.parentNode;
            const isActive = item.classList.contains('active');

            /* Fecha todos os itens ativos e redefine o ícone */
            document.querySelectorAll('.faq-item').forEach(el => {
                el.classList.remove('active');
                el.querySelector('.toggle-icon').textContent = '+';
            });

            /* Se o item clicado ainda não estava ativo, abre-o */
            if (!isActive) {
                item.classList.add('active');
                item.querySelector('.toggle-icon').textContent = '-';
            }
        });
    });
});
