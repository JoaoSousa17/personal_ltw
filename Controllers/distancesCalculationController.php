<?php
require_once(dirname(__FILE__) . '/../Database/connection.php');

/**
 * Calcula a distância entre dois locais baseado nos municípios e distritos
 * 
 * @param string $municipality1 Município de origem
 * @param string $municipality2 Município de destino
 * @param string $district1 Distrito de origem
 * @param string $district2 Distrito de destino
 * @return int Distância em quilómetros (retorna 55 em caso de erro)
 */
function calculateDistance($municipality1, $municipality2, $district1, $district2) {
    try {
        $db = getDatabaseConnection();
        
        // Verificar se ambos os distritos são Porto
        if (strtolower(trim($district1)) === 'porto' && strtolower(trim($district2)) === 'porto') {
            // Consultar tabela distances_Porto usando os municípios
            $stmt = $db->prepare("
                SELECT distance 
                FROM distances_Porto 
                WHERE origin = :origin AND destiny = :destiny
            ");
            
            $stmt->bindParam(':origin', $municipality1, PDO::PARAM_STR);
            $stmt->bindParam(':destiny', $municipality2, PDO::PARAM_STR);
        } else {
            // Consultar tabela distances_Districts usando os distritos
            $stmt = $db->prepare("
                SELECT distance 
                FROM distances_Districts 
                WHERE origin = :origin AND destiny = :destiny
            ");
            
            $stmt->bindParam(':origin', $district1, PDO::PARAM_STR);
            $stmt->bindParam(':destiny', $district2, PDO::PARAM_STR);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Se encontrou resultado, retorna a distância
        if ($result && isset($result['distance'])) {
            return (int) $result['distance'];
        }
        
        // Se não encontrou resultado, retorna valor padrão
        return 55;
        
    } catch (PDOException $e) {
        // Em caso de erro de base de dados, retorna valor padrão
        error_log("Erro ao calcular distância: " . $e->getMessage());
        return 55;
    } catch (Exception $e) {
        // Em caso de qualquer outro erro, retorna valor padrão
        error_log("Erro geral ao calcular distância: " . $e->getMessage());
        return 55;
    }
}
