<?php
require_once(dirname(__FILE__) . '/../Database/connection.php');

/**
 * Classe responsável por representar e gerir subscrições da newsletter.
 * Permite guardar, consultar e remover subscrições com base no email.
 */
class NewsletterSubscription {
    private $id;
    private $email;
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
     * Getter para o ID da subscrição.
     * 
     * @return int ID da subscrição.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Getter para o email associado à subscrição.
     * 
     * @return string Email do subscritor.
     */
    public function getEmail() {
        return $this->email;
    }

    /********
     Setters
    ********/

    /**
     * Setter para o ID da subscrição.
     * 
     * @param int $id Novo ID.
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Setter para o email da subscrição.
     * 
     * @param string $email Novo email.
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**********
     Functions
    **********/

    /**
     * Guarda uma nova subscrição na base de dados.
     * 
     * @return bool Verdadeiro se a subscrição for bem-sucedida.
     */
    public function save() {
        // Valida se o email é válido
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        try {
            // Prepara e executa o statement de inserção
            $stmt = $this->db->prepare("INSERT INTO Newsletter_email (email) VALUES (:email)");
            $result = $stmt->execute([':email' => $this->email]);

            // Em caso de sucesso, atualiza o ID da instância
            if ($result) {
                $this->id = $this->db->lastInsertId();
                return true;
            }
            return false;
        } 
        
        // Gestão de falhas na operação.
        catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Remove uma subscrição da base de dados.
     * 
     * @return bool Verdadeiro se a operação for bem-sucedida.
     */
    public function delete() {
        // Verifica se o ID está definido
        if (!$this->id) {
            return false;
        }

        try {
            // Prepara e executa o statement de remoção
            $stmt = $this->db->prepare("DELETE FROM Newsletter_email WHERE id = :id");
            return $stmt->execute([':id' => $this->id]);
        } 
        
        // Gestão de falhas na operação.
        catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Obtém todas as subscrições existentes na base de dados.
     * 
     * @param PDO $db Instância da base de dados.
     * @return array Lista de subscrições.
     */
    public static function getAllSubscriptions($db) {
        try {
            // Prepara e executa o statement de seleção
            $stmt = $db->prepare("SELECT * FROM Newsletter_email");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Procura uma subscrição através do email.
     * 
     * @param PDO $db Instância da base de dados.
     * @param string $email Email a pesquisar.
     * @return NewsletterSubscription|null Instância encontrada ou null.
     */
    public static function findByEmail($db, $email) {
        try {
            // Prepara e executa o statement de seleção
            $stmt = $db->prepare("SELECT * FROM Newsletter_email WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Em caso de sucesso, cria e devolve a instância
            if ($result) {
                $subscription = new self($db);
                $subscription->setId($result['id']);
                $subscription->setEmail($result['email']);
                return $subscription;
            }
            return null;
        } 
        
        // Gestão de falhas na operação.
        catch (PDOException $e) {
            return null;
        }
    }
}
?>
