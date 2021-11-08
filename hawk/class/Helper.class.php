<?php

class Helper
{
    public static function postVar($name)
    {
        return self::retrieveVar($name, $_POST);
    }

    public static function filesVar($name)
    {
        return self::retrieveVar($name, $_FILES);
    }

    public static function sessionVar($name)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        return self::retrieveVar($name, $_SESSION);
    }

    public static function retrieveVar($name, $tab)
    {
        //TODO PAS ISSET
        if (isset($tab[$name])) {
            if (!empty($tab[$name])) {
                return $tab[$name];
            }
            return TRUE;
        }
        return FALSE;
    }

    public static function serverVar($name)
    {
        return self::retrieveVar($name, $_SERVER);
    }

    public static function serverVarType($name, $type)
    {
        var_dump($_SERVER[$type]);
        return self::retrieveVar($name, $_SERVER[$type]);
    }
}

