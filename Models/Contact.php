<?php
require_once(dirname(__FILE__) . '/../Database/connection.php');

/**
 * Classe responsável por representar e gerir contactos/mensagens enviadas através do formulário de contacto.
 * Permite guardar, consultar e gerir as mensagens recebidas dos utilizadores.
 */
class Contact {
    private $id;
    private $name;
    private $email;
    private $phone;
    private $subject;
    private $message;
    private $createdAt;
    private $createdTime;
    private $isRead;
    private $adminResponse;
    private $responseDate;
    private $responseTime;
    private $db;

    /**
     * Construtor da classe.
     * 
     * @param PDO $db Instância ativa da ligação à base de dados.
     */
    public function __construct($db) {
        $this->db = $db;
        $this->isRead = false;
    }

    /********
     Getters
    ********/

    /**
     * Getter para o ID da mensagem de contacto.
     * 
     * @return int ID da mensagem.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Getter para o nome do remetente.
     * 
     * @return string Nome do remetente.
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Getter para o email do remetente.
     * 
     * @return string Email do remetente.
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Getter para o telefone do remetente.
     * 
     * @return string|null Telefone do remetente.
     */
    public function getPhone() {
        return $this->phone;
    }

    /**
     * Getter para o assunto da mensagem.
     * 
     * @return string Assunto da mensagem.
     */
    public function getSubject() {
        return $this->subject;
    }

    /**
     * Getter para o conteúdo da mensagem.
     * 
     * @return string Conteúdo da mensagem.
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Getter para a data de criação.
     * 
     * @return string Data de criação.
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * Getter para a hora de criação.
     * 
     * @return string Hora de criação.
     */
    public function getCreatedTime() {
        return $this->createdTime;
    }

    /**
     * Getter para o status de leitura.
     * 
     * @return bool Status de leitura.
     */
    public function getIsRead() {
        return $this->isRead;
    }

    /**
     * Getter para a resposta do administrador.
     * 
     * @return string|null Resposta do administrador.
     */
    public function getAdminResponse() {
        return $this->adminResponse;
    }

    /**
     * Getter para a data de resposta.
     * 
     * @return string|null Data de resposta.
     */
    public function getResponseDate() {
        return $this->responseDate;
    }

    /**
     * Getter para a hora de resposta.
     * 
     * @return string|null Hora de resposta.
     */
    public function getResponseTime() {
        return $this->responseTime;
    }

    /********
     Setters
    ********/

    /**
     * Setter para o ID da mensagem.
     * 
     * @param int $id Novo ID.
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Setter para o nome do remetente.
     * 
     * @param string $name Novo nome.
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Setter para o email do remetente.
     * 
     * @param string $email Novo email.
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * Setter para o telefone do remetente.
     * 
     * @param string $phone Novo telefone.
     */
    public function setPhone($phone) {
        $this->phone = $phone;
    }

    /**
     * Setter para o assunto da mensagem.
     * 
     * @param string $subject Novo assunto.
     */
    public function setSubject($subject) {
        $this->subject = $subject;
    }

    /**
     * Setter para o conteúdo da mensagem.
     * 
     * @param string $message Nova mensagem.
     */
    public function setMessage($message) {
        $this->message = $message;
    }

    /**
     * Setter para a data de criação.
     * 
     * @param string $createdAt Nova data de criação.
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }

    /**
     * Setter para a hora de criação.
     * 
     * @param string $createdTime Nova hora de criação.
     */
    public function setCreatedTime($createdTime) {
        $this->createdTime = $createdTime;
    }

    /**
     * Setter para o status de leitura.
     * 
     * @param bool $isRead Novo status de leitura.
     */
    public function setIsRead($isRead) {
        $this->isRead = (bool)$isRead;
    }

    /**
     * Setter para a resposta do administrador.
     * 
     * @param string $adminResponse Nova resposta.
     */
    public function setAdminResponse($adminResponse) {
        $this->adminResponse = $adminResponse;
    }

    /**
     * Setter para a data de resposta.
     * 
     * @param string $responseDate Nova data de resposta.
     */
    public function setResponseDate($responseDate) {
        $this->responseDate = $responseDate;
    }

    /**
     * Setter para a hora de resposta.
     * 
     * @param string $responseTime Nova hora de resposta.
     */
    public function setResponseTime($responseTime) {
        $this->responseTime = $responseTime;
    }

    /**********
     Functions
    **********/

    /**
     * Guarda uma nova mensagem de contacto na base de dados.
     * 
     * @return bool Verdadeiro se a operação for bem-sucedida.
     */
    public function save() {
        // Validar campos obrigatórios
        if (!$this->name || !$this->email || !$this->subject || !$this->message) {
            return false;
        }

        // Validar email
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        try {
            if ($this->id) {
                // Atualizar mensagem existente (para respostas de admin)
                $stmt = $this->db->prepare("
                    UPDATE Contact 
                    SET is_read = :is_read, 
                        admin_response = :admin_response, 
                        response_date = :response_date, 
                        response_time = :response_time 
                    WHERE id = :id
                ");
                
                return $stmt->execute([
                    ':is_read' => $this->isRead ? 1 : 0,
                    ':admin_response' => $this->adminResponse,
                    ':response_date' => $this->responseDate,
                    ':response_time' => $this->responseTime,
                    ':id' => $this->id
                ]);
            } else {
                // Inserir nova mensagem
                $stmt = $this->db->prepare("
                    INSERT INTO Contact (name_, email, phone, subject, message_, created_at, created_time, is_read) 
                    VALUES (:name, :email, :phone, :subject, :message, DATE('now'), TIME('now'), 0)
                ");
                
                $result = $stmt->execute([
                    ':name' => $this->name,
                    ':email' => $this->email,
                    ':phone' => $this->phone,
                    ':subject' => $this->subject,
                    ':message' => $this->message
                ]);

                if ($result) {
                    $this->id = $this->db->lastInsertId();
                    return true;
                }
                return false;
            }
        } catch (PDOException $e) {
            error_log("Erro ao guardar contacto: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Marca a mensagem como lida.
     * 
     * @return bool Verdadeiro se a operação for bem-sucedida.
     */
    public function markAsRead() {
        if (!$this->id) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("UPDATE Contact SET is_read = 1 WHERE id = :id");
            $result = $stmt->execute([':id' => $this->id]);
            
            if ($result) {
                $this->isRead = true;
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Erro ao marcar contacto como lido: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Adiciona uma resposta de administrador.
     * 
     * @param string $response Resposta do administrador.
     * @return bool Verdadeiro se a operação for bem-sucedida.
     */
    public function addAdminResponse($response) {
        if (!$this->id || empty($response)) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE Contact 
                SET admin_response = :response, 
                    response_date = DATE('now'), 
                    response_time = TIME('now'),
                    is_read = 1
                WHERE id = :id
            ");
            
            $result = $stmt->execute([
                ':response' => $response,
                ':id' => $this->id
            ]);
            
            if ($result) {
                $this->adminResponse = $response;
                $this->responseDate = date('Y-m-d');
                $this->responseTime = date('H:i:s');
                $this->isRead = true;
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Erro ao adicionar resposta: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove uma mensagem de contacto da base de dados.
     * 
     * @return bool Verdadeiro se a operação for bem-sucedida.
     */
    public function delete() {
        if (!$this->id) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM Contact WHERE id = :id");
            return $stmt->execute([':id' => $this->id]);
        } catch (PDOException $e) {
            error_log("Erro ao eliminar contacto: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtém todas as mensagens de contacto.
     * 
     * @param PDO $db Instância da base de dados.
     * @param string $orderBy Campo para ordenação (padrão: created_at DESC).
     * @return array Lista de mensagens de contacto.
     */
    public static function getAllContacts($db, $orderBy = 'created_at DESC, created_time DESC') {
        try {
            $stmt = $db->prepare("SELECT * FROM Contact ORDER BY " . $orderBy);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $contacts = [];
            foreach ($results as $result) {
                $contacts[] = self::createFromArray($db, $result);
            }
            return $contacts;
        } catch (PDOException $e) {
            error_log("Erro ao obter contactos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtém mensagens não lidas.
     * 
     * @param PDO $db Instância da base de dados.
     * @return array Lista de mensagens não lidas.
     */
    public static function getUnreadContacts($db) {
        try {
            $stmt = $db->prepare("SELECT * FROM Contact WHERE is_read = 0 ORDER BY created_at DESC, created_time DESC");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $contacts = [];
            foreach ($results as $result) {
                $contacts[] = self::createFromArray($db, $result);
            }
            return $contacts;
        } catch (PDOException $e) {
            error_log("Erro ao obter contactos não lidos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Procura uma mensagem de contacto pelo ID.
     * 
     * @param PDO $db Instância da base de dados.
     * @param int $id ID da mensagem.
     * @return Contact|null Instância da mensagem ou null se não encontrada.
     */
    public static function findById($db, $id) {
        try {
            $stmt = $db->prepare("SELECT * FROM Contact WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ? self::createFromArray($db, $result) : null;
        } catch (PDOException $e) {
            error_log("Erro ao procurar contacto por ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Conta o número total de mensagens.
     * 
     * @param PDO $db Instância da base de dados.
     * @return int Número total de mensagens.
     */
    public static function countContacts($db) {
        try {
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM Contact");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['count'];
        } catch (PDOException $e) {
            error_log("Erro ao contar contactos: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Conta o número de mensagens não lidas.
     * 
     * @param PDO $db Instância da base de dados.
     * @return int Número de mensagens não lidas.
     */
    public static function countUnreadContacts($db) {
        try {
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM Contact WHERE is_read = 0");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['count'];
        } catch (PDOException $e) {
            error_log("Erro ao contar contactos não lidos: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Cria uma instância de Contact a partir de um array de dados.
     * 
     * @param PDO $db Instância da base de dados.
     * @param array $array Dados da mensagem.
     * @return Contact Instância da classe preenchida.
     */
    private static function createFromArray($db, $array) {
        $contact = new self($db);
        $contact->setId($array['id']);
        $contact->setName($array['name_']);
        $contact->setEmail($array['email']);
        $contact->setPhone($array['phone']);
        $contact->setSubject($array['subject']);
        $contact->setMessage($array['message_']);
        $contact->setCreatedAt($array['created_at']);
        $contact->setCreatedTime($array['created_time']);
        $contact->setIsRead((bool)$array['is_read']);
        $contact->setAdminResponse($array['admin_response']);
        $contact->setResponseDate($array['response_date']);
        $contact->setResponseTime($array['response_time']);
        return $contact;
    }

    /**
     * Converte o objeto Contact num array associativo.
     * 
     * @return array Representação da mensagem em array.
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'name_' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'subject' => $this->subject,
            'message_' => $this->message,
            'created_at' => $this->createdAt,
            'created_time' => $this->createdTime,
            'is_read' => $this->isRead,
            'admin_response' => $this->adminResponse,
            'response_date' => $this->responseDate,
            'response_time' => $this->responseTime
        ];
    }
}
?>