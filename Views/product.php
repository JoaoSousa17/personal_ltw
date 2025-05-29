<?php

require_once("../Templates/common_elems.php");
require_once("../Templates/product_elems.php");
require_once("../Database/connection.php");
require_once("../Controllers/distancesCalculationController.php");

// Verificar autenticação do utilizador
$loggedInUser = $_SESSION['user_id'] ?? null;

if (!$loggedInUser) {
    $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    header("Location: /Views/auth.php");
    exit();
}

// Validar parâmetro de ID do serviço
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Serviço inválido.</p>";
    drawFooter();
    exit;
}

$serviceId = intval($_GET['id']);
$db = getDatabaseConnection();

// Obter informações detalhadas do serviço
try {
    $query = "
        SELECT S.id, S.name_ AS title, S.description_, S.price_per_hour, S.promotion, S.duration,
               U.name_ AS freelancer_name, U.profile_photo, S.freelancer_id, S.category_id,
               C.name_ AS category_name,
               GROUP_CONCAT(M.path_) AS image_paths
        FROM Service_ S
        JOIN User_ U ON S.freelancer_id = U.id
        JOIN Category C ON S.category_id = C.id
        LEFT JOIN Media M ON M.service_id = S.id
        WHERE S.id = ?
        GROUP BY S.id
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$serviceId]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Obter data de criação do serviço
    $dateQuery = "
        SELECT MIN(date_) AS creation_date
        FROM Service_Data
        WHERE service_id = ?
    ";
    
    $stmt = $db->prepare($dateQuery);
    $stmt->execute([$serviceId]);
    $dateResult = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Erro ao obter dados do serviço: " . $e->getMessage());
    echo "<p>Erro ao carregar o serviço.</p>";
    drawFooter();
    exit;
}

// Verificar se o serviço existe
if (!$service) {
    echo "<p>Serviço não encontrado.</p>";
    drawFooter();
    exit;
}

// Processamento de dados do serviço
$price = $service['price_per_hour'];
$duration = isset($service['duration']) ? (int)$service['duration'] : 0;
$discount = $service['promotion'];
$finalPrice = $price * (1 - $discount / 100.0);
$hasDiscount = $discount > 0;

// Formatação de preço e conversão de moeda
$displayPrice = convertAndFormatPrice($finalPrice, $loggedInUser);

// Processamento de imagens
$images = explode(",", $service['image_paths'] ?? "https://upload.wikimedia.org/wikipedia/commons/a/a3/Image-not-found.png");

// Dados para exibição
$categories = [$service['category_name']];
$description = $service['description_'];
$title = $service['title'];
$username = $service['freelancer_name'];
$profilePhotoId = $service['profile_photo'];

// Formatação da data
$rawDate = $dateResult['creation_date'] ?? null;
$date = $rawDate ? date("d \ F \ Y", strtotime($rawDate)) : "Data desconhecida";

drawHeader("Product", ["../Styles/Categories&Product.css"]);
?>

<main class="page-container">
    <!-- Container Principal do Produto -->
    <div class="product-container">
        
        <!-- Lado Esquerdo: Imagens e Descrição -->
        <div class="product-image-side">
            <!-- Carousel de Imagens -->
            <?php drawProductImageCarousel($images); ?>
            
            <!-- Descrição do Produto -->
            <?php drawProductDescription($description, $categories); ?>
        </div>
        
        <!-- Lado Direito: Informações e Freelancer -->
        <div class="product-info-side">
            <!-- Informações do Produto -->
            <?php drawProductInfo(
                $date, 
                $title, 
                $finalPrice, 
                $serviceId, 
                $hasDiscount ? $price : null, 
                $discount, 
                $duration
            ); ?>
            
            <!-- Informações do Anunciante -->
            <?php drawAdvertiserInfo($username, $profilePhotoId); ?>
        </div>
    </div>
    
    <!-- Popup de Mensagens -->
    <?php drawMessagePopup($username); ?>
</main>

<!-- Variáveis JavaScript -->
<script>
    const currentUserId = <?= json_encode($loggedInUser) ?>;
    const receiverId = <?= json_encode($service['freelancer_id']) ?>;
    const serviceId = <?= json_encode($service['id']) ?>;
</script>

<!-- Scripts da Página -->
<script src="/Scripts/image-slide.js"></script>
<script src="/Scripts/orders.js"></script>
<script src="/Scripts/messages.js"></script>

<?php drawFooter(); ?>