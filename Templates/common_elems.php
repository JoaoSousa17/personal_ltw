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
    // Verificar se existe sessão ativa
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    } ?>
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
                <!-- Botão do carrinho --> 
                <a href="/Views/cart.php" id="carrinho-button"> 
                    <img src="/Images/site/header/carrinho-de-compras.png" alt="Carrinho" class="top-icons"> 
                    <div id="cart-badge" <?php if($cartCount == 0) echo 'style="display: none;"'; ?>>  <!--Não apresenta no caso de não ter artigos no carrinho--> 
                        <?php echo $cartCount; ?> <!--Escreve o número de artigos no carrinho--> 
                    </div> 
                    <div id="cart-label">Carrinho</div> 
                </a>

                <!-- Botão de Notificações -->
                <a href="" id="notifications-button">
                    <img src="/Images/site/header/notifications-icon.png" alt="" class="top-icons">
                    <div id="notification-label">Notificações</div>
                </a>
                
                <!-- Botão de Perfil -->
                <div id="profile-button">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="/Views/profile/profile.php">
                            <img src="" alt=""> <!-- Colocar img do profile -->
                        </a>
                    <?php else: ?>
                        <a href="/Views/auth.php">
                            <img src="" alt=""> <!-- Colocar img do profile -->
                        </a>
                    <?php endif; ?>
                </div>
                    
                <!-- Botão Menu DropDown -->
                <div id="menu-dropdown">
                    <button id="icon-dropdown">
                        <img src="/Images/site/header/dropDown-icon.png" alt="" class="top-icons">
                    </button>
                    <div id="dropdown-content">
                        <a href="/">Home</a>
                        <a href="/sobre.html">Sobre</a>
                        <a href="/contacto.html">Contacto</a>
                        <a href="/servicos.html">Serviços</a>
                        <a href="/blog.html">Blog</a>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="/Views/profile/profile.php">O Meu Perfil</a>
                            <a href="/Views/profile/editProfile.php">Editar Perfil</a>
                            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                <a href="/Views/admin/adminPannel.php">Admin</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="/Views/auth/login.php">Login</a>
                            <a href="/Views/auth/register.php">Registar</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>
    </div>
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