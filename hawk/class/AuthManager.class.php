<?php

/**
 * Class Fonction de l'objet AuthToken
 * Class AuthManager
 */
class AuthManager extends FunctionManager
{

    /**
     * @var string
     */
    public $login;
    /**
     * @var string
     */
    public $password;

    /**
     * AuthManager constructor.
     * @param string $login
     * @param string $password
     */
    public function __construct(string $login, string $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    /**
     * converti les valeur de l'url en json
     * @return false|array retourne un tableau d'object  ou FALSE si les test ont error_loguée
     */
    public static function QueryToJson()
    {

        parse_str($_SERVER['QUERY_STRING'], $auth);
        if (empty($auth['login'])) {
            error_log('il manque un login');
            return FALSE;
        }
        if (empty($auth['password'])) {
            error_log('il manque un password');
            return FALSE;
        }
        $object[] = new AuthManager($auth['login'], $auth['password']);
        if (empty($object)) {
            error_log ('les valeur sont vides ');
            return FALSE;
        }
        return $object;
    }

    /**
     * Cette fonction connecte l'utilisateur en créant un token qui lui sera propre
     * @return false|true return false si les test ont échoué ou retourne le resultat de la fonction qui peut etre TRUE si le token va être ajouter dans le fichier json ou FALSE
     * @throws Exception
     */
    public static function auth() : bool
    {
        $user = self::check();
        if (is_bool($user)) {
            error_log('erreur de connection');
            return FALSE;
        }
        $token = AuthToken::getToken($user[0]->id);
        if (is_string($token)) {
            error_log('vous etes déja connecter sur un autre appareil ' . $token);
            return FALSE;
        }
        return self::put(json_encode($token), 'db/session.json');

    }

    /**
     * cette fonction deconnecte l'utilisateur en suppriment son token
     * @return false|true return false si les test ont échoué ou retourne le resultat de la fonction qui peut etre TRUE si le token va être supprimer dans le fichier json ou FALSE
     */
    public static function disconnection(): bool
    {
        $user = self::check();
        if (is_bool($user)) {
            error_log("erreur dans la recherche de l'utilisateur'");
            return FALSE;
        }
        return self::delete($user, 'db/session.json');
    }

    /**
     * Cette foncton permet dans verifier dans la base de données si il existe ou pas
     * @return array|false un tableau d'object d'un utilisateur  ou Fasle
     */
    public static function check()
    {
        $content = json_encode(self::QuerytoJson());
        $test = self::loadDatabase('db/user.json');
        if (empty($test)) {
            error_log('erreur dans la base de donnée');
            return FALSE;
        }
        $object = json_decode($content);
        $login = $object[0]->login;
        if (empty($login)) {
            error_log("le login est vide");
            return FALSE;
        }
        $password = $object[0]->password;
        if (empty($password)) {
            error_log("le password est vide");
            return FALSE;
        }
        for ($i = 0; $i < count($test); $i++) {
            if ($login === $test[$i]->login && $password === $test[$i]->password) {
                $confirmation[] = $test[$i];
                return $confirmation;

            }
        }
        return FALSE;
    }

    /**
     * Charge la base de donnée et créer un tableau d'objets AuthToken
     * @param $JSON_FILE
     * @return array AuthToken
     */
    public static function load($JSON_FILE)
    {
        $json = self::loadDatabase($JSON_FILE);
        $tokens = [];
        for ($i = 0; $i < sizeof($json); $i++) {
            $tokens[] = AuthToken::fromJson($json[$i]);
        }
        return $tokens;
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
        if (self::deleteDatabaseElement($content, $JSON_FILE) === FALSE) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Ajoute un et un seul objet dans la base de données, si plusieurs objets sont passés, seul le premier sera ajouté.
     * La variable $content doit être du Json, elle doit impérativement contenir une clé 'id' ainsi que les 4 autres paramètres de l'objet AuthToken.
     * Peu importe l'id passé, la fonction se charge de lui accorder un id inexistant.
     * @param string $content
     * @param string $JSON_FILE
     * @return bool
     */
    public static function put(string $content, string $JSON_FILE)
    {
        if (self::putDatabase($content, $JSON_FILE) === FALSE) {
            return FALSE;
        }
        return TRUE;
    }
}
