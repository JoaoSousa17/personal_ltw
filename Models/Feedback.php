<?php
require_once(dirname(__FILE__) . '/../Database/connection.php');

/**
 * Classe responsável por representar e gerir feedbacks/avaliações dos serviços.
 * Cada feedback está associado a um utilizador e a um serviço específico.
 */
class Feedback {
    private $id;
    private $userId;
    private $serviceId;
    private $title;
    private $description;
    private $evaluation;
    private $date;
    private $time;
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
     * Getter para o ID do feedback.
     * 
     * @return int ID do feedback.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Getter para o ID do utilizador.
     * 
     * @return int ID do utilizador que deixou o feedback.
     */
    public function getUserId() {
        return $this->userId;
    }

    /**
     * Getter para o ID do serviço.
     * 
     * @return int ID do serviço avaliado.
     */
    public function getServiceId() {
        return $this->serviceId;
    }

    /**
     * Getter para o título do feedback.
     * 
     * @return string Título do feedback.
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Getter para a descrição do feedback.
     * 
     * @return string|null Descrição do feedback.
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Getter para a avaliação.
     * 
     * @return float Avaliação de 0 a 5.
     */
    public function getEvaluation() {
        return $this->evaluation;
    }

    /**
     * Getter para a data do feedback.
     * 
     * @return string Data do feedback.
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * Getter para a hora do feedback.
     * 
     * @return string Hora do feedback.
     */
    public function getTime() {
        return $this->time;
    }

    /********
     Setters
    ********/

    /**
     * Setter para o ID do feedback.
     * 
     * @param int $id Novo ID.
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Setter para o ID do utilizador.
     * 
     * @param int $userId ID do utilizador.
     */
    public function setUserId($userId) {
        $this->userId = $userId;
    }

    /**
     * Setter para o ID do serviço.
     * 
     * @param int $serviceId ID do serviço.
     */
    public function setServiceId($serviceId) {
        $this->serviceId = $serviceId;
    }

    /**
     * Setter para o título do feedback.
     * 
     * @param string $title Título do feedback.
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Setter para a descrição do feedback.
     * 
     * @param string $description Descrição do feedback.
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * Setter para a avaliação.
     * 
     * @param float $evaluation Avaliação de 0 a 5.
     */
    public function setEvaluation($evaluation) {
        $this->evaluation = $evaluation;
    }

    /**
     * Setter para a data do feedback.
     * 
     * @param string $date Data do feedback.
     */
    public function setDate($date) {
        $this->date = $date;
    }

    /**
     * Setter para a hora do feedback.
     * 
     * @param string $time Hora do feedback.
     */
    public function setTime($time) {
        $this->time = $time;
    }

    /**********
     Functions
    **********/

    /**
     * Guarda o feedback na base de dados.
     * 
     * @return bool Verdadeiro se a operação for bem-sucedida.
     */
    public function save() {
        // Validar campos obrigatórios
        if (!$this->userId || !$this->serviceId || !$this->title || $this->evaluation === null) {
            return false;
        }

        // Validar avaliação (0 a 5, incrementos de 0.5)
        if ($this->evaluation < 0 || $this->evaluation > 5 || ($this->evaluation * 2) != (int)($this->evaluation * 2)) {
            return false;
        }

        try {
            if ($this->id) {
                // Atualizar feedback existente
                $stmt = $this->db->prepare("
                    UPDATE Feedback 
                    SET title = :title, 
                        description_ = :description, 
                        evaluation = :evaluation 
                    WHERE id = :id
                ");
                
                return $stmt->execute([
                    ':title' => $this->title,
                    ':description' => $this->description,
                    ':evaluation' => $this->evaluation,
                    ':id' => $this->id
                ]);
            } else {
                // Inserir novo feedback
                $stmt = $this->db->prepare("
                    INSERT INTO Feedback (user_id, service_id, title, description_, evaluation, date_, time_) 
                    VALUES (:user_id, :service_id, :title, :description, :evaluation, DATE('now'), TIME('now'))
                ");
                
                $result = $stmt->execute([
                    ':user_id' => $this->userId,
                    ':service_id' => $this->serviceId,
                    ':title' => $this->title,
                    ':description' => $this->description,
                    ':evaluation' => $this->evaluation
                ]);

                if ($result) {
                    $this->id = $this->db->lastInsertId();
                    
                    // Obter data e hora geradas automaticamente
                    $stmt = $this->db->prepare("SELECT date_, time_ FROM Feedback WHERE id = :id");
                    $stmt->execute([':id' => $this->id]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($row) {
                        $this->date = $row['date_'];
                        $this->time = $row['time_'];
                    }
                    
                    return true;
                }
                return false;
            }
        } catch (PDOException $e) {
            error_log("Erro ao guardar feedback: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove o feedback da base de dados.
     * 
     * @return bool Verdadeiro se a operação for bem-sucedida.
     */
    public function delete() {
        if (!$this->id) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM Feedback WHERE id = :id");
            return $stmt->execute([':id' => $this->id]);
        } catch (PDOException $e) {
            error_log("Erro ao eliminar feedback: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Procura um feedback pelo ID.
     * 
     * @param PDO $db Instância da base de dados.
     * @param int $id ID do feedback.
     * @return Feedback|null Instância do feedback ou null se não encontrado.
     */
    public static function findById($db, $id) {
        try {
            $stmt = $db->prepare("SELECT * FROM Feedback WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ? self::createFromArray($db, $result) : null;
        } catch (PDOException $e) {
            error_log("Erro ao procurar feedback por ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtém todos os feedbacks de um serviço.
     * 
     * @param PDO $db Instância da base de dados.
     * @param int $serviceId ID do serviço.
     * @return array Lista de feedbacks do serviço.
     */
    public static function getByServiceId($db, $serviceId) {
        try {
            $stmt = $db->prepare("
                SELECT f.*, u.name_ as user_name, u.username 
                FROM Feedback f
                JOIN User_ u ON f.user_id = u.id
                WHERE f.service_id = :service_id 
                ORDER BY f.date_ DESC, f.time_ DESC
            ");
            $stmt->execute([':service_id' => $serviceId]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $feedbacks = [];
            foreach ($results as $result) {
                $feedback = self::createFromArray($db, $result);
                // Adicionar informações do utilizador
                $feedback->userName = $result['user_name'];
                $feedback->username = $result['username'];
                $feedbacks[] = $feedback;
            }
            return $feedbacks;
        } catch (PDOException $e) {
            error_log("Erro ao obter feedbacks do serviço: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtém todos os feedbacks de um utilizador.
     * 
     * @param PDO $db Instância da base de dados.
     * @param int $userId ID do utilizador.
     * @return array Lista de feedbacks do utilizador.
     */
    public static function getByUserId($db, $userId) {
        try {
            $stmt = $db->prepare("
                SELECT f.*, s.name_ as service_name 
                FROM Feedback f
                JOIN Service_ s ON f.service_id = s.id
                WHERE f.user_id = :user_id 
                ORDER BY f.date_ DESC, f.time_ DESC
            ");
            $stmt->execute([':user_id' => $userId]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $feedbacks = [];
            foreach ($results as $result) {
                $feedback = self::createFromArray($db, $result);
                // Adicionar nome do serviço
                $feedback->serviceName = $result['service_name'];
                $feedbacks[] = $feedback;
            }
            return $feedbacks;
        } catch (PDOException $e) {
            error_log("Erro ao obter feedbacks do utilizador: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Verifica se um utilizador já deixou feedback para um serviço.
     * 
     * @param PDO $db Instância da base de dados.
     * @param int $userId ID do utilizador.
     * @param int $serviceId ID do serviço.
     * @return bool True se já existe feedback, false caso contrário.
     */
    public static function userHasFeedback($db, $userId, $serviceId) {
        try {
            $stmt = $db->prepare("
                SELECT COUNT(*) as count 
                FROM Feedback 
                WHERE user_id = :user_id AND service_id = :service_id
            ");
            $stmt->execute([':user_id' => $userId, ':service_id' => $serviceId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Erro ao verificar feedback existente: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Calcula a média de avaliações de um serviço.
     * 
     * @param PDO $db Instância da base de dados.
     * @param int $serviceId ID do serviço.
     * @return array Array com 'average' e 'count'.
     */
    public static function getServiceRating($db, $serviceId) {
        try {
            $stmt = $db->prepare("
                SELECT AVG(evaluation) as average, COUNT(*) as count 
                FROM Feedback 
                WHERE service_id = :service_id
            ");
            $stmt->execute([':service_id' => $serviceId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'average' => $result['average'] ? round($result['average'], 1) : 0,
                'count' => (int)$result['count']
            ];
        } catch (PDOException $e) {
            error_log("Erro ao calcular rating do serviço: " . $e->getMessage());
            return ['average' => 0, 'count' => 0];
        }
    }

    /**
     * Obtém os melhores feedbacks (ordenados por avaliação).
     * 
     * @param PDO $db Instância da base de dados.
     * @param int $limit Limite de resultados.
     * @return array Lista dos melhores feedbacks.
     */
    public static function getTopRatedFeedbacks($db, $limit = 10) {
        try {
            $stmt = $db->prepare("
                SELECT f.*, u.name_ as user_name, s.name_ as service_name 
                FROM Feedback f
                JOIN User_ u ON f.user_id = u.id
                JOIN Service_ s ON f.service_id = s.id
                WHERE f.evaluation >= 4
                ORDER BY f.evaluation DESC, f.date_ DESC
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $feedbacks = [];
            foreach ($results as $result) {
                $feedback = self::createFromArray($db, $result);
                $feedback->userName = $result['user_name'];
                $feedback->serviceName = $result['service_name'];
                $feedbacks[] = $feedback;
            }
            return $feedbacks;
        } catch (PDOException $e) {
            error_log("Erro ao obter top feedbacks: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Cria uma instância de Feedback a partir de um array de dados.
     * 
     * @param PDO $db Instância da base de dados.
     * @param array $array Dados do feedback.
     * @return Feedback Instância da classe preenchida.
     */
    private static function createFromArray($db, $array) {
        $feedback = new self($db);
        $feedback->setId($array['id']);
        $feedback->setUserId($array['user_id']);
        $feedback->setServiceId($array['service_id']);
        $feedback->setTitle($array['title']);
        $feedback->setDescription($array['description_']);
        $feedback->setEvaluation($array['evaluation']);
        $feedback->setDate($array['date_']);
        $feedback->setTime($array['time_']);
        return $feedback;
    }

    /**
     * Converte o objeto Feedback num array associativo.
     * 
     * @return array Representação do feedback em array.
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'service_id' => $this->serviceId,
            'title' => $this->title,
            'description_' => $this->description,
            'evaluation' => $this->evaluation,
            'date_' => $this->date,
            'time_' => $this->time
        ];
    }

    /**
     * Converte a avaliação numérica em estrelas visuais.
     * 
     * @return string HTML das estrelas.
     */
    public function getStarsHtml() {
        $fullStars = floor($this->evaluation);
        $hasHalfStar = ($this->evaluation - $fullStars) >= 0.5;
        $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
        
        $html = '';
        
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
            $html .= '<span class="star empty">★</span>';
        }
        
        return $html;
    }

    /**
     * Obtém uma versão resumida da descrição.
     * 
     * @param int $maxLength Comprimento máximo do resumo.
     * @return string Descrição resumida.
     */
    public function getShortDescription($maxLength = 100) {
        if (!$this->description || strlen($this->description) <= $maxLength) {
            return $this->description;
        }
        
        return substr($this->description, 0, $maxLength) . '...';
    }

    /**
     * Formata a data e hora do feedback.
     * 
     * @return string Data e hora formatadas.
     */
    public function getFormattedDateTime() {
        if (!$this->date || !$this->time) {
            return '';
        }
        
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $this->date . ' ' . $this->time);
        return $dateTime ? $dateTime->format('d/m/Y H:i') : $this->date . ' ' . $this->time;
    }
}
?>
