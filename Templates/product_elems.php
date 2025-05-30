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
            src="<?= '/' . ltrim(htmlspecialchars($url), '/') ?>"
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
 * @param float $finalPrice Preço final por hora (já com desconto aplicado, em EUR)
 * @param int $serviceId
 * @param float|null $originalPrice Preço original por hora (sem desconto, em EUR) 
 * @param int $discount Percentagem de desconto
 * @param int $duration Duração em minutos (da base de dados)
 */
function drawProductInfo(string $date, string $title, float $finalPrice, int $serviceId, ?float $originalPrice = null, int $discount = 0, int $duration = 0) { 
    // CORRIGIDO: A duração vem em minutos da BD, converter para horas
    $durationHours = $duration / 60.0;
    
    // Calcular preço total baseado na duração em horas
    $totalPrice = $finalPrice * $durationHours;
    
    // Log para debug
    error_log("Duration (minutes): " . $duration);
    error_log("Duration (hours): " . $durationHours);
    error_log("Price per hour: " . $finalPrice);
    error_log("Total price: " . $totalPrice);
    
    // Obter informações da moeda do utilizador para conversão
    require_once(dirname(__FILE__)."/../Controllers/distancesCalculationController.php");
    $currencyInfo = getUserCurrencyInfo();
    
    // Converter preços para a moeda do utilizador
    $finalPriceConverted = convertCurrency($finalPrice, $currencyInfo['code']);
    $totalPriceConverted = convertCurrency($totalPrice, $currencyInfo['code']);
    
    $originalPriceConverted = null;
    if ($originalPrice !== null) {
        $originalPriceConverted = convertCurrency($originalPrice, $currencyInfo['code']);
    }
    
    ?>
  <div class="product-info">
    <div>
      <h6>Publicado a <?= htmlspecialchars($date) ?></h6>
      <h2><?= htmlspecialchars($title) ?></h2>

      <?php if ($originalPrice !== null && $discount > 0): ?>
        <div class="discount-badge"><?= $discount ?>% OFF</div>
      <?php endif; ?>

      <div class="price-info">
        <?php if ($originalPriceConverted !== null && $discount > 0): ?>
          <p class="original-price">
            <s><?= $currencyInfo['symbol'] ?><?= number_format($originalPriceConverted, 2, ',', '') ?></s>
          </p>
        <?php endif; ?>
        <h3 class="price-hour">
          <?= $currencyInfo['symbol'] ?><?= number_format($finalPriceConverted, 2, ',', '') ?>/h
        </h3>
        <p class="duration">
          <?= $duration ?> min (<?= number_format($durationHours, 1) ?> hrs)
        </p>
        <p class="total-price">
          <strong>Total: <?= $currencyInfo['symbol'] ?><?= number_format($totalPriceConverted, 2, ',', '') ?></strong>
        </p>
      </div>

      <button onclick="openPopup()">Enviar mensagem</button>
      
      <!-- CORRIGIDO: Passar o preço total em EUR (base) calculado corretamente -->
      <button 
        id="orderBtn" 
        data-id="<?= htmlspecialchars($serviceId) ?>" 
        data-price="<?= number_format($totalPrice, 2, '.', '') ?>"
        data-currency="eur"
        data-debug-duration="<?= $duration ?>"
        data-debug-hours="<?= $durationHours ?>"
        data-debug-price-hour="<?= $finalPrice ?>"
      >
        Encomendar (<?= $currencyInfo['symbol'] ?><?= number_format($totalPriceConverted, 2, ',', '') ?>)
      </button>
    </div>
  </div>
<?php }


/**
 * Mostra a informação do anunciante.
 * @param string $username
 * @param int|null $profilePhotoId
 */
function drawAdvertiserInfo(string $username, ?int $profilePhotoId) {
    // Função para obter URL da foto de perfil
    
    $profilePhotoUrl = getProfilePhotoUrl($profilePhotoId);
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
 * Função para desenhar o Popup
 * @param string $receiverName
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
