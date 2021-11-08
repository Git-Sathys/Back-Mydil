<?php

/**
 * Class FunctionManager
 * Dans cette classe, tous les $content doivent impérativement être en format Json car ils seront décodés à l'intérieur des fonctions.
 */
class FunctionManager
{
    /**
     * Renvoie la base de donnée en format tableau Php
     * @param $jsonFile
     * @return false|mixed
     */
    public static function loadDatabase($jsonFile)
    {
        if (!is_file($jsonFile)) {
            error_log('Le fichier : '. $jsonFile . ' n\'existe pas');
            return FALSE;
        }
        $content = file_get_contents($jsonFile);
        if ($content === FALSE) {
            error_log('Probleme de droits');
            return FALSE;
        }
        $json = json_decode($content);
        if (!$json) {
            error_log('Pas du json');
            return FALSE;
        }
        return $json;
    }

    /**
     * Renvoie vrai ou faux en fonction du résultat de la sauvegarde.
     * Sauvegarde la base de données, on peut passer un objet ou plusieurs, seuls ceux différents de la base de données seront modifiés.
     * La variable $content doit être du Json, elle doit impérativement contenir une clé 'id' ainsi que TOUS les autres paramètres de l'objet.
     * Il n'y aura aucun ajout ni suppression.
     * @param string $content
     * @param string $jsonFile
     * @return bool
     */
    public static function saveDatabase(string $content, string $jsonFile)
    {
        if (AuthManager::check() === FALSE){
            return FALSE;
        }
        self::tests($content, $jsonFile);
        $database = self::loadDatabase($jsonFile);
        $object = json_decode($content);
        if (!self::isArray($object) || !self::isArray($database)){
            return FALSE;
        }
        $updatedDatabase = self::checkDatabase($database, $object);
        if (!$updatedDatabase) {
            error_log('Can\'t check database updatedDatabase');
            return FALSE;
        }
        if (file_put_contents($jsonFile, json_encode($updatedDatabase)) === FALSE) {
            error_log('Probleme de sauvegarde inconnu');
            return FALSE;
        }
        return TRUE;
    }

    /**
     *Ajoute un et un seul objet dans la base de données, si plusieurs objets sont passés, seul le premier sera ajouté.
     * La variable $content doit être du Json, elle doit impérativement contenir une clé 'id' ainsi que les TOUS les autres paramètres de l'objet.
     * Peu importe l'id passé, la fonction se charge de lui accorder un id inexistant.
     * @param string $content
     * @param string $jsonFile
     * @return bool
     */
    public static function putDatabase(string $content, string $jsonFile)
    {
        if (AuthManager::check() === FALSE){
            return FALSE;
        }
        self::tests($content, $jsonFile);
        $database = self::loadDatabase($jsonFile);
        $object = json_decode($content);
        if (!self::isArray($object) || !self::isArray($database)){
            return FALSE;
        }
        $updatedDatabase = self::addDatabase($database, $object);
        if ($updatedDatabase === FALSE) {
            return FALSE;
        }
        if (file_put_contents($jsonFile, json_encode($updatedDatabase)) === FALSE) {
            error_log('Probleme de sauvegarde inconnu');
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Supprime un et un seul objet de la base de données.
     * La variable $content doit être du Json, elle doit impérativement contenir une clé 'id'.
     * Le reste n'est pas nécessaire, mais ne pose aucun problème, les autres clés seront ignorées.
     * @param string $content
     * @param string $jsonFile
     * @return bool
     */
    public static function deleteDatabaseElement(string $content, string $jsonFile)
    {
        if (AuthManager::check() === FALSE){
            return FALSE;
        }
        self::tests($content, $jsonFile);
        $database = self::loadDatabase($jsonFile);
        $object = json_decode($content);
        if (!self::isArray($object) || !self::isArray($database)){
            return FALSE;
        }
        $updatedDatabase = self::deleteDatabase($database, $object);
        if ($updatedDatabase === FALSE) {
            return FALSE;
        }
        if (file_put_contents($jsonFile, json_encode($updatedDatabase)) === FALSE) {
            error_log('Probleme de sauvegarde inconnu');
            return FALSE;
        }
        return TRUE;
    }

    /**
     * TODO
     * Actuellement Fonction de test, elle n'est pas utilisée.
     * @param string $loanFile
     * @param string $productFile
     * @param string $userFile
     * @return false
     */
    public static function getUserProductByLoan(string $loanFile, string $productFile, string $userFile)
    {
        $loanDatabase = self::loadDatabase($loanFile);
        $productDatabase = self::loadDatabase($productFile);
        $userDatabase = self::loadDatabase($userFile);
        var_dump($loanDatabase);
        $product = 'vide';
        $user = 'vide';

        for ($i = 0; $i < count($productDatabase); $i++) {
            if ($loanDatabase[0]->{'pid'} == $productDatabase[$i]->{'id'}) {
                $product = $productDatabase[$i];
            }
        }

        for ($i = 0; $i < count($userDatabase); $i++) {
            if ($loanDatabase[0]->{'uid'} == $userDatabase[$i]->{'id'}) {
                $user = $userDatabase[$i];
            }
        }
        if ($product === 'vide' || $user === 'vide') {
            return FALSE;
        }
        var_dump("l'objet : " . $product->{'name'} . " a été emprinté par " . $user->{'login'});
    }

    /**
     * Fonction retournant la base de donnée modifiée avec les nouveaux éléments
     * @param $database
     * @param $object
     * @return false|array
     */
    private static function checkDatabase($database, $object)
    {
        if (!self::isArray($object) || !self::isArray($database)){
            return FALSE;
        }
        for ($i = 0; $i < count($database); $i++) {
            for ($x = 0; $x < count($object); $x++) {
                if ($object[$x]->{'id'} == $database[$i]->{'id'}) {
                    if ($object[$x] == $database[$i]) {
                        error_log("Pas de modifications a faire");
                    } else {
                        $database[$i] = $object[$x];
                        error_log("Modification effectuee");
                    }
                }
            }
        }
        return $database;
    }

    /**
     * Fonction retournant la base de données en y ajoutant un object à la fin de celle-ci.
     * L'objet ajouté aura comme id, le plus grand id existant + 1.
     * @param $database
     * @param $object
     * @return false|array
     */
    private static function addDatabase($database, $object)
    {
        if (!self::isArray($object) || !self::isArray($database)){
            return FALSE;
        }

        $lastId = 0;

        for ($i = 0; $i < count($database); $i++) {
            if ($lastId < $database[$i]->{'id'}) {
                $lastId = $database[$i]->{'id'};
            }
        }

        $object[0]->{'id'} = $lastId + 1;
        array_push($database, $object[0]);

        return $database;
    }

    /**
     * Fonction retournant la base de données sans l'objet passé en paramètre.
     * Les tests sont effectués en fonction de l'id passé. La fonction fonctionne aussi bien avec un simple 'id' dans du Json, qu'un objet entier.
     * @param $database
     * @param $object
     * @return false|array
     */
    private static function deleteDatabase($database, $object)
    {
        if (!self::isArray($object) || !self::isArray($database)){
            return FALSE;
        }
        $productExist = FALSE;

        for ($i = 0; $i < count($database); $i++) {
            if ($object[0]->{'id'} == $database[$i]->{'id'}) {
                array_splice($database, $i, 1);
                $productExist = TRUE;
            }
        }

        if ($productExist === FALSE){
            error_log("Pas de produit a supprimer avec cet ID");
            return FALSE;
        }
        return $database;
    }

    public static function isArray($object)
    {
        if (!is_array($object)) {
            error_log('Pas un objet valide');
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Vérifie si les paramètres passés sont corrects, cette fonction permet d'alléger le code. Elle retourne false si un des paramètre est incorrect, sinon elle ne retourne rien.
     * @param $content
     * @param string $jsonFile
     * @return false
     */
    private static function tests($content, string $jsonFile)
    {
        if (!is_file($jsonFile)) {
            error_log('Le fichier : '. $jsonFile . ' n\'existe pas');
            return FALSE;
        }
        if ($content === FALSE) {
            error_log('Probleme de droits');
            return FALSE;
        }
    }
}
