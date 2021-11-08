<?php

/**
 * Class Api
 * C'est une classe générique qui permet de récupérer une requête et exécuter la bonne fonction en fonction de celle-ci.
 * Elle s'utilise en appellant la fonction execute, avec le nom de la classe et l'emplacement du fichier Json.
 * Les fichiers Json sont par défaut stockés dans le dossier db.
 */
class Api
{
    /**
     * Appelle la fonction correspondante à la méthode reçue.
     * @param string $className
     * @param string $jsonFile
     */
    public static function execute(string $className, string $jsonFile)
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        $toCall = self::class . '::' . $method;
        $data = [];
        if ($method == 'post' || $method == 'put' || $method == 'delete') {
            $data = file_get_contents('php://input');
        }
        if (!empty($data)) {
            if (is_callable($toCall)) {
                call_user_func($toCall, $data, $className, $jsonFile);
                die();
            }
        } else {
            if (is_callable($toCall)) {
                call_user_func($toCall, $className, $jsonFile);
                die();
            }
        }
        http_response_code(400);
        die();
    }

    /**
     * Cette fonction est appellée automatiquement par la fonction execute.
     * Renvoie la base de donnée si les données ont bien été récupérées.
     * @param string $className
     * @param string $jsonFile
     */
    private static function get(string $className, string $jsonFile)
    {
        $data = FALSE;
        try {
            $call = $className. '::' . 'load';
            if (is_callable($call)) {
                $data = call_user_func($call, $jsonFile);
            }
        } catch (Exception $e) {
            error_log($e);
        }

        if($data === FALSE){
            error_log('No data in : ' . $jsonFile);
        }else{
            echo(json_encode($data));
        }
    }

    /**
     * Cette fonction est appellée automatiquement par la fonction execute.
     * @param $content
     * @param string $className
     * @param string $jsonFile
     * @return bool
     */
    private static function post($content, string $className, string $jsonFile)
    {
        $call = $className. '::' . 'save';
        if (is_callable($call)) {
            if (call_user_func($call, $content, $jsonFile) === FALSE)
            {
                error_log('Sauvegarde echouee');
                return FALSE;
            }
        }
        error_log('Sauvegarde reussie');
        return TRUE;
    }

    /**
     * Cette fonction est appellée automatiquement par la fonction execute.
     * @param $content
     * @param string $className
     * @param string $jsonFile
     * @return bool
     */
    private static function put($content, string $className, string $jsonFile)
    {
        $call = $className. '::' . 'put';
        if (is_callable($call)) {
            if (call_user_func($call, $content, $jsonFile) === FALSE)
            {
                error_log('Put echouee');
                return FALSE;
            }
        }
        error_log('Put reussi');
        return TRUE;
    }

    /**
     * Cette fonction est appellée automatiquement par la fonction execute.
     * @param $content
     * @param string $className
     * @param string $jsonFile
     * @return bool
     */
    private static function delete($content, string $className, string $jsonFile)
    {
        $call = $className. '::' . 'delete';
        if (is_callable($call)) {
            if (call_user_func($call, $content, $jsonFile) === FALSE)
            {
                error_log('Delete echouee');
                return FALSE;
            }
        }
        error_log('Delete reussi');
        return TRUE;
    }
}
