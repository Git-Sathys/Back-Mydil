<?php

/**
 * Class User
 * Contient l'objet Utilisateur, contient l'id, le nom de compte, le mot de passe, le nom, le prénom, l'adresse mail.
 */
class User extends FunctionManager {

    public $id, $login, $password, $name, $lastname, $mail;

    /**
     * User constructor.
     * @param int $id
     * @param string $login
     * @param string $password
     * @param string $name
     * @param string $lastname
     * @param string $mail
     */
    public function __construct(int $id, string $login, string $password, string $name, string $lastname, string $mail){
        $this->id = $id;
        $this->login = $login;
        $this->password = $password;
        $this->name = $name;
        $this->lastname = $lastname;
        $this->mail = $mail;
    }

    /**
     * Renvoie l'objet User si les paramètres sont respéctés
     * @param $obj
     * @return false|User
     */
    public static function fromJson($obj){
        $vars = get_object_vars($obj);
        if(
            !array_key_exists('id', $vars) ||
            !array_key_exists('login', $vars) ||
            !array_key_exists('password', $vars) ||
            !array_key_exists('name', $vars) ||
            !array_key_exists('lastname', $vars) ||
            !array_key_exists('mail', $vars))
        {
            return FALSE;
        }
        return new User($obj->id, $obj->login, $obj->password, $obj->name, $obj->lastname, $obj->mail);
    }

    /**
     * Charge la base de donnée et créer un tableau d'objets User
     * @param $JSON_FILE
     * @return array User
     */
    public static function load($JSON_FILE)
    {
        $json = self::loadDatabase($JSON_FILE);
        //return $json;
        $users = [];
        for ($i=0; $i < sizeof($json); $i++) {
            $users[] = self::fromJson($json[$i]);
        }
        return $users;
    }

    /**
     * Sauvegarde la base de données, on peut passer un objet ou plusieurs, seuls ceux différents de la base de données seront modifiés.
     * La variable $content doit être du Json, elle doit impérativement contenir une clé 'id' ainsi que les 5 autres paramètres de l'objet User.
     * Il n'y aura aucun ajout ni suppression.
     * @param string $content
     * @param string $JSON_FILE
     * @return bool
     */
    public static function save(string $content, string $JSON_FILE){
        if(self::saveDatabase($content,$JSON_FILE) === FALSE) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Ajoute un et un seul objet dans la base de données, si plusieurs objets sont passés, seul le premier sera ajouté.
     * La variable $content doit être du Json, elle doit impérativement contenir une clé 'id' ainsi que les 5 autres paramètres de l'objet User.
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
