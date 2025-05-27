<?php
require_once(dirname(__FILE__) . '/../Database/connection.php');

/**
 * Classe responsável por representar e gerir moradas dos utilizadores.
 * Cada morada está associada a um utilizador e contém informações completas de localização.
 */
class Address {
    private $id;
    private $userId;
    private $street;
    private $doorNum;
    private $floor;
    private $extra;
    private $district;
    private $municipality;
    private $zipCode;
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
     * Getter para o ID da morada.
     * 
     * @return int ID da morada.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Getter para o ID do utilizador.
     * 
     * @return int ID do utilizador associado.
     */
    public function getUserId() {
        return $this->userId;
    }

    /**
     * Getter para a rua.
     * 
     * @return string Nome da rua.
     */
    public function getStreet() {
        return $this->street;
    }

    /**
     * Getter para o número da porta.
     * 
     * @return string Número da porta.
     */
    public function getDoorNum() {
        return $this->doorNum;
    }

    /**
     * Getter para o andar.
     * 
     * @return string|null Andar.
     */
    public function getFloor() {
        return $this->floor;
    }

    /**
     * Getter para informações extra.
     * 
     * @return string|null Informações adicionais.
     */
    public function getExtra() {
        return $this->extra;
    }

    /**
     * Getter para o distrito.
     * 
     * @return string Distrito.
     */
    public function getDistrict() {
        return $this->district;
    }

    /**
     * Getter para o município.
     * 
     * @return string Município.
     */
    public function getMunicipality() {
        return $this->municipality;
    }

    /**
     * Getter para o código postal.
     * 
     * @return string Código postal.
     */
    public function getZipCode() {
        return $this->zipCode;
    }

    /********
     Setters
    ********/

    /**
     * Setter para o ID da morada.
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
     * Setter para a rua.
     * 
     * @param string $street Nome da rua.
     */
    public function setStreet($street) {
        $this->street = $street;
    }

    /**
     * Setter para o número da porta.
     * 
     * @param string $doorNum Número da porta.
     */
    public function setDoorNum($doorNum) {
        $this->doorNum = $doorNum;
    }

    /**
     * Setter para o andar.
     * 
     * @param string $floor Andar.
     */
    public function setFloor($floor) {
        $this->floor = $floor;
    }

    /**
     * Setter para informações extra.
     * 
     * @param string $extra Informações adicionais.
     */
    public function setExtra($extra) {
        $this->extra = $extra;
    }

    /**
     * Setter para o distrito.
     * 
     * @param string $district Distrito.
     */
    public function setDistrict($district) {
        $this->district = $district;
    }

    /**
     * Setter para o município.
     * 
     * @param string $municipality Município.
     */
    public function setMunicipality($municipality) {
        $this->municipality = $municipality;
    }

    /**
     * Setter para o código postal.
     * 
     * @param string $zipCode Código postal.
     */
    public function setZipCode($zipCode) {
        $this->zipCode = $zipCode;
    }

    /**********
     Functions
    **********/

    /**
     * Guarda ou atualiza a morada na base de dados.
     * 
     * @return bool Verdadeiro se a operação for bem-sucedida.
     */
    public function save() {
        // Validar campos obrigatórios
        if (!$this->userId || !$this->street || !$this->doorNum || 
            !$this->district || !$this->municipality || !$this->zipCode) {
            return false;
        }

        try {
            if ($this->id) {
                // Atualizar morada existente
                $stmt = $this->db->prepare("
                    UPDATE Address_ 
                    SET street = :street, 
                        door_num = :door_num, 
                        floor_ = :floor, 
                        extra = :extra, 
                        district = :district, 
                        municipality = :municipality, 
                        zip_code = :zip_code 
                    WHERE id = :id
                ");
                
                return $stmt->execute([
                    ':street' => $this->street,
                    ':door_num' => $this->doorNum,
                    ':floor' => $this->floor,
                    ':extra' => $this->extra,
                    ':district' => $this->district,
                    ':municipality' => $this->municipality,
                    ':zip_code' => $this->zipCode,
                    ':id' => $this->id
                ]);
            } else {
                // Inserir nova morada
                $stmt = $this->db->prepare("
                    INSERT INTO Address_ (user_id, street, door_num, floor_, extra, district, municipality, zip_code) 
                    VALUES (:user_id, :street, :door_num, :floor, :extra, :district, :municipality, :zip_code)
                ");
                
                $result = $stmt->execute([
                    ':user_id' => $this->userId,
                    ':street' => $this->street,
                    ':door_num' => $this->doorNum,
                    ':floor' => $this->floor,
                    ':extra' => $this->extra,
                    ':district' => $this->district,
                    ':municipality' => $this->municipality,
                    ':zip_code' => $this->zipCode
                ]);

                if ($result) {
                    $this->id = $this->db->lastInsertId();
                    return true;
                }
                return false;
            }
        } catch (PDOException $e) {
            error_log("Erro ao guardar morada: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove a morada da base de dados.
     * 
     * @return bool Verdadeiro se a operação for bem-sucedida.
     */
    public function delete() {
        if (!$this->id) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM Address_ WHERE id = :id");
            return $stmt->execute([':id' => $this->id]);
        } catch (PDOException $e) {
            error_log("Erro ao eliminar morada: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Procura uma morada pelo ID do utilizador.
     * 
     * @param PDO $db Instância da base de dados.
     * @param int $userId ID do utilizador.
     * @return Address|null Instância da morada ou null se não encontrada.
     */
    public static function findByUserId($db, $userId) {
        try {
            $stmt = $db->prepare("SELECT * FROM Address_ WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return self::createFromArray($db, $result);
            }
            return null;
        } catch (PDOException $e) {
            error_log("Erro ao procurar morada por ID do utilizador: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Procura uma morada pelo ID.
     * 
     * @param PDO $db Instância da base de dados.
     * @param int $id ID da morada.
     * @return Address|null Instância da morada ou null se não encontrada.
     */
    public static function findById($db, $id) {
        try {
            $stmt = $db->prepare("SELECT * FROM Address_ WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return self::createFromArray($db, $result);
            }
            return null;
        } catch (PDOException $e) {
            error_log("Erro ao procurar morada por ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtém todas as moradas de um utilizador.
     * 
     * @param PDO $db Instância da base de dados.
     * @param int $userId ID do utilizador.
     * @return array Lista de moradas do utilizador.
     */
    public static function getAllByUserId($db, $userId) {
        try {
            $stmt = $db->prepare("SELECT * FROM Address_ WHERE user_id = :user_id ORDER BY id ASC");
            $stmt->execute([':user_id' => $userId]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $addresses = [];
            foreach ($results as $result) {
                $addresses[] = self::createFromArray($db, $result);
            }
            return $addresses;
        } catch (PDOException $e) {
            error_log("Erro ao obter moradas do utilizador: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Cria uma instância de Address a partir de um array de dados.
     * 
     * @param PDO $db Instância da base de dados.
     * @param array $array Dados da morada.
     * @return Address Instância da classe preenchida.
     */
    private static function createFromArray($db, $array) {
        $address = new self($db);
        $address->setId($array['id']);
        $address->setUserId($array['user_id']);
        $address->setStreet($array['street']);
        $address->setDoorNum($array['door_num']);
        $address->setFloor($array['floor_']);
        $address->setExtra($array['extra']);
        $address->setDistrict($array['district']);
        $address->setMunicipality($array['municipality']);
        $address->setZipCode($array['zip_code']);
        return $address;
    }

    /**
     * Converte o objeto Address num array associativo.
     * 
     * @return array Representação da morada em array.
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'street' => $this->street,
            'door_num' => $this->doorNum,
            'floor_' => $this->floor,
            'extra' => $this->extra,
            'district' => $this->district,
            'municipality' => $this->municipality,
            'zip_code' => $this->zipCode
        ];
    }

    /**
     * Formata a morada completa numa string legível.
     * 
     * @return string Morada formatada.
     */
    public function getFormattedAddress() {
        $parts = [];
        
        // Rua e número
        $parts[] = $this->street . ', ' . $this->doorNum;
        
        // Andar (se existir)
        if (!empty($this->floor)) {
            $parts[] = $this->floor . 'º andar';
        }
        
        // Informações extra (se existirem)
        if (!empty($this->extra)) {
            $parts[] = $this->extra;
        }
        
        // Código postal, município e distrito
        $parts[] = $this->zipCode . ' ' . $this->municipality . ', ' . $this->district;
        
        return implode(', ', $parts);
    }

    /**
     * Valida o código postal português.
     * 
     * @param string $zipCode Código postal a validar.
     * @return bool True se válido, false caso contrário.
     */
    public static function validateZipCode($zipCode) {
        // Formato português: XXXX-XXX
        return preg_match('/^\d{4}-\d{3}$/', $zipCode);
    }

    /**
     * Formata o código postal no formato português.
     * 
     * @param string $zipCode Código postal a formatar.
     * @return string Código postal formatado.
     */
    public static function formatZipCode($zipCode) {
        // Remove todos os caracteres não numéricos
        $numbers = preg_replace('/\D/', '', $zipCode);
        
        // Se tiver 7 dígitos, formatar como XXXX-XXX
        if (strlen($numbers) === 7) {
            return substr($numbers, 0, 4) . '-' . substr($numbers, 4, 3);
        }
        
        return $zipCode; // Retorna original se não for possível formatar
    }
}
?>