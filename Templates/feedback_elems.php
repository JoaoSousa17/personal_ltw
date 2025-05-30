<?php

/**
 * Desenha um feedback individual
 *
 * @param Feedback $feedback Objeto Feedback
 */
function drawFeedback($feedback) {
    $userName = htmlspecialchars($feedback->getUserName() ?? 'Utilizador');
    $title = htmlspecialchars($feedback->getTitle());
    $description = htmlspecialchars($feedback->getDescription() ?? '');
    $evaluation = floatval($feedback->getEvaluation());
    $date = $feedback->getDate();
    $time = $feedback->getTime();
    
    // Formatação da data
    $formattedDate = date("d/m/Y", strtotime($date));
    
    // Geração das estrelas
    $starsHtml = generateStarsHtml($evaluation);
    ?>
    
    <div class="feedback-item">
        <div class="feedback-header">
            <div class="feedback-user-info">
                <div class="feedback-avatar">
                    <img src="/Images/site/header/genericProfile.png" alt="Avatar" class="avatar-img">
                </div>
                <div class="feedback-user-details">
                    <h4 class="feedback-user-name"><?= $userName ?></h4>
                    <div class="feedback-rating">
                        <?= $starsHtml ?>
                        <span class="rating-number"><?= number_format($evaluation, 1) ?></span>
                    </div>
                </div>
            </div>
            <div class="feedback-date">
                <time datetime="<?= $date ?>"><?= $formattedDate ?></time>
            </div>
        </div>
        
        <div class="feedback-content">
            <h5 class="feedback-title"><?= $title ?></h5>
            <?php if (!empty($description)): ?>
                <p class="feedback-description"><?= $description ?></p>
            <?php endif; ?>
        </div>
    </div>
    
    <?php
}

/**
 * Desenha a secção completa de feedbacks
 *
 * @param array $feedbacks Array de feedbacks
 * @param array $stats Estatísticas dos feedbacks
 * @param int $serviceId ID do serviço
 */
function drawFeedbackSection($feedbacks, $stats, $serviceId) {
    $averageRating = $stats['average'];
    $totalCount = $stats['count'];
    ?>
    
    <section class="feedback-section">
        <div class="feedback-section-header">
            <h3>Avaliações e Comentários</h3>
            
            <?php if ($totalCount > 0): ?>
                <div class="feedback-summary">
                    <div class="overall-rating">
                        <div class="rating-display">
                            <span class="rating-large"><?= number_format($averageRating, 1) ?></span>
                            <?= generateStarsHtml($averageRating) ?>
                        </div>
                        <p class="rating-count">Baseado em <?= $totalCount ?> avaliação<?= $totalCount != 1 ? 'ões' : '' ?></p>
                    </div>
                </div>
            <?php else: ?>
                <p class="no-reviews-text">Este serviço ainda não tem avaliações.</p>
            <?php endif; ?>
        </div>
        
        <?php if ($totalCount > 0): ?>
            <div class="feedback-list">
                <?php foreach ($feedbacks as $feedback): ?>
                    <?php drawFeedback($feedback); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="feedback-actions">
            <a href="/Views/feedback.php?service_id=<?= $serviceId ?>" class="leave-feedback-btn">
                Deixar Avaliação
            </a>
        </div>
    </section>
    
    <?php
}

/**
 * Gera HTML das estrelas baseado na avaliação
 *
 * @param float $rating Avaliação de 0 a 5
 * @return string HTML das estrelas
 */
function generateStarsHtml($rating) {
    $fullStars = floor($rating);
    $hasHalfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
    
    $html = '<div class="stars-container">';
    
    // Estrelas cheias
    for ($i = 0; $i < $fullStars; $i++) {
        $html .= '<span class="star filled">★</span>';
    }
    
    // Meia estrela
    if ($hasHalfStar) {
        $html .= '<span class="star half">★</span>';
    }
    
    // Estrelas vazias
    for ($i = 0; $i < $emptyStars; $i++) {
        $html .= '<span class="star empty">☆</span>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Desenha um feedback expandido (para modal ou página detalhada)
 *
 * @param Feedback $feedback Objeto Feedback
 */
function drawExpandedFeedback($feedback) {
    $userName = htmlspecialchars($feedback->getUserName() ?? 'Utilizador');
    $title = htmlspecialchars($feedback->getTitle());
    $description = htmlspecialchars($feedback->getDescription() ?? '');
    $evaluation = floatval($feedback->getEvaluation());
    $date = $feedback->getDate();
    $time = $feedback->getTime();
    
    // Formatação da data e hora
    $formattedDateTime = date("d/m/Y \à\s H:i", strtotime($date . ' ' . $time));
    
    // Geração das estrelas
    $starsHtml = generateStarsHtml($evaluation);
    ?>
    
    <div class="feedback-expanded">
        <div class="feedback-expanded-header">
            <div class="feedback-user-section">
                <div class="feedback-avatar-large">
                    <img src="/Images/site/header/genericProfile.png" alt="Avatar" class="avatar-img-large">
                </div>
                <div class="feedback-user-info-expanded">
                    <h3 class="feedback-user-name-large"><?= $userName ?></h3>
                    <div class="feedback-rating-large">
                        <?= $starsHtml ?>
                        <span class="rating-number-large"><?= number_format($evaluation, 1) ?> / 5</span>
                    </div>
                    <p class="feedback-date-expanded"><?= $formattedDateTime ?></p>
                </div>
            </div>
        </div>
        
        <div class="feedback-content-expanded">
            <h4 class="feedback-title-expanded"><?= $title ?></h4>
            <?php if (!empty($description)): ?>
                <div class="feedback-description-expanded">
                    <p><?= nl2br($description) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php
}

/**
 * Desenha estatísticas detalhadas de feedback
 *
 * @param array $stats Estatísticas dos feedbacks
 */
function drawFeedbackStats($stats) {
    $distribution = $stats['distribution'] ?? [];
    $totalCount = $stats['total_count'] ?? 0;
    
    if ($totalCount == 0) {
        return;
    }
    ?>
    
    <div class="feedback-stats">
        <h4>Distribuição de Avaliações</h4>
        <div class="rating-distribution">
            <?php for ($star = 5; $star >= 1; $star--): ?>
                <?php
                $count = 0;
                foreach ($distribution as $dist) {
                    if (floor($dist['evaluation']) == $star) {
                        $count += $dist['count'];
                    }
                }
                $percentage = $totalCount > 0 ? ($count / $totalCount) * 100 : 0;
                ?>
                <div class="rating-bar">
                    <span class="star-label"><?= $star ?> ★</span>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= $percentage ?>%"></div>
                    </div>
                    <span class="count-label"><?= $count ?></span>
                </div>
            <?php endfor; ?>
        </div>
    </div>
    
    <?php
}
?>
