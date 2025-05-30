<?php

require_once("../Templates/common_elems.php");
require_once("../Templates/product_elems.php");
require_once("../Templates/feedback_elems.php");
require_once("../Database/connection.php");
require_once("../Controllers/distancesCalculationController.php");
require_once("../Controllers/feedbackController.php");

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

// Debug dos dados do serviço
error_log("=== DEBUG SERVIÇO ===");
error_log("ID: " . $service['id']);
error_log("Título: " . $service['title']);
error_log("Preço por hora: " . $service['price_per_hour']);
error_log("Duração (minutos): " . $service['duration']);
error_log("Promoção: " . $service['promotion']);
error_log("====================");

// Obter feedbacks do serviço
$feedbacks = getServiceFeedbacks($serviceId);
$feedbackStats = getServiceRating($serviceId);

// Processamento de dados do serviço
$pricePerHour = floatval($service['price_per_hour']);
$duration = intval($service['duration']); // Duração em minutos
$discount = intval($service['promotion']);

// Calcular preço final com desconto
$finalPricePerHour = $pricePerHour * (1 - $discount / 100.0);
$hasDiscount = $discount > 0;

// Debug do cálculo
error_log("=== DEBUG CÁLCULOS ===");
error_log("Preço original por hora: " . $pricePerHour);
error_log("Desconto: " . $discount . "%");
error_log("Preço final por hora: " . $finalPricePerHour);
error_log("Duração em minutos: " . $duration);
error_log("======================");

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

drawHeader("Product", ["../Styles/Categories&Product.css", "../Styles/feedback.css"]);
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
            <?php 
            // Debug antes de chamar a função
            error_log("=== ANTES DE drawProductInfo ===");
            error_log("Date: " . $date);
            error_log("Title: " . $title);
            error_log("Final Price Per Hour: " . $finalPricePerHour);
            error_log("Service ID: " . $serviceId);
            error_log("Original Price Per Hour: " . ($hasDiscount ? $pricePerHour : 'null'));
            error_log("Discount: " . $discount);
            error_log("Duration (minutes): " . $duration);
            error_log("================================");
            
            drawProductInfo(
                $date, 
                $title, 
                $finalPricePerHour, // Preço por hora em EUR
                $serviceId, 
                $hasDiscount ? $pricePerHour : null, 
                $discount, 
                $duration // Duração em minutos
            ); ?>
            
            <!-- Informações do Anunciante -->
            <?php drawAdvertiserInfo($username, $profilePhotoId); ?>
        </div>
    </div>
    
    <!-- Seção de Feedbacks -->
    <?php drawFeedbackSection($feedbacks, $feedbackStats, $serviceId); ?>
    
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
<script src="/Scripts/order.js"></script>
<script src="/Scripts/popupMessages.js"></script>

<?php drawFooter(); ?>
