/**
 * Lógica de filtragem de pedidos de desbloqueio por estado (status).
 * Os pedidos são filtrados dinamicamente com base no botão selecionado.
 */
document.addEventListener('DOMContentLoaded', function() {

    /* Seleciona todos os botões de filtro de pedidos */
    const filterButtons = document.querySelectorAll('.appeal-filter-btn');

    /* Seleciona todos os cartões de pedidos */
    const appealCards = document.querySelectorAll('.appeal-card');

    /* Adiciona comportamento a cada botão de filtro */
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {

            /* Atualiza o botão ativo (visualmente) */
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            /* Obtém o valor do filtro (ex: 'pending', 'approved', 'rejected') */
            const filter = this.getAttribute('data-filter');

            /* Mostra ou oculta os cartões de acordo com o filtro selecionado */
            appealCards.forEach(card => {
                const status = card.getAttribute('data-status');
                if (filter === 'all' || status === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    /* Aplica automaticamente o filtro "pending" ao carregar a página */
    const pendingFilterButton = document.querySelector('[data-filter="pending"]');
    if (pendingFilterButton) {
        pendingFilterButton.click();
    }
});
