<?php
require_once(dirname(__FILE__) . '/../Database/connection.php');

/**
 * Classe responsável pela representação e gestão de utilizadores no sistema.
 * Baseada na estrutura real da tabela User_ da base de dados.
 */
class User {
    private $id;
    private $name;
    private $password;
    private $email;
    private $username;
    private $webLink;
    private $phoneNumber;
    private $profilePhoto;
    private $isAdmin;
    private $creationDate;
    private $isFreelancer;
    private $currency;
    private $isBlocked;
    private $nightMode;
    private $db;

    /**
     * Construtor da classe.
     * 
     * @param PDO $db Instância da ligação à base de dados.
     */
    public function __construct($db) {
        $this->db = $db;
        $this->currency = 'eur'; // Moeda padrão
        $this->isAdmin = false;
        $this->isBlocked = false;
        $this->isFreelancer = false;
        $this->nightMode = false;
    }

    /********
     Getters
    ********/

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getWebLink() {
        return $this->webLink;
    }

    public function getPhoneNumber() {
        return $this->phoneNumber;
    }

    public function getProfilePhoto() {
        return $this->profilePhoto;
    }

    public function getIsAdmin() {
        return $this->isAdmin;
    }

    public function getCreationDate() {
        return $this->creationDate;
    }

    public function getRegisterDate() {
        return $this->creationDate;
    }

    public function getIsFreelancer() {
        return $this->isFreelancer;
    }

    public function getCurrency() {
        return $this->currency;
    }

    public function getIsBlocked() {
        return $this->isBlocked;
    }

    public function getNightMode() {
        return $this->nightMode;
    }

    /********
     Setters
    ********/

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setPassword($password) {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function setPasswordHash($passwordHash) {
        $this->password = $passwordHash;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function setWebLink($webLink) {
        $this->webLink = $webLink;
    }

    public function setPhoneNumber($phoneNumber) {
        $this->phoneNumber = $phoneNumber;
    }

    public function setProfilePhoto($profilePhoto) {
        $this->profilePhoto = $profilePhoto;
    }

    public function setIsAdmin($isAdmin) {
        $this->isAdmin = (bool)$isAdmin;
    }

    public function setCreationDate($creationDate) {
        $this->creationDate = $creationDate;
    }

    public function setRegisterDate($registerDate) {
        $this->creationDate = $registerDate;
    }

    public function setIsFreelancer($isFreelancer) {
        $this->isFreelancer = (bool)$isFreelancer;
    }

    public function setCurrency($currency) {
        $this->currency = $currency;
    }

    public function setIsBlocked($isBlocked) {
        $this->isBlocked = (bool)$isBlocked;
    }

    public function setNightMode($nightMode) {
        $this->nightMode = (bool)$nightMode;
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
        if (!$this->username || !$this->email) {
            return false;
        }

        try {
            if ($this->id) {
                // Atualização de utilizador existente
                $stmt = $this->db->prepare("
                    UPDATE User_ 
                    SET name_ = :name, 
                        email = :email, 
                        username = :username,
                        web_link = :web_link,
                        phone_number = :phone_number,
                        profile_photo = :profile_photo,
                        is_admin = :is_admin, 
                        is_freelancer = :is_freelancer,
                        currency = :currency,
                        is_blocked = :is_blocked,
                        night_mode = :night_mode
                    WHERE id = :id
                ");

                $params = [
                    ':name' => $this->name,
                    ':email' => $this->email,
                    ':username' => $this->username,
                    ':web_link' => $this->webLink,
                    ':phone_number' => $this->phoneNumber,
                    ':profile_photo' => $this->profilePhoto,
                    ':is_admin' => $this->isAdmin ? 1 : 0,
                    ':is_freelancer' => $this->isFreelancer ? 1 : 0,
                    ':currency' => $this->currency,
                    ':is_blocked' => $this->isBlocked ? 1 : 0,
                    ':night_mode' => $this->nightMode ? 1 : 0,
                    ':id' => $this->id
                ];

                // Incluir password apenas se foi alterada
                if (!empty($this->password)) {
                    $stmt = $this->db->prepare("
                        UPDATE User_ 
                        SET name_ = :name, 
                            email = :email, 
                            username = :username,
                            password_ = :password,
                            web_link = :web_link,
                            phone_number = :phone_number,
                            profile_photo = :profile_photo,
                            is_admin = :is_admin, 
                            is_freelancer = :is_freelancer,
                            currency = :currency,
                            is_blocked = :is_blocked,
                            night_mode = :night_mode
                        WHERE id = :id
                    ");
                    $params[':password'] = $this->password;
                }

                return $stmt->execute($params);
            } else {
                // Criação de novo utilizador
                $stmt = $this->db->prepare("
                    INSERT INTO User_ (name_, password_, email, username, web_link, 
                                     phone_number, profile_photo, is_admin, is_freelancer, 
                                     currency, is_blocked, night_mode) 
                    VALUES (:name, :password, :email, :username, :web_link, 
                           :phone_number, :profile_photo, :is_admin, :is_freelancer, 
                           :currency, :is_blocked, :night_mode)
                ");

                $result = $stmt->execute([
                    ':name' => $this->name,
                    ':password' => $this->password,
                    ':email' => $this->email,
                    ':username' => $this->username,
                    ':web_link' => $this->webLink,
                    ':phone_number' => $this->phoneNumber,
                    ':profile_photo' => $this->profilePhoto,
                    ':is_admin' => $this->isAdmin ? 1 : 0,
                    ':is_freelancer' => $this->isFreelancer ? 1 : 0,
                    ':currency' => $this->currency,
                    ':is_blocked' => $this->isBlocked ? 1 : 0,
                    ':night_mode' => $this->nightMode ? 1 : 0
                ]);

                if ($result) {
                    $this->id = $this->db->lastInsertId();
                    return true;
                }
                return false;
            }
        } catch (PDOException $e) {
            error_log("Erro ao guardar utilizador: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove o utilizador da base de dados.
     * 
     * @return bool Verdadeiro se a operação for bem-sucedida.
     */
    public function delete() {
        if (!$this->id) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM User_ WHERE id = :id");
            return $stmt->execute([':id' => $this->id]);
        } catch (PDOException $e) {
            error_log("Erro ao eliminar utilizador: " . $e->getMessage());
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
     * Promove o utilizador a administrador.
     * 
     * @return bool Verdadeiro se a operação for bem-sucedida.
     */
    public function promoteToAdmin() {
        $this->isAdmin = true;
        return $this->save();
    }

    /**
     * Remove privilégios de administrador do utilizador.
     * 
     * @return bool Verdadeiro se a operação for bem-sucedida.
     */
    public function demoteFromAdmin() {
        $this->isAdmin = false;
        return $this->save();
    }

    /**
     * Define o utilizador como freelancer.
     * 
     * @return bool Verdadeiro se a operação for bem-sucedida.
     */
    public function setAsFreelancer() {
        $this->isFreelancer = true;
        return $this->save();
    }

    /**
     * Remove o status de freelancer do utilizador.
     * 
     * @return bool Verdadeiro se a operação for bem-sucedida.
     */
    public function removeFreelancerStatus() {
        $this->isFreelancer = false;
        return $this->save();
    }

    /**
     * Verifica se a password fornecida está correta.
     * 
     * @param string $password Password a verificar
     * @return bool True se a password estiver correta
     */
    public function verifyPassword($password) {
        return password_verify($password, $this->password);
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
            $stmt = $db->prepare("SELECT * FROM User_ WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return self::createFromArray($db, $result);
            }
            return null;
        } catch (PDOException $e) {
            error_log("Erro ao procurar utilizador por ID: " . $e->getMessage());
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
            $stmt = $db->prepare("SELECT * FROM User_ WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return self::createFromArray($db, $result);
            }
            return null;
        } catch (PDOException $e) {
            error_log("Erro ao procurar utilizador por username: " . $e->getMessage());
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
            $stmt = $db->prepare("SELECT * FROM User_ WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return self::createFromArray($db, $result);
            }
            return null;
        } catch (PDOException $e) {
            error_log("Erro ao procurar utilizador por email: " . $e->getMessage());
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
            $stmt = $db->prepare("SELECT * FROM User_ ORDER BY creation_date DESC");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $users = [];
            foreach ($results as $result) {
                $users[] = self::createFromArray($db, $result);
            }
            return $users;
        } catch (PDOException $e) {
            error_log("Erro ao obter todos os utilizadores: " . $e->getMessage());
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
            $stmt = $db->prepare("SELECT * FROM User_ WHERE is_blocked = 1 ORDER BY creation_date DESC");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $users = [];
            foreach ($results as $result) {
                $users[] = self::createFromArray($db, $result);
            }
            return $users;
        } catch (PDOException $e) {
            error_log("Erro ao obter utilizadores bloqueados: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtém todos os utilizadores administradores.
     * 
     * @param PDO $db Instância da base de dados.
     * @return array Lista de utilizadores administradores.
     */
    public static function getAllAdmins($db) {
        try {
            $stmt = $db->prepare("SELECT * FROM User_ WHERE is_admin = 1 ORDER BY creation_date DESC");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $users = [];
            foreach ($results as $result) {
                $users[] = self::createFromArray($db, $result);
            }
            return $users;
        } catch (PDOException $e) {
            error_log("Erro ao obter administradores: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtém todos os freelancers.
     * 
     * @param PDO $db Instância da base de dados.
     * @return array Lista de freelancers.
     */
    public static function getAllFreelancers($db) {
        try {
            $stmt = $db->prepare("SELECT * FROM User_ WHERE is_freelancer = 1 ORDER BY creation_date DESC");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $users = [];
            foreach ($results as $result) {
                $users[] = self::createFromArray($db, $result);
            }
            return $users;
        } catch (PDOException $e) {
            error_log("Erro ao obter freelancers: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Autentica um utilizador com username/email e password.
     * 
     * @param PDO $db Instância da base de dados.
     * @param string $login username ou email a ser utilizado para tentar a autenticação.
     * @param string $password password a ser utilizada para tentar a autenticação.
     * @return User|null Instância do User (autenticação teve sucesso) ou null (caso contrário).
     */
    public static function authenticate($db, $login, $password) {
        // Tentar encontrar por username ou email
        $user = self::findByUsername($db, $login);
        if (!$user) {
            $user = self::findByEmail($db, $login);
        }

        // Verificar password
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }

        return null;
    }

    /**
     * Conta o número total de utilizadores.
     * 
     * @param PDO $db Instância da base de dados.
     * @return int Número total de utilizadores.
     */
    public static function countUsers($db) {
        try {
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM User_");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['count'];
        } catch (PDOException $e) {
            error_log("Erro ao contar utilizadores: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Conta o número de utilizadores registados hoje.
     * 
     * @param PDO $db Instância da base de dados.
     * @return int Número de utilizadores registados hoje.
     */
    public static function countTodayRegistrations($db) {
        try {
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM User_ WHERE DATE(creation_date) = DATE('now')");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['count'];
        } catch (PDOException $e) {
            error_log("Erro ao contar registos de hoje: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Cria uma instância da classe User a partir de um array associativo.
     * 
     * @param PDO $db Instância da base de dados.
     * @param array $array Array associativo com os dados do utilizador.
     * @return User Objeto User populado com os dados fornecidos.
     */
    private static function createFromArray($db, $array) {
        $user = new self($db);

        // Dados obrigatórios
        $user->setId($array['id']);
        $user->setName($array['name_']);
        $user->setPasswordHash($array['password_']); // Usar hash direto, não re-hash
        $user->setEmail($array['email']);
        $user->setUsername($array['username']);
        $user->setIsAdmin((bool)$array['is_admin']);
        $user->setIsBlocked((bool)$array['is_blocked']);
        $user->setIsFreelancer((bool)$array['is_freelancer']);
        $user->setCurrency($array['currency']);
        $user->setNightMode((bool)$array['night_mode']);

        // Dados opcionais
        if (isset($array['web_link'])) {
            $user->setWebLink($array['web_link']);
        }
        if (isset($array['phone_number'])) {
            $user->setPhoneNumber($array['phone_number']);
        }
        if (isset($array['profile_photo'])) {
            $user->setProfilePhoto($array['profile_photo']);
        }
        if (isset($array['creation_date'])) {
            $user->setCreationDate($array['creation_date']);
        }

        return $user;
    }

    /**
     * Converte o objeto User num array associativo.
     * 
     * @return array Representação do utilizador em array.
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'name_' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'web_link' => $this->webLink,
            'phone_number' => $this->phoneNumber,
            'profile_photo' => $this->profilePhoto,
            'is_admin' => $this->isAdmin,
            'creation_date' => $this->creationDate,
            'is_freelancer' => $this->isFreelancer,
            'currency' => $this->currency,
            'is_blocked' => $this->isBlocked,
            'night_mode' => $this->nightMode
        ];
    }
}
?>