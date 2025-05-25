<?php
require_once(dirname(__FILE__) . '/../Models/ReasonBlock.php');
require_once(dirname(__FILE__) . '/../Database/connection.php');

/**
 * Adiciona ou atualiza uma razão de bloqueio para um determinado utilizador.
 *
 * @param int $userId ID do utilizador a ser bloqueado.
 * @param string $reason Razão do bloqueio.
 * @param string $extraInfo Informação adicional opcional.
 * @return bool True se guardado com sucesso, False caso contrário.
 */
function addBlockReason($userId, $reason, $extraInfo = '') {
    $db = getDatabaseConnection();

    // Verifica se já existe uma razão para este utilizador
    $existingReason = ReasonBlock::findByUserId($db, $userId);

    if ($existingReason) {
        // Atualiza a razão existente
        $existingReason->setReason($reason);
        $existingReason->setExtraInfo($extraInfo);
        return $existingReason->save();
    } else {
        // Cria nova entrada
        $reasonBlock = new ReasonBlock($db);
        $reasonBlock->setUserId($userId);
        $reasonBlock->setReason($reason);
        $reasonBlock->setExtraInfo($extraInfo);
        return $reasonBlock->save();
    }
}

/**
 * Remove uma entrada de razão de bloqueio com base no ID do utilizador.
 *
 * @param int $userId ID do utilizador a remover.
 * @return bool True se removido com sucesso, ou se não existir nenhuma entrada.
 */
function removeBlockReason($userId) {
    $db = getDatabaseConnection();
    $reasonBlock = ReasonBlock::findByUserId($db, $userId);

    if ($reasonBlock) {
        return $reasonBlock->delete();
    }

    return true; // Considera sucesso se não existir entrada
}

/**
 * Obtém a razão de bloqueio associada a um utilizador.
 *
 * @param int $userId ID do utilizador.
 * @return ReasonBlock|null Objeto ReasonBlock ou null se não encontrado.
 */
function getBlockReason($userId) {
    $db = getDatabaseConnection();
    return ReasonBlock::findByUserId($db, $userId);
}

/**
 * Obtém todas as razões de bloqueio existentes no sistema.
 *
 * @return array Lista de objetos ReasonBlock.
 */
function getAllBlockReasons() {
    $db = getDatabaseConnection();
    return ReasonBlock::getAllReasonBlocks($db);
}
?>
