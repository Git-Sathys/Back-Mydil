<?php

/**
 * Class Product
 * Contient l'objet Produit, contient l'id du produit, son nom, son image, son stock et sa catégorie.
 */
class Product extends FunctionManager {

    public $stock, $id, $name, $img, $category;

    /**
     * Product constructor.
     * @param int $id
     * @param string $name
     * @param string $img
     * @param int $stock
     * @param string $category
     */
    public function __construct(int $id, string $name, string $img, int $stock, string $category)
    {
        $this->id = $id;
        $this->name = $name;
        $this->img = $img;
        $this->stock = $stock;
        $this->category = $category;
    }

    /**
     * Renvoie l'objet Product si les paramètres sont respéctés
     * @return false|Product
     */
    public static function fromJson($obj)
    {
        $vars = get_object_vars($obj);
        if(!array_key_exists('id', $vars) ||
            !array_key_exists('name', $vars) ||
            !array_key_exists('img', $vars) ||
            !array_key_exists('stock', $vars) ||
            !array_key_exists('category', $vars)){
            return FALSE;
        }
        return new Product($obj->id, $obj->name, $obj->img, $obj->stock, $obj->category);
    }

    /**
     * Charge la base de donnée et créer un tableau d'objets Product
     * @param $JSON_FILE
     * @return array Product
     */
    public static function load($JSON_FILE)
    {
        $json = self::loadDatabase($JSON_FILE);
        $products = [];
        for ($i=0; $i < sizeof($json); $i++) {
            $products[] = self::fromJson($json[$i]);
        }
        return $products;
    }

    /**
     * Sauvegarde la base de données, on peut passer un objet ou plusieurs, seuls ceux différents de la base de données seront modifiés.
     * La variable $content doit être du Json, elle doit impérativement contenir une clé 'id' ainsi que les 4 autres paramètres de l'objet Product.
     * Il n'y aura aucun ajout ni suppression.
     * @param string $content
     * @param string $JSON_FILE
     * @return bool
     */
    public static function save(string $content, string $JSON_FILE)
    {
        if(self::saveDatabase($content, $JSON_FILE) === FALSE) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Ajoute un et un seul objet dans la base de données, si plusieurs objets sont passés, seul le premier sera ajouté.
     * La variable $content doit être du Json, elle doit impérativement contenir une clé 'id' ainsi que les 4 autres paramètres de l'objet Product.
     * Peu importe l'id passé, la fonction se charge de lui accorder un id inexistant.
     * @param string $content
     * @param string $JSON_FILE
     * @return bool
     */
    public static function put(string $content, string $JSON_FILE)
    {
        if(self::putDatabase($content, $JSON_FILE) === FALSE) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Supprime un et un seul objet de la base de données.
     * La variable $content doit être du Json, elle doit impérativement contenir une clé 'id'. Le reste n'est pas nécessaire, mais ne pose aucun soucis de fonctionnement.
     * @param string $content
     * @param string $JSON_FILE
     * @return bool
     */
    public static function delete(string $content, string $JSON_FILE)
    {
        if(self::deleteDatabaseElement($content, $JSON_FILE) === FALSE) {
            return FALSE;
        }
        return TRUE;
    }

}
