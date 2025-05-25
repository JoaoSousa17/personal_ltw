document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const suggestionsContainer = document.getElementById('search-suggestions');
    let typingTimer;
    const doneTypingInterval = 300; // Tempo de espera após o usuário parar de digitar (em ms)

    if (!searchInput || !suggestionsContainer) return;

    // Evento de input para detectar quando o usuário digita
    searchInput.addEventListener('input', function() {
        clearTimeout(typingTimer);
        if (searchInput.value.length >= 2) {
            typingTimer = setTimeout(fetchSuggestions, doneTypingInterval);
        } else {
            suggestionsContainer.innerHTML = '';
            suggestionsContainer.style.display = 'none';
        }
    });

    // Evento de foco para mostrar sugestões quando o campo recebe foco
    searchInput.addEventListener('focus', function() {
        if (searchInput.value.length >= 2) {
            fetchSuggestions();
        }
    });

    // Fechar sugestões ao clicar fora do campo de pesquisa
    document.addEventListener('click', function(event) {
        if (!searchInput.contains(event.target) && !suggestionsContainer.contains(event.target)) {
            suggestionsContainer.style.display = 'none';
        }
    });

    // Função para buscar sugestões via AJAX
    function fetchSuggestions() {
        const query = searchInput.value.trim();
        
        if (query.length < 2) {
            suggestionsContainer.innerHTML = '';
            suggestionsContainer.style.display = 'none';
            return;
        }

        fetch(`/Controllers/SearchBarController.php?action=suggestions&query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                displaySuggestions(data);
            })
            .catch(error => {
                console.error('Erro ao obter sugestões:', error);
            });
    }

    // Função para exibir as sugestões
    function displaySuggestions(suggestions) {
        suggestionsContainer.innerHTML = '';
        
        if (suggestions.length === 0) {
            suggestionsContainer.style.display = 'none';
            return;
        }

        suggestions.forEach(suggestion => {
            const div = document.createElement('div');
            div.className = 'suggestion-item';
            div.textContent = suggestion.name;
            
            div.addEventListener('click', function() {
                searchInput.value = suggestion.name;
                suggestionsContainer.style.display = 'none';
                
                // Opcional: submeter o formulário automaticamente
                // searchInput.closest('form').submit();
            });
            
            suggestionsContainer.appendChild(div);
        });
        
        suggestionsContainer.style.display = 'block';
    }

    // Navegação pelo teclado nas sugestões
    searchInput.addEventListener('keydown', function(e) {
        const items = suggestionsContainer.querySelectorAll('.suggestion-item');
        
        if (!items.length) return;
        
        const activeItem = suggestionsContainer.querySelector('.active');
        
        switch(e.key) {
            case 'ArrowDown':
                e.preventDefault();
                if (!activeItem) {
                    items[0].classList.add('active');
                } else {
                    const nextItem = activeItem.nextElementSibling;
                    if (nextItem) {
                        activeItem.classList.remove('active');
                        nextItem.classList.add('active');
                    }
                }
                break;
                
            case 'ArrowUp':
                e.preventDefault();
                if (activeItem) {
                    const prevItem = activeItem.previousElementSibling;
                    activeItem.classList.remove('active');
                    if (prevItem) {
                        prevItem.classList.add('active');
                    }
                }
                break;
                
            case 'Enter':
                if (activeItem) {
                    e.preventDefault();
                    searchInput.value = activeItem.textContent;
                    suggestionsContainer.style.display = 'none';
                }
                break;
                
            case 'Escape':
                suggestionsContainer.style.display = 'none';
                break;
        }
    });
});
