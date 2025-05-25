<?php
require_once(dirname(__FILE__) . '/../Database/connection.php');

/**
 * Classe responsável por representar e gerir pedidos de desbloqueio por parte de utilizadores.
 * Permite criar, atualizar, aprovar, rejeitar e eliminar pedidos, bem como consultar o estado dos mesmos.
 */
class UnblockAppeal {
    private $id;
    private $userId;
    private $title;
    private $body;
    private $date;
    private $time;
    private $status;
    private $db;

    /**
     * Construtor da classe.
     * 
     * @param PDO $db Instância da ligação à base de dados.
     */
    public function __construct($db) {
        $this->db = $db;
    }

    /********
     Getters
    ********/

    /** 
     * Getter para o ID do pedido de desbloqueio.
     * 
     * @return int ID do pedido de desbloqueio.
     */
    public function getId() {
        return $this->id;
    }

    /** 
     * Getter para o ID do utilizador que submeteu o pedido de desbloqueio.
     * 
     * @return int ID do utilizador.
     */
    public function getUserId() {
        return $this->userId;
    }

    /** 
     * Getter para o título do pedido de desbloqueio.
     * 
     * @return string Título do pedido de desbloqueio.
     */
    public function getTitle() {
        return $this->title;
    }

    /** 
     * Getter para o corpo (texto) do pedido de desbloqueio.
     * 
     * @return string Corpo (texto) do pedido.
     */
    public function getBody() {
        return $this->body;
    }

    /** 
     * Getter para a data de submissão do pedido de desbloqueio.
     * 
     * @return string Data (YYYY-MM-DD).
     */
    public function getDate() {
        return $this->date;
    }

    /** 
     * Getter para a hora de submissão do pedido de desbloqueio.
     * 
     * @return string Hora (HH:MM:SS).
     */
    public function getTime() {
        return $this->time;
    }

    /** 
     * Getter para o estado atual do pedido de desbloqueio.
     * 
     * @return string Estado ('pending', 'approved', 'rejected').
     */
    public function getStatus() {
        return $this->status;
    }

    /********
     Setters
    ********/

    /** 
     * Setter para o ID do pedido de desbloqueio.
     * 
     * @param int $id Novo ID do pedido de desbloqueio.
     */
    public function setId($id) {
        $this->id = $id;
    }

    /** 
     * Setter para o ID do utilizador associado ao pedido de desbloqueio.
     * 
     * @param int $userId Novo ID do utilizador.
     */
    public function setUserId($userId) {
        $this->userId = $userId;
    }

    /** 
     * Setter para o título do pedido de desbloqueio.
     * 
     * @param string $title Novo título.
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /** 
     * Setter para o corpo (texto) do pedido de desbloqueio.
     * 
     * @param string $body Nova mensagem do pedido.
     */
    public function setBody($body) {
        $this->body = $body;
    }

    /** 
     * Setter para a data do pedido de desbloqueio.
     * 
     * @param string $date Nova data no formato YYYY-MM-DD.
     */
    public function setDate($date) {
        $this->date = $date;
    }

    /** 
     * Setter para a hora do pedido de desbloqueio.
     * 
     * @param string $time Nova hora no formato HH:MM:SS.
     */
    public function setTime($time) {
        $this->time = $time;
    }

    /** 
     * Setter para o estado do pedido de desbloqueio.
     * 
     * @param string $status Novo estado ('pending', 'approved', 'rejected').
     */
    public function setStatus($status) {
        $this->status = $status;
    }

    /**********
     Functions
    **********/

    /**
     * Guarda ou atualiza o pedido de desbloqueio na base de dados.
     *
     * @return bool Verdadeiro se a operação for bem-sucedida.
     */
    public function save() {
        // Operação falhará caso o id do User ou o título ou corpo de texto não estiverem definidos (campos obrigatórios).
        if (!$this->userId || !$this->title || !$this->body) return false;

        try {
            // Caso id esteja definido (Atualização de dados do pedido de desbloqueio).
            if ($this->id) {
                // Preparação do SQL Statement e posterior execução.
                $stmt = $this->db->prepare("UPDATE Unblock_Appeal SET user_id = :user_id, title = :title, body_ = :body, status_ = :status WHERE id = :id");
                return $stmt->execute([
                    ':user_id' => $this->userId,
                    ':title' => $this->title,
                    ':body' => $this->body,
                    ':status' => $this->status,
                    ':id' => $this->id
                ]);
            } 
            
            // Caso id não esteja definido (criação de Novo pedido de Desbloqueio).
            else {
                // Preparação do SQL Statement e posterior execução.
                $stmt = $this->db->prepare("INSERT INTO Unblock_Appeal (user_id, title, body_, date_, time_, status_) VALUES (:user_id, :title, :body, DATE('now'), TIME('now'), :status)");
                $result = $stmt->execute([
                    ':user_id' => $this->userId,
                    ':title' => $this->title,
                    ':body' => $this->body,
                    ':status' => $this->status
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
     * Aprova o pedido de desbloqueio atual e atualiza o estado para 'approved'.
     *
     * @return bool Resultado da operação.
     */
    public function approve() {
        if (!$this->id) return false;
        $this->status = 'approved';
        return $this->save();
    }

    /**
     * Rejeita o pedido de desbloqueio atual e atualiza o estado para 'rejected'.
     *
     * @return bool Resultado da operação.
     */
    public function reject() {
        if (!$this->id) return false;
        $this->status = 'rejected';
        return $this->save();
    }

    /**
     * Elimina o pedido de desbloqueio da base de dados.
     *
     * @return bool Resultado da operação.
     */
    public function delete() {
        // Verifica se o id está definido na instância do Model
        if (!$this->id) return false;

        // Prepara e Executa o SQl Statement para a eliminação
        try {
            $stmt = $this->db->prepare("DELETE FROM Unblock_Appeal WHERE id = :id");
            return $stmt->execute([':id' => $this->id]);
        } 
        
        // Gestão de falhas na operação.
        catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Procura um pedido de desbloqueio através do seu ID.
     *
     * @param PDO $db Instância da base de dados.
     * @param int $id ID do pedido de desbloqueio.
     * @return UnblockAppeal|null Instância ou null se não existir.
     */
    public static function findById($db, $id) {
        try {
            // Preparação e Execução do SQL Statement.
            $stmt = $db->prepare("SELECT * FROM Unblock_Appeal WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Caso o resultado não seja null, cria a instância de User e retorna-a.
            return $result ? self::createFromArray($db, $result) : null;
        } 
        
        // Gestão de falhas na operação.
        catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Obtém todos os pedido de desbloqueios submetidos por um utilizador.
     *
     * @param PDO $db Instância da base de dados.
     * @param int $userId ID do utilizador.
     * @return array Lista de pedido de desbloqueios.
     */
    public static function findByUserId($db, $userId) {
        try {
            // Preparação e Execução do SQL Statement.
            $stmt = $db->prepare("SELECT * FROM Unblock_Appeal WHERE user_id = :user_id ORDER BY date_ DESC, time_ DESC");
            $stmt->execute([':user_id' => $userId]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Para cada linha retornada, cria a instância de User adiciona ao array $appeals.
            $appeals = [];
            foreach ($results as $result) {
                $appeals[] = self::createFromArray($db, $result);
            }
            return $appeals;
        } 
        
        // Gestão de falhas na operação.
        catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Verifica se o utilizador tem algum pedido de desbloqueio pendente.
     *
     * @param PDO $db Instância da base de dados.
     * @param int $userId ID do utilizador.
     * @return bool Verdadeiro se houver pedido de desbloqueios pendentes.
     */
    public static function hasPendingAppeal($db, $userId) {
        try {
            // Preparação e Execução do SQL Statement.
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM Unblock_Appeal WHERE user_id = :user_id AND status_ = 'pending'");
            $stmt->execute([':user_id' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;    // Retorna true ou false em função do número de Instâncias no array.
        } 
        
        // Gestão de falhas na operação.
        catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Constrói uma instância de UnblockAppeal a partir de um array associativo.
     *
     * @param PDO $db Instância da base de dados.
     * @param array $array Dados do pedido de desbloqueio.
     * @return UnblockAppeal Instância do modelo preenchida.
     */
    private static function createFromArray($db, $array) {
        $appeal = new self($db);
        $appeal->setId($array['id']);
        $appeal->setUserId($array['user_id']);
        $appeal->setTitle($array['title']);
        $appeal->setBody($array['body_']);
        $appeal->setDate($array['date_']);
        $appeal->setTime($array['time_']);
        $appeal->setStatus($array['status_']);
        return $appeal;
    }
}
?>
