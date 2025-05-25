<?php
require_once(dirname(__FILE__) . '/../Database/connection.php');

/**
 * Classe responsável pela representação e gestão de utilizadores no sistema.
 * Inclui operações de base de dados, autenticação, e controlo de estado (admin/bloqueado).
 */
class User {
    private $id;
    private $username;
    private $email;
    private $password;
    private $name;
    private $isAdmin;
    private $isBlocked;
    private $db;
    private $registerDate;
    private $bio;
    private $webLink;
    private $isFreelancer;
    private $currency;
    private $profilePhoto;

    /**
     * Construtor da classe.
     * 
     * @param PDO $db Instância da ligação à base de dados.
     */
    public function __construct($db) {
        $this->db = $db;
        $this->currency = 'eur'; // Moeda padrão
    }

    /********
     Getters
    ********/

    /** 
     * Getter para o id do User.
     * 
     * @return int ID do utilizador.
     */
    public function getId() {
        return $this->id;
    }

    /** 
     * Getter para o username do User.
     * 
     * @return string Username do utilizador.
     */
    public function getUsername() {
        return $this->username;
    }

    /** 
     * Getter para o email do User.
     * 
     * @return string Email do utilizador.
     */
    public function getEmail() {
        return $this->email;
    }

    /** 
     * Getter para o nome do User.
     * 
     * @return string Nome do utilizador.
     */
    public function getName() {
        return $this->name;
    }

    /** 
     * Getter para o bolleen isAdmin do User (ativo, caso o utilizador seja administrador).
     * 
     * @return bool Indica se o utilizador é administrador.
     */
    public function getIsAdmin() {
        return $this->isAdmin;
    }

    /** 
     * Getter para o bolleen isBlocked do User (ativo, caso o utilizador se encontre bloqueado).
     * 
     * @return bool Indica se o utilizador está bloqueado.
     */
    public function getIsBlocked() {
        return $this->isBlocked;
    }

    /** 
     * Getter para a data de criação da conta do User.
     * 
     * @return string Data de registo do utilizador.
     */
    public function getRegisterDate() {
        return $this->registerDate;
    }

    /** 
     * Getter para a biografia do User.
     * 
     * @return string|null Biografia do utilizador.
     */
    public function getBio() {
        return $this->bio;
    }

    /** 
     * Getter para o link disponibilizado pelo User.
     * 
     * @return string|null Link externo associado ao perfil.
     */
    public function getWebLink() {
        return $this->webLink;
    }

    /** 
     * Getter para o bolleen isFreelancer do User (ativo, caso o utilizador tenha anúncios publicados).
     * 
     * @return bool Indica se o utilizador é freelancer.
     */
    public function getIsFreelancer() {
        return $this->isFreelancer;
    }

    /** 
     * Getter para a moeda preferida do User.
     * 
     * @return string Código da moeda preferida.
     */
    public function getCurrency() {
        return $this->currency;
    }

    /** 
     * Getter para o ID da foto de perfil do User.
     * 
     * @return int|null ID da foto de perfil.
     */
    public function getProfilePhoto() {
        return $this->profilePhoto;
    }

    /********
     Setters
    ********/

    /** 
     * Setter para o id do User.
     * 
     * @param int $id Novo ID do utilizador.
     */
    public function setId($id) {
        $this->id = $id;
    }

    /** 
     * Setter para o username do User.
     * 
     * @param string $username Novo username.
     */
    public function setUsername($username) {
        $this->username = $username;
    }

    /** 
     * Setter para o email do User.
     * 
     * @param string $email Novo email.
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /** 
     * Setter para a password, definindo-a, após fazer o hash com algoritmo seguro.
     * 
     * @param string $password Palavra-passe em texto simples.
     */
    public function setPassword($password) {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    /** 
     * Setter para o nome do User.
     * 
     * @param string $name Novo nome do utilizador (!=username ).
     */
    public function setName($name) {
        $this->name = $name;
    }

    /** 
     * Setter para o bollean isAdmin do User (ativo, caso o utilizador seja administrador).
     * 
     * @param bool $isAdmin Novo estado do boleano isAdmin.
     */
    public function setIsAdmin($isAdmin) {
        $this->isAdmin = $isAdmin;
    }

    /** 
     * Setter para o bolleen isBlocked do User (ativo, caso o utilizador se encontre bloqueado).
     * 
     * @param bool $isBlocked Novo estado do boleano isBlocked.
     */
    public function setIsBlocked($isBlocked) {
        $this->isBlocked = $isBlocked;
    }

    /** 
     * Setter para a data de criação da conta do User.
     * 
     * @param string $registerDate Nova data de registo.
     */
    public function setRegisterDate($registerDate) {
        $this->registerDate = $registerDate;
    }

    /** 
     * Setter para a biografia do User.
     * 
     * @param string $bio Nova biografia.
     */
    public function setBio($bio) {
        $this->bio = $bio;
    }

    /** 
     * Setter para o link disponibilizado pelo User.
     * 
     * @param string $webLink Novo link externo disponibilizado.
     */
    public function setWebLink($webLink) {
        $this->webLink = $webLink;
    }

    /** 
     * Setter para o bolleen isFreelancer do User (ativo, caso o utilizador tenha anúncios publicados).
     * 
     * @param bool $isFreelancer Novo estado do boleano isFreelancer.
     */
    public function setIsFreelancer($isFreelancer) {
        $this->isFreelancer = $isFreelancer;
    }

    /** 
     * Setter para a moeda preferida do User.
     * 
     * @param string $currency Código da nova moeda preferida.
     */
    public function setCurrency($currency) {
        $this->currency = $currency;
    }

    /** 
     * Setter para o ID da foto de perfil do User.
     * 
     * @param int|null $profilePhoto ID da nova foto de perfil.
     */
    public function setProfilePhoto($profilePhoto) {
        $this->profilePhoto = $profilePhoto;
    }

    /**********
     Functions
    **********/

    /**
     * Guarda ou atualiza o User na base de dados.
     * 
     * @return bool Verdadeiro se a operação for bem-sucedida.
     */
    public function save() {
        // Operação falhará caso o username e o email não estiverem definidos (necessário para identificação)
        if (!$this->username || !$this->email) {
            return false;
        }

        try {
            // Caso id esteja definido (Atualização de dados da conta)
            if ($this->id) {
                //Preparação do SQL Statement
                $stmt = $this->db->prepare("
                    UPDATE User_ 
                    SET username = :username, 
                        email = :email, 
                        name_ = :name, 
                        is_admin = :is_admin, 
                        is_blocked = :is_blocked
                    WHERE id = :id
                ");

                // Preparação dos Parâmetros do SQL Statement
                $params = [
                    ':username' => $this->username,
                    ':email' => $this->email,
                    ':name' => $this->name,
                    ':is_admin' => $this->isAdmin ? 1 : 0,
                    ':is_blocked' => $this->isBlocked ? 1 : 0,
                    ':id' => $this->id
                ];

                // Permitirá atualizar também a password, caso esta esteja definida (caso genérico)
                if (!empty($this->password)) {
                    $stmt = $this->db->prepare("
                        UPDATE User_ 
                        SET username = :username, 
                            email = :email, 
                            password_ = :password, 
                            name_ = :name, 
                            is_admin = :is_admin, 
                            is_blocked = :is_blocked
                        WHERE id = :id
                    ");
                    $params[':password'] = $this->password;
                }

                return $stmt->execute($params); // Execução SQL Statement
            } 
            
            // Caso id não esteja definido (criação de Conta Nova)
            else {
                // Preparação do SQL Statement
                $stmt = $this->db->prepare("
                    INSERT INTO User_ (username, email, password_, name_, is_admin, is_blocked) 
                    VALUES (:username, :email, :password, :name, :is_admin, :is_blocked)
                ");

                // Execução SQL Statement, com respetivos parâmetros
                $result = $stmt->execute([
                    ':username' => $this->username,
                    ':email' => $this->email,
                    ':password' => $this->password,
                    ':name' => $this->name,
                    ':is_admin' => $this->isAdmin ? 1 : 0,
                    ':is_blocked' => $this->isBlocked ? 1 : 0
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
     * Remove o utilizador da base de dados.
     * 
     * @return bool Verdadeiro se a operação for bem-sucedida.
     */
    public function delete() {
        // Verifica se o id está definido na instância do Model
        if (!$this->id) {
            return false;
        }

        // Prepara e Executa o SQl Statement para a eliminação
        try {
            $stmt = $this->db->prepare("DELETE FROM User_ WHERE id = :id");
            return $stmt->execute([':id' => $this->id]);
        }

        // Gestão de falhas na operação.
        catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Bloqueia o utilizador atual.
     * 
     * @return bool Verdadeiro se a operação for bem-sucedida.
     */
    public function blockUser() {
        $this->isBlocked = true;
        return $this->save();
    }

    /**
     * Desbloqueia o utilizador atual.
     * 
     * @return bool Verdadeiro se a operação for bem-sucedida.
     */
    public function unblockUser() {
        $this->isBlocked = false;
        return $this->save();
    }

    /**
     * Procura um utilizador pelo ID.
     * 
     * @param PDO $db Instância da base de dados.
     * @param int $id ID do utilizador a pesquisar.
     * @return User|null Instância do User ou null se não encontrado.
     */
    public static function findById($db, $id) {
        try {
            // Preparação e Execução do SQL Statement.
            $stmt = $db->prepare("SELECT * FROM User_ WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Caso o resultado não seja null, cria a instânvia de User.
            if ($result) {
                return self::createFromArray($db, $result);
            }
            return null;
        } 
        
        // Gestão de falhas na operação.
        catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Procura um utilizador pelo username.
     * 
     * @param PDO $db Instância da base de dados.
     * @param string $username username do utilizador a pesquisar.
     * @return User|null Instância do User ou null se não encontrado.
     */
    public static function findByUsername($db, $username) {
        try {
            // Preparação e Execução do SQL Statement.
            $stmt = $db->prepare("SELECT * FROM User_ WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Caso o resultado não seja null, cria a instância de User.
            if ($result) {
                return self::createFromArray($db, $result);
            }
            return null;
        }
        
        // Gestão de falhas na operação.
        catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Procura um utilizador pelo email.
     * 
     * @param PDO $db Instância da base de dados.
     * @param string $email email do utilizador a pesquisar.
     * @return User|null Instância do User ou null se não encontrado.
     */
    public static function findByEmail($db, $email) {
        try {
            // Preparação e Execução do SQL Statement.
            $stmt = $db->prepare("SELECT * FROM User_ WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Caso o resultado não seja null, cria a instância de User.
            if ($result) {
                return self::createFromArray($db, $result);
            }
            return null;
        } 
        
        // Gestão de falhas na operação.
        catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Obtém todos os utilizadores do sistema.
     * 
     * @param PDO $db Instância da base de dados.
     * @return array Lista de utilizadores.
     */
    public static function getAllUsers($db) {
        try {
            // Preparação e Execução do SQL Statement.
            $stmt = $db->prepare("SELECT * FROM User_");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Para cada linha retornada, cria a instância de User adiciona ao array $users.
            $users = [];
            foreach ($results as $result) {
                $users[] = self::createFromArray($db, $result);
            }
            return $users;
        } 
        
        // Gestão de falhas na operação.
        catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Obtém todos os utilizadores bloqueados.
     * 
     * @param PDO $db Instância da base de dados.
     * @return array Lista de utilizadores bloqueados.
     */
    public static function getAllBlockedUsers($db) {
        try {
            // Preparação e Execução do SQL Statement.
            $stmt = $db->prepare("SELECT * FROM User_ WHERE is_blocked = 1");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Para cada linha retornada, cria a instância de User adiciona ao array $users.
            $users = [];
            foreach ($results as $result) {
                $users[] = self::createFromArray($db, $result);
            }
            return $users;
        } 
        
        // Gestão de falhas na operação.
        catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Autentica um utilizador com username e password.
     * 
     * @param PDO $db Instância da base de dados.
     * @param string $username username a ser utilizado para tentar a autenticação.
     * @param string $password password a ser utilizada para tentar a autenticação.
     * @return User|null Instância do User (autenticação teve sucesso) ou null (caso contrário).
     */
    public static function authenticate($db, $username, $password) {
        //Confirma que existe um User com o username fornecido.
        $user = self::findByUsername($db, $username);

        //Confirma que a password fornecida, corresponde à guardada na DB (hashed).
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }

        return null;
    }

    /**
     * Cria uma instância da classe User a partir de um array associativo.
     * 
     * @param PDO $db Instância da base de dados.
     * @param array $array Array associativo com os dados do utilizador (fetch(PDO::FETCH_ASSOC)).
     * @return User Objeto User populado com os dados fornecidos.
     */
    private static function createFromArray($db, $array) {
        $user = new self($db);

        // Atribuição dos dados principais obrigatórios
        $user->setId($array['id']);
        $user->username = $array['username'];
        $user->email = $array['email'];
        $user->password = $array['password_'];
        $user->name = $array['name_'];
        $user->isAdmin = (bool)$array['is_admin'];
        $user->isBlocked = (bool)$array['is_blocked'];

        // Atribuição de campos opcionais, se existirem no resultado
        if (isset($array['creation_date'])) {
            $user->registerDate = $array['creation_date'];
        }
        if (isset($array['web_link'])) {
            $user->webLink = $array['web_link'];
        }
        if (isset($array['is_freelancer'])) {
            $user->isFreelancer = (bool)$array['is_freelancer'];
        }
        if (isset($array['bio'])) {
            $user->bio = $array['bio'];
        }
        if (isset($array['currency'])) {
            $user->currency = $array['currency'];
        }
        if (isset($array['profile_photo'])) {
            $user->profilePhoto = $array['profile_photo'];
        }

        return $user;
    }
}
?>