<?php

/**
 * Gera o carrossel de imagens do produto.
 * @param array $imageUrls URLs das imagens do produto.
 */
function drawProductImageCarousel(array $imageUrls) { ?>
  <div class="product-image">
    <div class="image-box">
      <button class="image-btn left-btn" onclick="changeImage('left')" style="display: none;"><</button>

      <div class="image-container">
        <?php foreach ($imageUrls as $index => $url): ?>
          <img
            src="<?= htmlspecialchars($url) ?>"
            alt="Product"
            class="product-image-img"
            style="<?= $index > 0 ? 'display: none;' : '' ?>"
          />
        <?php endforeach; ?>
      </div>

      <button class="image-btn right-btn" onclick="changeImage('right')">></button>
    </div>
  </div>
<?php }


/**
 * Mostra a descrição do produto e as categorias.
 * @param string $description Texto descritivo.
 * @param array $categories Lista de categorias associadas.
 */
function drawProductDescription(string $description, array $categories) { ?>
  <div class="product-description">
    <div class="categories">
      <?php foreach ($categories as $cat): ?>
        <div><?= htmlspecialchars($cat) ?></div>
      <?php endforeach; ?>
    </div>
    <h2>Descrição</h2>
    <p><?= htmlspecialchars($description) ?></p>
    <div class="Report">
      <img src="https://t4.ftcdn.net/jpg/04/81/50/49/360_F_481504964_Qds62jOjvjfAWbhEAVKg9bItIGntAC6P.jpg" alt="Flag" class="flag-image">
    </div>
  </div>
<?php }


/**
 * Mostra as informações principais do produto (data, título, preço).
 * @param string $date
 * @param string $title
 * @param float $price
 */
function drawProductInfo(string $date, string $title, float $price) { ?>
  <div class="product-info">
    <div>
      <h6>Publicado a <?= htmlspecialchars($date) ?></h6>
      <h2><?= htmlspecialchars($title) ?></h2>
      <h3>Preço: <?= number_format($price, 2, ',', '') ?>€</h3>
      <button onclick="openPopup()">Enviar mensagem</button>
      <button id="orderBtn">Encomendar</button>
    </div>
  </div>
<?php }


/**
 * Mostra a informação do anunciante.
 * @param string $username
 * @param int|null $profilePhotoId
 */
function drawAdvertiserInfo(string $username, ?int $profilePhotoId) {
    $profilePhotoUrl = $profilePhotoId ? getProfilePhotoUrl($profilePhotoId) : null;
    ?>
    <div class="product-advertiser">
        <p>Utilizador</p>

        <?php if ($profilePhotoUrl): ?>
            <img src="<?= htmlspecialchars($profilePhotoUrl) ?>" class="user-icon">
        <?php else: ?>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="user-icon">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
            </svg>
        <?php endif; ?>

        <?= htmlspecialchars($username) ?>
    </div>
<?php }


/**
 * Função para obter a URL da imagem de perfil com base no ID.
 * @param int $profilePhotoId
 * @return string|null
 */
function getProfilePhotoUrl(int $profilePhotoId): ?string {
    try {
        $db = getDatabaseConnection();
        $query = "SELECT path_ FROM Media WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$profilePhotoId]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result && !empty($result['path_']) ? $result['path_'] : null;
    } catch (PDOException $e) {
        return null;
    }
}

/**
 * Função para desenhar o Popup
 * @param int $receiverName
 */
function drawMessagePopup(string $receiverName) { ?>
  <div id="messagePopup" class="message-popup hidden">
    
    <div class="popup-header">
      <span onclick="togglePopup()" style="cursor: pointer;"><?= htmlspecialchars($receiverName) ?></span>
      <div class="popup-buttons">
        <button id="minimizeBtn" onclick="event.stopPropagation(); minimizePopup(event)">–</button>
        <button class="popup-close-btn" onclick="event.stopPropagation(); closePopup()">✕</button>
      </div>
    </div>
    
    <div class="popup-body">
      <div id="messageList" class="message-list"></div>
    </div>

    <div class="message-input">
      <textarea id="messageText" placeholder="Escreva a sua mensagem..."></textarea>
      <button onclick="sendMessage()">Enviar</button>
    </div>

  </div>
<?php }