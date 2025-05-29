<?php

function drawConversationList(array $conversations) {
    foreach ($conversations as $conv) {
        $photoUrl = getProfilePhotoUrl($conv['profile_photo']);
        ?>
        <div class="conversation-item" data-user-id="<?= $conv['other_user_id'] ?>">
            <?php if ($photoUrl): ?>
                <img src="<?= htmlspecialchars($photoUrl) ?>" class="user-icon">
            <?php else: ?>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="user-icon">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
            <?php endif; ?>
            <span><?= htmlspecialchars($conv['username']) ?></span>
        </div>
        <?php
    }
}

function drawAllChatWindows(array $conversations, int $loggedInUserId) {
    foreach ($conversations as $conv) {
        $otherId = $conv['other_user_id'];
        $messages = getConversationMessages($loggedInUserId, $otherId);
        ?>
        <div class="chat-window" id="chat-with-<?= $otherId ?>" style="display:none">
            <div class="chat-header">
                <h2><?= htmlspecialchars($conv['username']) ?></h2>
                <button class="request-button" data-freelancer-id="<?= $otherId ?>">Pedido</button>
            </div>

            <div class="chat-messages">
                <?php foreach ($messages as $msg): 
                    $class = $msg['sender_id'] == $loggedInUserId ? 'message sent' : 'message received';
                ?>
                    <div class="<?= $class ?>">
                        <?= htmlspecialchars($msg['content']) ?>
                        <span class="message-time"><?= htmlspecialchars(substr($msg['time_'], 0, 5)) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="request-modal" style="display:none">
                <div class="request-content">
                    <h3>Detalhes do Pedido</h3>
                    <div class="service-row">
                        <label for="serviceSelect"><strong>Serviço:</strong></label>
                        <select class="serviceSelect" id="serviceSelect"></select>
                    </div>

                    <p><strong>Preço por hora:</strong> €<span class="hourlyRate">--</span></p>

                    <label for="newPrice">Propor novo preço (€):</label>
                    <input type="number" class="newPrice" placeholder="Ex: 30.00" min="0" step="0.01">

                    <div class="request-actions">
                        <button class="sendRequest">Enviar</button>
                        <button class="closeRequest">Cancelar</button>
                    </div>
                </div>
            </div>

            
            <div class="chat-input">
                <form class="message-form" data-receiver-id="<?= $otherId ?>">
                    <input type="text" name="message" placeholder="Escreva uma mensagem..." autocomplete="off" required>
                    <button type="submit">Enviar</button>
                </form>
            </div>
        </div>
        <?php
    }
}


function getConversationMessages(int $userId, int $otherUserId): array {
    $db = getDatabaseConnection();

    // Marca mensagens não lidas como lidas
    $markStmt = $db->prepare("
        UPDATE Message_
        SET is_read = 1
        WHERE receiver_id = :userId
          AND sender_id = :otherUserId
          AND is_read = 0
    ");
    $markStmt->execute([
        'userId' => $userId,
        'otherUserId' => $otherUserId,
    ]);

    $stmt = $db->prepare("
        SELECT sender_id, body_ AS content, date_, time_
        FROM Message_
        WHERE (sender_id = :user1 AND receiver_id = :user2)
           OR (sender_id = :user2 AND receiver_id = :user1)
        ORDER BY date_, time_
    ");
    $stmt->execute([
        'user1' => $userId,
        'user2' => $otherUserId
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



function getLastMessageTimestamp(int $userId, int $otherUserId): ?string {
    $db = getDatabaseConnection();
    $stmt = $db->prepare("
        SELECT date_ || ' ' || time_ AS timestamp
        FROM Message_
        WHERE (sender_id = :user1 AND receiver_id = :user2)
           OR (sender_id = :user2 AND receiver_id = :user1)
        ORDER BY date_ DESC, time_ DESC
        LIMIT 1
    ");
    $stmt->execute([
        'user1' => $userId,
        'user2' => $otherUserId
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['timestamp'] : null;
}
