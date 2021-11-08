<?php

/**
 * Class AuthToken
 * Contient le Token de l'utilisateur, il se créer à sa première connexion.
 * Il contient l'id, le token généré aléatoirement, la date de création, l'ip de l'utilisateur ainsi que son id.
 */
class AuthToken extends FunctionManager
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $token;
    /**
     * @var string
     */
    public $startDate;
    /**
     * @var string
     */
    public $ip;
    /**
     * @var string
     */
    public $uid;

    /**
     * AuthToken constructor.
     * @param int $id
     * @param string $token
     * @param string $ip
     * @param string $startDate
     * @param string $uid
     */

    public function __construct(int $id, string $token, string $ip, string $startDate, string $uid)
    {
        $this->id = $id;
        $this->token = $token;
        $this->startDate = $startDate;
        $this->ip = $ip;
        $this->uid = $uid;
    }

    /**
     * cette fonction créer un nouveau token et passe ensuite un test
     * @param $uid
     * @return array|string retourne soit un tableau qui correspond au token ,soit l'ip de l'utilisateur stocké dans le token qui est lui même stocké dans le fichier json
     * @throws Exception
     */
    public static function getToken($uid)
    {
        $id = 0;
        $data[] = new AuthToken($id, base64_encode(random_bytes(20)), $_SERVER['REMOTE_ADDR'], time(), $uid);

        if (is_string(self::checkToken($data))){
            return self::checkToken($data);
        }
        return $data;
    }

    /**
     * cette fonction permet de verifier les tokens
     * @param $data
     * @return TRUE|string return True si le token que l'on reçoit n'a pas le meme id d'utilisateur que les token qui se trouve dans la base de données ou un string dans le cas contraire.
     */
    public static function checkToken($data): bool
    {
        $confirmation = AuthManager::load('db/session.json');
        for ($i = 0; $i < count($confirmation); $i++) {
            if ($confirmation[$i]->uid === $data[0]->uid) {
                return $data[0]->ip;
            }
        }
        return TRUE;
    }

    /**
     * @param $obj
     * @return AuthToken|false
     */
    public static function fromJson($obj)
    {
        $vars = get_object_vars($obj);
        if (!array_key_exists('id', $vars) ||
            !array_key_exists('token', $vars) ||
            !array_key_exists('startDate', $vars) ||
            !array_key_exists('ip', $vars) ||
            !array_key_exists('uid', $vars)) {
            return FALSE;
        }
        return new AuthToken($obj->id, $obj->token, $obj->ip, $obj->startDate, $obj->uid);
    }


}
