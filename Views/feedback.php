<?php
session_start();
require_once(dirname(__FILE__) . '/../Utils/session.php');
require_once(dirname(__FILE__) . '/../Controllers/feedbackController.php');
require_once(dirname(__FILE__) . '/../Controllers/serviceController.php');
require_once(dirname(__FILE__) . '/../Templates/common_elems.php');

// Verificar se o utilizador está logado
if (!isUserLoggedIn()) {
    $_SESSION['error'] = 'Deve fazer login para deixar feedback.';
    header("Location: auth.php");
    exit();
}

// Obter service_id do parâmetro
$serviceId = isset($_GET['service_id']) ? intval($_GET['service_id']) : 0;

if ($serviceId <= 0) {
    $_SESSION['error'] = 'Serviço não encontrado.';
    header("Location: mainPage.php");
    exit();
}

// Obter dados do serviço
$service = getServiceById($serviceId);
if (!$service) {
    $_SESSION['error'] = 'Serviço não encontrado.';
    header("Location: mainPage.php");
    exit();
}

$userId = getCurrentUserId();

// Verificar se o utilizador já deixou feedback para este serviço
if (hasUserFeedback($userId, $serviceId)) {
    $_SESSION['error'] = 'Já deixou feedback para este serviço.';
    header("Location: product.php?id=" . $serviceId);
    exit();
}

// Verificar se o utilizador contratou este serviço
if (!hasUserContractedService($userId, $serviceId)) {
    $_SESSION['error'] = 'Só pode deixar feedback para serviços que contratou.';
    header("Location: product.php?id=" . $serviceId);
    exit();
}

// Desenhar o cabeçalho
drawHeader("Deixar Feedback - FreelanceConnect", ["/Styles/feedback.css"]);
?>

<div class="feedback-container">
    <div class="feedback-card">
        <div class="feedback-header">
            <h1>Deixar Feedback</h1>
            <div class="service-info">
                <h3><?= htmlspecialchars($service['name']) ?></h3>
                <p class="service-description"><?= htmlspecialchars($service['description']) ?></p>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success'] ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= $_SESSION['error'] ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="../Controllers/feedbackController.php" method="POST" class="feedback-form">
            <input type="hidden" name="action" value="create_feedback">
            <input type="hidden" name="service_id" value="<?= $serviceId ?>">

            <div class="form-group">
                <label for="title">Título do Feedback</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    class="form-input" 
                    maxlength="255"
                    placeholder="Resumo da sua experiência..."
                    value="<?= isset($_SESSION['feedback_form_data']['title']) ? htmlspecialchars($_SESSION['feedback_form_data']['title']) : '' ?>"
                    required
                >
                <small>Máximo 255 caracteres</small>
            </div>

            <div class="form-group">
                <label for="evaluation">Avaliação</label>
                <div class="rating-container">
                    <div class="stars-display" id="starsDisplay">
                        <span class="star" data-rating="0.5">★</span>
                        <span class="star" data-rating="1">★</span>
                        <span class="star" data-rating="1.5">★</span>
                        <span class="star" data-rating="2">★</span>
                        <span class="star" data-rating="2.5">★</span>
                        <span class="star" data-rating="3">★</span>
                        <span class="star" data-rating="3.5">★</span>
                        <span class="star" data-rating="4">★</span>
                        <span class="star" data-rating="4.5">★</span>
                        <span class="star" data-rating="5">★</span>
                    </div>
                    <span class="rating-text" id="ratingText">Clique para avaliar</span>
                    <input type="hidden" name="evaluation" id="evaluation" required>
                </div>
                <small>Avalie de 0.5 a 5 estrelas</small>
            </div>

            <div class="form-group">
                <label for="description">Descrição (Opcional)</label>
                <textarea 
                    id="description" 
                    name="description" 
                    class="form-input" 
                    rows="6"
                    maxlength="1000"
                    placeholder="Conte mais sobre sua experiência com este serviço..."
                ><?= isset($_SESSION['feedback_form_data']['description']) ? htmlspecialchars($_SESSION['feedback_form_data']['description']) : '' ?></textarea>
                <small>Máximo 1000 caracteres</small>
            </div>

            <div class="form-actions">
                <a href="../Views/product.php?id=<?= $serviceId ?>" class="cancel-button">
                    Cancelar
                </a>
                <button type="submit" class="submit-button" id="submitButton">
                    Enviar Feedback
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Sistema de avaliação por estrelas
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.star');
        const ratingText = document.getElementById('ratingText');
        const evaluationInput = document.getElementById('evaluation');
        let currentRating = 0;

        // Textos para cada avaliação
        const ratingTexts = {
            0.5: 'Muito Ruim',
            1: 'Ruim',
            1.5: 'Ruim',
            2: 'Regular',
            2.5: 'Regular',
            3: 'Bom',
            3.5: 'Bom',
            4: 'Muito Bom',
            4.5: 'Muito Bom',
            5: 'Excelente'
        };

        function updateStars(rating) {
            stars.forEach((star, index) => {
                const starValue = parseFloat(star.dataset.rating);
                if (starValue <= rating) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });
        }

        function updateRatingText(rating) {
            if (rating > 0) {
                ratingText.textContent = `${rating} - ${ratingTexts[rating]}`;
            } else {
                ratingText.textContent = 'Clique para avaliar';
            }
        }

        // Event listeners para as estrelas
        stars.forEach(star => {
            star.addEventListener('click', function() {
                currentRating = parseFloat(this.dataset.rating);
                evaluationInput.value = currentRating;
                updateStars(currentRating);
                updateRatingText(currentRating);
            });

            star.addEventListener('mouseenter', function() {
                const hoverRating = parseFloat(this.dataset.rating);
                updateStars(hoverRating);
                updateRatingText(hoverRating);
            });
        });

        // Restaurar estado ao sair do hover
        document.querySelector('.stars-display').addEventListener('mouseleave', function() {
            updateStars(currentRating);
            updateRatingText(currentRating);
        });

        // Validação do formulário
        const form = document.querySelector('.feedback-form');
        const submitButton = document.getElementById('submitButton');

        form.addEventListener('submit', function(e) {
            if (currentRating === 0) {
                e.preventDefault();
                alert('Por favor, selecione uma avaliação.');
                return;
            }

            // Desabilitar botão durante envio
            submitButton.disabled = true;
            submitButton.textContent = 'Enviando...';
        });
    });
</script>

<?php
// Limpar dados do formulário da sessão após exibir
if (isset($_SESSION['feedback_form_data'])) {
    unset($_SESSION['feedback_form_data']);
}

// Desenhar o rodapé
drawFooter();
?>
