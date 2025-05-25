<?php
require_once(dirname(__FILE__) . '/../Database/connection.php');

/**
 * Classe responsável por representar e gerir categorias disponíveis na plataforma.
 * Cada categoria pode estar associada a uma imagem (media) e possui um identificador e nome.
 */
class Category {
    private $id;
    private $name;
    private $photo_id;
    private $photo_url;

    /**
     * Construtor da classe.
     * 
     * @param int $id ID da categoria.
     * @param string $name Nome da categoria.
     * @param int $photo_id ID da imagem associada.
     * @param string|null $photo_url Caminho do ficheiro da imagem (opcional).
     */
    public function __construct($id, $name, $photo_id, $photo_url = null) {
        $this->id = $id;
        $this->name = $name;
        $this->photo_id = $photo_id;
        $this->photo_url = $photo_url;
    }

    /********
     Getters
    ********/

    /**
     * Getter para o ID da categoria.
     * 
     * @return int ID da categoria.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Getter para o nome da categoria.
     * 
     * @return string Nome da categoria.
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Getter para o ID da imagem associada.
     * 
     * @return int ID da imagem.
     */
    public function getPhotoId() {
        return $this->photo_id;
    }

    /**
     * Getter para o caminho da imagem associada.
     * 
     * @return string|null Caminho da imagem ou null se não definido.
     */
    public function getPhotoUrl() {
        return $this->photo_url;
    }

    /********
     Setters
    ********/

    /**
     * Setter para o caminho da imagem associada.
     * 
     * @param string $url Novo caminho da imagem.
     */
    public function setPhotoUrl($url) {
        $this->photo_url = $url;
    }

    /**********
     Functions
    **********/

    /**
     * Obtém todas as categorias da base de dados.
     * 
     * @return array Lista de objetos Category.
     */
    public static function getAllCategories() {
        $db = getDatabaseConnection();

        // Prepara e executa o statement de seleção
        $stmt = $db->prepare("
            SELECT c.id, c.name_, c.photo_id, m.path_ as file_path
            FROM Category c
            LEFT JOIN Media m ON c.photo_id = m.id
        ");
        $stmt->execute();

        // Para cada linha retornada, cria a instância de Category, adiciona ao array $categories.
        $categories = [];
        while ($category = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = new Category(
                $category['id'],
                $category['name_'],
                $category['photo_id'],
                $category['file_path']
            );
        }

        return $categories;
    }

    /**
     * Procura uma categoria pelo seu ID.
     * 
     * @param int $id ID da categoria.
     * @return Category|null Objeto Category ou null se não encontrada.
     */
    public static function getCategoryById($id) {
        $db = getDatabaseConnection();

        // Prepara e executa o statement de seleção
        $stmt = $db->prepare("
            SELECT c.id, c.name_, c.photo_id, m.path_ as file_path
            FROM Category c
            LEFT JOIN Media m ON c.photo_id = m.id
            WHERE c.id = :id
        ");
        $stmt->execute([':id' => $id]);

        // Cria a instância, retornada pelo Statement.
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($category) {
            return new Category(
                $category['id'],
                $category['name_'],
                $category['photo_id'],
                $category['file_path']
            );
        }

        return null;
    }

    /**
     * Converte o objeto Category num array associativo (para uso em APIs, JSON, etc.).
     * 
     * @return array Representação da categoria em array.
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'photo_id' => $this->photo_id,
            'photo_url' => $this->photo_url
        ];
    }
}
?>
