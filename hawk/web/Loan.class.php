<?php

/**
 * Class Loan
 * Contient l'objet réservation, contient l'id de la réservation, de l'objet, de l'utilisateur ainsi que le timestamp de début et de fin de réservation.
 */
class Loan extends FunctionManager {

    public $id, $uid, $pid, $dateStart, $dateEnd;

    /**
     * Loan constructor.
     * @param int $id
     * @param int $uid
     * @param int $pid
     * @param int $dateStart
     * @param int $dateEnd
     */
    public function __construct(int $id, int $uid, int $pid, int $dateStart, int $dateEnd)
    {
        $this->id = $id;
        $this->uid = $uid;
        $this->pid = $pid;
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
    }

    /**
     * Renvoie l'objet Loan si les paramètres sont respéctés
     * @param $obj
     * @return false|Loan
     */
    public static function fromJson($obj)
    {
        $vars = get_object_vars($obj);
        if(!array_key_exists('id', $vars) ||
            !array_key_exists('uid', $vars) ||
            !array_key_exists('pid', $vars) ||
            !array_key_exists('dateStart', $vars) ||
            !array_key_exists('dateEnd', $vars)){
            return FALSE;
        }
        return new Loan($obj->id, $obj->uid, $obj->pid, $obj->dateStart, $obj->dateEnd);
    }

    /**
     * Charge la base de donnée et créer un tableau d'objets Product
     * @param $JSON_FILE
     * @return array Loan
     */
    public static function load($JSON_FILE)
    {
        $json = self::loadDatabase($JSON_FILE);
        //return $json;
        $loan = [];
        for ($i=0; $i < sizeof($json); $i++) {
            $loan[] = self::fromJson($json[$i]);
        }
        return $loan;
    }

    /**
     * Sauvegarde la base de données, on peut passer un objet ou plusieurs, seuls ceux différents de la base de données seront modifiés.
     * La variable $content doit être du Json, elle doit impérativement contenir une clé 'id' ainsi que les 4 autres paramètres de l'objet Loan.
     * Il n'y aura aucun ajout ni suppression.
     * @param string $content
     * @param string $JSON_FILE
     * @return bool
     */
    public static function save(string $content, string $JSON_FILE)
    {
        if(self::saveDatabase($content, $JSON_FILE) === FALSE) {
            error_log(self::class);
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Ajoute un et un seul objet dans la base de données, si plusieurs objets sont passés, seul le premier sera ajouté.
     * La variable $content doit être du Json, elle doit impérativement contenir une clé 'id' ainsi que les 4 autres paramètres de l'objet Loan.
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
