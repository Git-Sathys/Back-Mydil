<?php


class ModuleManager
{

    private static $instance;
    private $modules = [];
    private $moduleDirectory;

    const MODULE_CONFIG_FILE = 'module.json';
    const MODULE_CLASS_PREFIX = 'Mod';

    private static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new ModuleManager();
            self::$instance->moduleDirectory = pathinfo(__DIR__)['dirname'];
        }
        return self::$instance;
    }

    public static function getModules() : array
    {
        return array_values(self::getInstance()->modules);
    }

    public static function load()
    {
        $folders = self::getModuleDirectories();
        foreach ($folders as $folder) {
            $configFile = $folder . DIRECTORY_SEPARATOR . self::MODULE_CONFIG_FILE;
            if (!is_file($configFile)) {
                continue;
            }
            $content = file_get_contents($configFile);
            if ($content === FALSE) {
                continue;
            }
            $json = json_decode($content);
            if ($json === FALSE) {
                continue;
            }
            $module = Module::fromJson($json);
            self::getInstance()->modules[$folder] = $module;
            if($module->activated){
                // Ajouter le path dans l'autoloader
            }
        }
        // Double boucle pour la gestion des dépendances
        foreach (self::getInstance()->modules as $folder => $module){
            if($module->activated){
                $pattern = $folder . DIRECTORY_SEPARATOR . self::MODULE_CLASS_PREFIX.'*.class.php';
                $files = glob($pattern);
                foreach ($files as $file){
                    $classname = substr($file, strrpos($file, DIRECTORY_SEPARATOR) + 1);
                    $classname = substr($classname, 0, strpos($classname, '.'));
                    $callable = $classname . '::registerEvent';
                    include $file;
                    if(!is_callable($callable)){
                        continue;
                    }
                    call_user_func($callable);
                }
            }
        }
    }

    private static function getModuleDirectories()
    {
        $root = self::getInstance()->moduleDirectory;
        $folders = glob($root . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
        for ($i = 0; $i < sizeof($folders); $i++) {
            if ($folders[$i] == __DIR__) {
                unset($folders[$i]);
                break;
            }
        }
        return $folders;
    }

}
