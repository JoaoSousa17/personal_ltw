<!-------------------------
Usados em todas as páginas
--------------------------->

<?php
/**
 * Gera o cabeçalho HTML padrão da página, definindo o head, com o título da página e os estilos a utilizar. Adicionalmente desenha o header (top menu) do 
 * site.
 *
 * @param string $title  Título da página a ser exibido na aba do navegador.
 * @param array $styles  Lista de estilos CSS a serem incluídos.
 */
function drawHeader($title, $styles){
    // Incluir as funções de sessão
    require_once(dirname(__FILE__)."/../Utils/session.php");
    require_once(dirname(__FILE__)."/../Controllers/userController.php");
    
    // Obter dados da sessão usando as funções do utils
    $currentUser = getCurrentUser();
    $isLoggedIn = isUserLoggedIn();
    $isAdmin = isUserAdmin();
    $cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
    $userId = getCurrentUserId();
    
    // Obter URL da foto de perfil se o usuário estiver logado
    $profilePhotoUrl = null;
    if ($isLoggedIn && $userId) {
        $profilePhotoUrl = getProfilePhotoUrl($userId);
    }
    ?>
    <!DOCTYPE html>
    <html lang="pt">

    <!-- Head -->
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="/Styles/common_ellements.css">

        <!-- Inclusão de estilos CSS, enviados como argumentos da função, numa lista. -->
        <?php foreach ($styles as $style){?>
            <link rel="stylesheet" href=<?php echo $style?>>
        <?php }?>
        <title><?php echo $title?></title>
        <link rel="shortcut icon" type="imagex/png" href="/Images/site/header/icon.ico">
    </head>

    <!-- Body -->
    <body>
        <div id="site-header">
        <header id="main-header">
            <!-- Logo -->
            <a href="/Views/mainPage.php">
                <img src="/Images/site/header/logo.png" alt="Logo" id="logo-icon">
            </a>
                
            <!-- Barra de Pesquisa -->
            <div id="search-container">
            <form action="/Views/searchPages/search-results.php" method="get">
                <input type="text" placeholder="Pesquisar serviços..." name="query" id="search-input" autocomplete="off">
                <div id="search-suggestions"></div>
                <button type="submit" id="search-button">
                    <img src="/Images/site/header/search-icon.png" alt="" id="search-icon">
                </button>
            </form>

            <!-- Botão de Categorias -->
            <a href="/Views/categories.php" id="categories-button">
                <img src="/Images/site/header/categories-icon.png" alt="" class="top-icons">
                <div id="categories-label">Categorias</div>
            </a>
            </div>
                
            <div id="user-actions">
                <?php if ($isLoggedIn): ?>
                    <!-- Elementos visíveis apenas para utilizadores logados -->
                    
                    <!-- Botão do carrinho --> 
                    <a href="/Views/cart.php" id="carrinho-button"> 
                        <img src="/Images/site/header/carrinho-de-compras.png" alt="Carrinho" class="top-icons"> 
                        <div id="cart-badge" <?php if($cartCount == 0) echo 'style="display: none;"'; ?>>
                            <?php echo $cartCount; ?>
                        </div> 
                        <div id="cart-label">Carrinho</div> 
                    </a>

                    <!-- Botão de Notificações -->
                    <a href="/Views/messages.php" id="notifications-button">
                        <img src="/Images/site/header/notifications-icon.png" alt="" class="top-icons inverted-icon">
                        <div id="notification-label">Mensagens</div>
                    </a>
                    
                    <!-- Botão de Perfil -->
                    <div id="profile-button">
                        <a href="/Views/profile/profile.php">
                            <?php if ($profilePhotoUrl): ?>
                                <img src="<?php echo htmlspecialchars($profilePhotoUrl); ?>" alt="Perfil" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                            <?php else: ?>
                                <img src="/Images/site/header/genericProfile.png" alt="Perfil" style="width: 100%; height: 100%; border-radius: 50%;">
                            <?php endif; ?>
                        </a>
                    </div>
                <?php endif; ?>
                    
                <!-- Botão Menu DropDown -->
                <div id="menu-dropdown">
                    <button id="icon-dropdown">
                        <img src="/Images/site/header/dropDown-icon.png" alt="" class="top-icons">
                    </button>
                    <div id="hover-buffer"></div>
                    <div id="dropdown-content">
                        <?php if ($isLoggedIn): ?>
                            <!-- Opções para utilizador autenticado -->
                            <a href="/Views/profile/profile.php">
                                <img src="/Images/site/header/perfil.png" style="margin-right: 8px; width: 1em;">
                                Perfil
                            </a>
                            <a href="/Views/profile/editProfile.php">
                                <img src="/Images/site/header/editProfile.png" style="margin-right: 8px; width: 1em;">
                                Editar Perfil
                            </a>
                            <a href="/Views/categories.php">
                                <img src="/Images/site/header/categories.png" style="margin-right: 8px; width: 1em;">
                                Categorias
                            </a>
                            <!-- Adicionar Mensagens -->
                            <a href="/Views/orders/myOrders.php">
                                <img src="/Images/site/header/pedidos.png" style="margin-right: 8px; width: 1em;">
                                Meus Pedidos
                            </a>
                            <a href="/Views/orders/myServices.php">
                                <img src="/Images/site/header/services.png" style="margin-right: 8px; width: 1em;">
                                Meus Anúncios
                            </a>
                            <?php if ($isAdmin): ?>
                                <hr style="margin: 8px 0; border: none; border-top: 1px solid #eee;">
                                <a href="/Views/admin/adminPannel.php">
                                    <img src="/Images/site/header/admin-crown.png" style="margin-right: 8px; width: 1em;">
                                    Admin
                                </a>
                            <?php endif; ?>
                            <hr style="margin: 8px 0; border: none; border-top: 1px solid #eee;">
                            <a href="/Controllers/authController.php?action=logout">
                                <img src="/Images/site/header/terminarSessao.png" style="margin-right: 8px; width: 1.3em;">
                                Terminar Sessão
                            </a>
                        <?php else: ?>
                            <!-- Opções para utilizador não autenticado -->
                            <a href="/Views/auth.php">
                                <img src="/Images/site/header/login.png" style="margin-right: 8px; width: 1em;">
                                Login
                            </a>
                            <a href="/Views/auth.php">
                                <img src="/Images/site/header/register.png" style="margin-right: 8px; width: 1em;">
                                Registar
                            </a>
                            <a href="/Views/categories.php">
                                <img src="/Images/site/header/categories.png" style="margin-right: 8px; width: 1em;">
                                Categorias
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>
    </div>

    <!-- JavaScript para funcionalidade de pesquisa -->
    <script>
    // JavaScript para funcionalidade de pesquisa com AJAX
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input');
        const suggestionsContainer = document.getElementById('search-suggestions');
        let searchTimeout;

        if (searchInput && suggestionsContainer) {
            // Evento de digitação no campo de pesquisa
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                
                // Limpar timeout anterior
                if (searchTimeout) {
                    clearTimeout(searchTimeout);
                }
                
                // Se o campo estiver vazio ou muito curto, esconder sugestões
                if (query.length < 2) {
                    hideSuggestions();
                    return;
                }
                
                // Delay para evitar muitas requisições
                searchTimeout = setTimeout(() => {
                    fetchSuggestions(query);
                }, 300);
            });

            // Esconder sugestões ao clicar fora
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                    hideSuggestions();
                }
            });

            // Mostrar sugestões ao focar no campo (se já houver texto)
            searchInput.addEventListener('focus', function() {
                const query = this.value.trim();
                if (query.length >= 2) {
                    fetchSuggestions(query);
                }
            });

            // Navegação por teclado nas sugestões
            searchInput.addEventListener('keydown', function(e) {
                const suggestions = suggestionsContainer.querySelectorAll('.suggestion-item');
                const activeSuggestion = suggestionsContainer.querySelector('.suggestion-item.active');
                
                if (suggestions.length === 0) return;
                
                switch(e.key) {
                    case 'ArrowDown':
                        e.preventDefault();
                        if (!activeSuggestion) {
                            suggestions[0].classList.add('active');
                        } else {
                            const currentIndex = Array.from(suggestions).indexOf(activeSuggestion);
                            activeSuggestion.classList.remove('active');
                            if (currentIndex < suggestions.length - 1) {
                                suggestions[currentIndex + 1].classList.add('active');
                            } else {
                                suggestions[0].classList.add('active');
                            }
                        }
                        break;
                        
                    case 'ArrowUp':
                        e.preventDefault();
                        if (!activeSuggestion) {
                            suggestions[suggestions.length - 1].classList.add('active');
                        } else {
                            const currentIndex = Array.from(suggestions).indexOf(activeSuggestion);
                            activeSuggestion.classList.remove('active');
                            if (currentIndex > 0) {
                                suggestions[currentIndex - 1].classList.add('active');
                            } else {
                                suggestions[suggestions.length - 1].classList.add('active');
                            }
                        }
                        break;
                        
                    case 'Enter':
                        if (activeSuggestion) {
                            e.preventDefault();
                            activeSuggestion.click();
                        }
                        break;
                        
                    case 'Escape':
                        hideSuggestions();
                        searchInput.blur();
                        break;
                }
            });
        }

        // Função para buscar sugestões via AJAX
        function fetchSuggestions(query) {
            // Mostrar indicador de carregamento
            showLoadingSuggestions();
            
            fetch(`/Controllers/searchBarController.php?action=suggestions&query=${encodeURIComponent(query)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na requisição');
                    }
                    return response.json();
                })
                .then(data => {
                    displaySuggestions(data, query);
                })
                .catch(error => {
                    console.error('Erro ao buscar sugestões:', error);
                    hideSuggestions();
                });
        }

        // Função para exibir as sugestões
        function displaySuggestions(suggestions, query) {
            if (!suggestions || suggestions.length === 0) {
                hideSuggestions();
                return;
            }

            let html = '';
            suggestions.forEach((suggestion, index) => {
                // Destacar o texto pesquisado
                const highlightedName = highlightMatch(suggestion.name, query);
                html += `<div class="suggestion-item" data-id="${suggestion.id}" data-name="${suggestion.name}">
                            <div class="suggestion-content">
                                <span class="suggestion-name">${highlightedName}</span>
                            </div>
                         </div>`;
            });

            suggestionsContainer.innerHTML = html;
            suggestionsContainer.style.display = 'block';

            // Adicionar eventos de clique às sugestões
            const suggestionItems = suggestionsContainer.querySelectorAll('.suggestion-item');
            suggestionItems.forEach(item => {
                item.addEventListener('click', function() {
                    const serviceName = this.getAttribute('data-name');
                    searchInput.value = serviceName;
                    hideSuggestions();
                    
                    // Opcional: redirecionar automaticamente para a pesquisa
                    // window.location.href = `/Views/searchPages/search-results.php?query=${encodeURIComponent(serviceName)}`;
                });

                // Adicionar hover effect
                item.addEventListener('mouseenter', function() {
                    // Remover active de todos
                    suggestionItems.forEach(s => s.classList.remove('active'));
                    // Adicionar active ao atual
                    this.classList.add('active');
                });
            });
        }

        // Função para mostrar indicador de carregamento
        function showLoadingSuggestions() {
            suggestionsContainer.innerHTML = '<div class="suggestion-loading">Buscando...</div>';
            suggestionsContainer.style.display = 'block';
        }

        // Função para esconder sugestões
        function hideSuggestions() {
            suggestionsContainer.style.display = 'none';
            suggestionsContainer.innerHTML = '';
        }

        // Função para destacar o texto correspondente
        function highlightMatch(text, query) {
            if (!query) return text;
            
            const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
            return text.replace(regex, '<strong>$1</strong>');
        }
    });
    </script>
<?php }
/**
 * Desenha o rodapé da página com navegação, informações de contacto, links úteis, redes sociais e formulário de subscrição à newsletter.
 */
function drawFooter(){ ?>
        <footer id="site-footer">
            <div id="footer-container">
            
                <!-- Secção de Navegação por Páginas do Site -->
                <div class="footer-section">
                    <h3>Navegação</h3>
                    <ul>
                        <li><a href="/Views/mainPage.php">Home</a></li>
                        <li><a href="/Views/staticPages/aboutUS.php">Sobre Nós</a></li>
                        <li><a href="/Views/staticPages/services.php">Serviços</a></li>
                        <li><a href="/Views/categories.php">Categorias</a></li>
                    </ul>
                </div>
                
                <!-- Secção de Navegação por Páginas de Suporte e de Consulta de Documentação do Site -->
                <div class="footer-section">
                    <h3>Suporte</h3>
                    <ul>
                        <li><a href="/Views/staticPages/faq.php">FAQ</a></li>
                        <li><a href="/Views/staticPages/contact.php">Contacto</a></li>
                        <li><a href="/Views/staticPages/terms.php">Termos e Condições</a></li>
                        <li><a href="/Views/staticPages/privacy.php">Política de Privacidade</a></li>
                    </ul>
                </div>
                    
                <!-- Secção de Contactos e Redes Sociais -->
                <div class="footer-section">
                    <!-- Contactos -->
                    <h3>Contacte-nos</h3>
                    <p><img src="/Images/site/footer/mail.png" alt="" id="icon-email"> geral@handee.pt</p>
                    <p><img src="/Images/site/footer/phone.png" alt="" id="icon-phone"> +351 123 456 789</p>

                    <!-- Redes Sociais -->
                    <div class="social-media">
                        <a href="https://www.facebook.com" class="social-icon">
                            <img src="/Images/site/footer/socialMedia/facebook.png" alt="FB" class="social-icon">
                        </a>
                        <a href="https://www.instagram.com" class="social-icon">
                            <img src="/Images/site/footer/socialMedia/instagram.png" alt="IG" class="social-icon">
                        </a>
                        <a href="https://x.com" class="social-icon">
                            <img src="/Images/site/footer/socialMedia/x.png" alt="X" class="social-icon">
                        </a>
                        <a href="https://www.linkedin.com" class="social-icon">
                            <img src="/Images/site/footer/socialMedia/linkedin.png" alt="LI" class="social-icon">
                        </a>
                    </div>
                </div>
                    
                <!-- Secção de Newsletter -->
                <div class="footer-section">
                    <h3>Newsletter</h3>
                    <p>Subscreva para receber novidades</p>
                    <form class="newsletter-form" action="/Controllers/newsletterController.php" method="POST">
                        <input type="email" placeholder="O seu email" name="email">
                        <button type="submit">Subscrever</button>
                    </form>
                </div>
            </div>
            
            <!-- Secção de Footer clássico, aviso copyright -->
            <div class="footer-bottom">
                <p>&copy; 2025 - Handee. Todos os direitos reservados.</p>
            </div>
        </footer>
    </body>
</html>
<?php }

/**
 * Desenha o cabeçalho de uma secção com título, parágrafo ("" para sem parágrafo) e, opcionalmente, um botão de voltar.
 *
 * @param string $header1     Título principal da secção.
 * @param string $paragraph   Texto descritivo da secção.
 * @param bool $backButton    Booleano indicativo da existência de botão de retorno (por defeito: false).
 */
function drawSectionHeader($header1, $paragraph, $backButton = false){ ?>
    <div class="section-header">
        <?php if($backButton) { ?>
            <a href="/Views/admin/adminPannel.php" id="link-back-button">
                <img src="/Images/site/admin/backArrow.png" alt="" class="back-button">
            </a>
        <?php } ?>
        <h2><?php echo $header1 ?></h2>
        <p><?php echo $paragraph ?></p>
    </div>
<?php }

/*-----------------------------------
Usados em todas as páginas estáticas
-----------------------------------*/

/**
 * Desenha uma secção principal de uma página estática com cabeçalho, uma imagem e texto, de momento, fictício.
 *
 * @param string $header1     Título principal da secção (passado para drawSectionHeader).
 * @param string $paragraph   Parágrafo descritivo da secção (passado para drawSectionHeader).
 * @param string $imagePath   Caminho da imagem a ser exibida na secção.
 * @param int $num            Número de parágrafos de texto fictício a serem exibidos (por defeito: 3).
 */
function drawMainSection($header1, $paragraph, $imagePath, $num = 3){ ?>
    <section class="main-section">
        <?php drawSectionHeader($header1, $paragraph)?>
        <div class="main-content">
            <div class="main-image">
                <img src="<?php echo $imagePath ?>" alt="">
            </div>
            <div class="main-text">
                <?php for ($i = 0; $i < $num; $i++) {
                    echo "Lorem ipsum dolor sit amet consectetur, adipisicing elit. Eaque nesciunt voluptatum optio quam, reiciendis quisquam illo! Nostrum debitis voluptatem dolore quisquam amet commodi aspernatur fuga cum neque. Tenetur, ad labore.</p>";
                } ?>
            </div>
        </div>
    </section>
<?php }

function drawBanner($header1, $paragraph){
    ?>
        <section class="banner">
            <div class="banner-content">
                <h1><?php echo $header1 ?></h1>
                <p><?php echo $paragraph ?></p>
            </div>
        </section>
    <?php }

/*--------------------------------
Usados em páginas administrativas
--------------------------------*/

/**
 * Desenha um sub-título de uma página, para nomear uma secção.
 *
 * @param string $text Texto do aub-título a ser exibido.
 */
function drawSectionTitle($text) { ?>
    <div class="section-title">
        <h3><?php echo $text; ?></h3>
    </div>
<?php } ?>