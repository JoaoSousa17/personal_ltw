<?php
require_once(dirname(__FILE__) . '/../Database/connection.php');

/**
 * Classe responsável por representar e gerir as razões de bloqueio de utilizadores.
 * Cada razão está associada a um utilizador e pode incluir informação adicional.
 */
class ReasonBlock {
    private $id;
    private $userId;
    private $reason;
    private $extraInfo;
    private $db;

    /**
     * Construtor da classe.
     * 
     * @param PDO $db Instância ativa da ligação à base de dados.
     */
    public function __construct($db) {
        $this->db = $db;
    }

    /********
     Getters
    ********/

    /**
     * Getter para o ID do motivo de bloqueio.
     * 
     * @return int ID do motivo de bloqueio.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Getter para o ID do utilizador associado.
     * 
     * @return int ID do utilizador.
     */
    public function getUserId() {
        return $this->userId;
    }

    /**
     * Getter para a razão do bloqueio.
     * 
     * @return string Razão principal do bloqueio.
     */
    public function getReason() {
        return $this->reason;
    }

    /**
     * Getter para informação adicional fornecida.
     * 
     * @return string|null Informação complementar ao motivo.
     */
    public function getExtraInfo() {
        return $this->extraInfo;
    }

    /********
     Setters
    ********/

    /**
     * Setter para o ID do motivo de bloqueio.
     * 
     * @param int $id Novo ID.
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Setter para o ID do utilizador.
     * 
     * @param int $userId ID do utilizador associado.
     */
    public function setUserId($userId) {
        $this->userId = $userId;
    }

    /**
     * Setter para a razão do bloqueio.
     * 
     * @param string $reason Texto da razão.
     */
    public function setReason($reason) {
        $this->reason = $reason;
    }

    /**
     * Setter para a informação adicional.
     * 
     * @param string $extraInfo Texto adicional, contemplando detalhes do bloqueio.
     */
    public function setExtraInfo($extraInfo) {
        $this->extraInfo = $extraInfo;
    }

    /**********
     Functions
    **********/

    /**
     * Guarda ou atualiza a razão de bloqueio na base de dados.
     *
     * @return bool Verdadeiro se a operação for bem-sucedida.
     */
    public function save() {
        // Operação falhará caso o id do User ou a razão não estiverem definidos (campos obrigatórios).
        if (!$this->userId || !$this->reason) {
            return false;
        }

        try {
            // Caso id esteja definido (Atualização de dados do motivo de bloqueio).
            if ($this->id) {
                // Preparação do SQL Statement e posterior execução.
                $stmt = $this->db->prepare("UPDATE Reason_Block SET user_id = :user_id, reason = :reason, extra_info = :extra_info WHERE id = :id");
                return $stmt->execute([
                    ':user_id' => $this->userId,
                    ':reason' => $this->reason,
                    ':extra_info' => $this->extraInfo,
                    ':id' => $this->id
                ]);
            } 
            
            // Caso id não esteja definido (criação de Nova razão de Bloqueio).
            else {
                // Preparação do SQL Statement e posterior execução.
                $stmt = $this->db->prepare("INSERT INTO Reason_Block (user_id, reason, extra_info) VALUES (:user_id, :reason, :extra_info)");
                $result = $stmt->execute([
                    ':user_id' => $this->userId,
                    ':reason' => $this->reason,
                    ':extra_info' => $this->extraInfo
                ]);

                // Atualiza a instância do Model, em caso de sucesso da operação anterior.
                if ($result) {
                    $this->id = $this->db->lastInsertId();
                    return true;
                }
                return false;
            }
        } 
        
        // Gestão de falhas na operação.
        catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Elimina a razão de bloqueio da base de dados.
     *
     * @return bool Resultado da operação.
     */
    public function delete() {
        // Verifica se o id está definido na instância do Model
        if (!$this->id) {
            return false;
        }

        // Prepara e Executa o SQl Statement para a eliminação
        try {
            $stmt = $this->db->prepare("DELETE FROM Reason_Block WHERE id = :id");
            return $stmt->execute([':id' => $this->id]);
        } 
        
        // Gestão de falhas na operação.
        catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Procura uma razão de bloqueio através do ID do utilizador.
     * 
     * @param PDO $db Instância da base de dados.
     * @param int $userId ID do utilizador.
     * @return ReasonBlock|null Instância encontrada ou null.
     */
    public static function findByUserId($db, $userId) {
        try {
            // Preparação e Execução do SQL Statement.
            $stmt = $db->prepare("SELECT * FROM Reason_Block WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Caso o resultado não seja null, cria a instância de ReasonBlock e retorna-a.
            return $result ? self::createFromArray($db, $result) : null;
        } 
        
        // Gestão de falhas na operação.
        catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Procura uma entrada de bloqueio através do seu ID.
     * 
     * @param PDO $db Instância da base de dados.
     * @param int $id ID da entrada.
     * @return ReasonBlock|null Instância encontrada ou null.
     */
    public static function findById($db, $id) {
        try {
            // Preparação e Execução do SQL Statement.
            $stmt = $db->prepare("SELECT * FROM Reason_Block WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Caso o resultado não seja null, cria a instância de ReasonBlock e retorna-a.
            return $result ? self::createFromArray($db, $result) : null;
        } 
        
        // Gestão de falhas na operação.
        catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Obtém todas as entradas de bloqueio.
     * 
     * @param PDO $db Instância da base de dados.
     * @return array Lista de ReasonBlock.
     */
    public static function getAllReasonBlocks($db) {
        try {
            // Preparação e Execução do SQL Statement.
            $stmt = $db->prepare("SELECT * FROM Reason_Block");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Para cada linha retornada, cria a instância de ReasonBlock adiciona ao array $reasonBlocks.
            $reasonBlocks = [];
            foreach ($results as $result) {
                $reasonBlocks[] = self::createFromArray($db, $result);
            }
            return $reasonBlocks;
        } 
        
        // Gestão de falhas na operação.
        catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Cria uma instância de ReasonBlock a partir de um array de dados.
     * 
     * @param PDO $db Instância da base de dados.
     * @param array $array Linha de dados obtida do PDO.
     * @return ReasonBlock Instância da classe preenchida.
     */
    private static function createFromArray($db, $array) {
        $reasonBlock = new self($db);
        $reasonBlock->setId($array['id']);
        $reasonBlock->setUserId($array['user_id']);
        $reasonBlock->setReason($array['reason']);
        $reasonBlock->setExtraInfo($array['extra_info']);
        return $reasonBlock;
    }
}
?>
