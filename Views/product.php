<?php
require_once("../Templates/common_elems.php");
require_once("../Templates/product_elems.php");
require_once("../Database/connection.php");

drawHeader("Product", ["../Styles/product.css"]);

session_start();
$loggedInUser = $_SESSION['user_id'] ?? null;

if (!$loggedInUser) {
    $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    header("Location: /Views/auth.php");
    exit();
}

$serviceId = 7;                // PARA TESTAR APENAS
/*if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Serviço inválido.</p>";
    drawFooter();
    exit;
}*/

//$serviceId = intval($_GET['id']);
$db = getDatabaseConnection();

// Consulta SQL
$query = "
SELECT S.id, S.name_ AS title, S.description_, S.price_per_hour, S.promotion,
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

$dateQuery = "
    SELECT MIN(date_) AS creation_date
    FROM Service_Data
    WHERE service_id = ?
";

$stmt = $db->prepare($dateQuery);
$stmt->execute([$serviceId]);
$dateResult = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    echo "<p>Serviço não encontrado.</p>";
    drawFooter();
    exit;
}

// Cálculos e variáveis
$price = $service['price_per_hour'];
$discount = $service['promotion'];
$finalPrice = $price * (1 - $discount / 100.0);
$images = explode(",", $service['image_paths'] ?? "https://upload.wikimedia.org/wikipedia/commons/a/a3/Image-not-found.png");
$categories = [$service['category_name']];
$description = $service['description_'];
$title = $service['title'];
$username = $service['freelancer_name'];
$profilePhotoId = $service['profile_photo'];
$rawDate = $dateResult['creation_date'] ?? null;
$date = $rawDate ? date("d \ F \ Y", strtotime($rawDate)) : "Data desconhecida";
?>

<div class="page-container">
  <div class="product-container">
    <div class="product-image-side">
      <?php drawProductImageCarousel($images); ?>
      <?php drawProductDescription($description, $categories); ?>
    </div>

    <div class="product-info-side">
      <?php drawProductInfo($date, $title, $finalPrice); ?>
      <?php drawAdvertiserInfo($username, $profilePhotoId); ?>
    </div>
  </div>

  <?php drawMessagePopup($username); ?>
</div>

<script>
  const currentUserId = <?= json_encode($loggedInUser) ?>;
  const receiverId = <?= json_encode($service['freelancer_id']) ?>;
  const serviceId = <?= json_encode($service['id']) ?>;
</script>

<script src="/Scripts/image-slide.js"></script>
<script src="/Scripts/order.js"></script>
<script src="/Scripts/popupMessages.js"></script>

<?php drawFooter(); ?>
