<?php

namespace kernel;

/**
 * Class Autoloader
 * @package kernel
 */
class Autoloader
{
    const CLASS_EXTENTION = '.class.php';
    private $classDirectory;
    private static $instance;

    private function __construct()
    {
        $this->classDirectory = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR;
    }

    private static function getInstance()
    {
        if (self::$instance == NULL){
            self::$instance= new Autoloader();
        }
        return self::$instance;
    }

    public static function autoload($class)
    {
        if (self::autoloadNamespace($class)) {
            return TRUE;
        }
        if (self::autoloadPasNamespace($class, self::getInstance()->classDirectory)){
            return TRUE;
        }
        echo('Unable to load ' . $class);
        exit(1);
    }

    public static function autoloadNamespace(string $class): bool
    {
        if (strpos($class, '\\') === FALSE) {
            return FALSE;
        }
        $pieces = explode('\\', $class);
        $filename = implode(DIRECTORY_SEPARATOR, $pieces);
        $filename .= self::CLASS_EXTENTION;
        if (!is_file($filename)) {
            return FALSE;
        }
        include $filename;
        return TRUE;
    }

    public static function autoloadPasNamespace(string $class, string $root)
    {
        $folders = scandir($root);
        array_splice($folders, 0, 2);
        foreach ($folders as $folder) {
            if(substr($folder, 0) == '.'){
                continue;
            }
            $filename = $root . DIRECTORY_SEPARATOR . $folder;
            $classfile = $class . self::CLASS_EXTENTION;
            if ($folder == $classfile) {
                include $filename;
                return TRUE;
            }
            if (is_dir($filename)) {
                if (self::autoloadPasNamespace($class, $filename)) {
                    return TRUE;
                }
            }
        }
        return FALSE;
    }
}

spl_autoload_register('kernel\Autoloader::autoload');
