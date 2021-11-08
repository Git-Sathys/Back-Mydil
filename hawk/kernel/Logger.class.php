<?php

include '../Config.php';

class Logger
{
    /**
     * Default logging origin when none is specified
     * @var string
     */
    const DEFAULT_LOGGING_NAME = 'HAWK';
    /**
     * Log level OFF
     * @var int
     */
    const LOG_LEVEL_OFF = 0;
    /**
     * Log level FATAL
     * @var int
     */
    const LOG_LEVEL_FATAL = 1;
    /**
     * Log level ERROR
     * @var int
     */
    const LOG_LEVEL_ERROR = 2;
    /**
     * Log level WARNING
     * @var int
     */
    const LOG_LEVEL_WARNING = 3;
    /**
     * Log level INFO
     * @var int
     */
    const LOG_LEVEL_INFO = 4;
    /**
     * Log level DEBUG
     * @var int
     */
    const LOG_LEVEL_DEBUG = 5;
    /**
     * Log level SYSTEM
     * @var int
     */
    const LOG_LEVEL_SYSTEM = 6;

    private static $LEVEL_NAMES = array('OFF', 'FATAL', 'ERROR', 'WARNING', 'INFO', 'DEBUG');

    private $level;
    private $file;
    private $folder;
    private $lastMessage;
    private static $instance;

    private function __construct()
    {
        $this->folder = getcwd() . DIRECTORY_SEPARATOR . LOG_FOLDER;
        if (empty(LOG_FILE) || empty(LOG_LEVEL)) {
            $this->level = self::LOG_LEVEL_OFF;
            return;
        }
        $this->file = $this->folder . DIRECTORY_SEPARATOR . LOG_FILE;
        $this->level = LOG_LEVEL;
    }

    /**
     * Return Logger object if it does exists in Kernel
     */
    private static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new Logger();
        }
        return self::$instance;
    }

    /**
     * Used to change the log level from 5 (debug) to 0 (off)
     * @param int $level
     * @return boolean
     */
    public static function setLevel(int $level)
    {
        if ($level < 0 || $level > 5) {
            self::getInstance()->level = self::LOG_LEVEL_OFF;
            return FALSE;
        }
        Logger::getInstance()->level = $level;
        return TRUE;
    }
    /**
     * Return the current log level
     */
    public static function getLevel()
    {
        return self::getInstance()->level;
    }
    /**
     *
     * Used to set logging file, empty file will disable logging
     * @param string $file
     * @return boolean
     */
    public static function setFile(string $file)
    {
        if (empty($file)) {
            self::getInstance()->level = self::LOG_LEVEL_OFF;
            return FALSE;
        }
        $logFile = Logger::getInstance()->folder . DIRECTORY_SEPARATOR . $file;
        Logger::getInstance()->file = $logFile;
        return TRUE;
    }

    public static function getLastMessage()
    {
        return self::getInstance()->lastMessage;
    }

    public static function debug($message, $module = NULL, $function = NULL)
    {
        self::getInstance()->log($message, self::LOG_LEVEL_DEBUG, $module, $function);
    }

    public static function info($message, $module = NULL, $function = NULL)
    {
        self::getInstance()->log($message, self::LOG_LEVEL_INFO, $module, $function);
    }

    public static function warning($message, $module = NULL, $function = NULL)
    {
        self::getInstance()->log($message, self::LOG_LEVEL_WARNING, $module, $function);
    }

    public static function error($message, $module = NULL, $function = NULL)
    {
        self::getInstance()->log($message, self::LOG_LEVEL_ERROR, $module, $function);
    }

    public static function fatal($message, $module = NULL, $function = NULL)
    {
        self::getInstance()->log($message, self::LOG_LEVEL_FATAL, $module, $function);
    }

    /**
     *
     * Used to log in default output with the following format :
     *
     * date|$level|$module||$log
     *
     * $module and $level are optionnal, see default values
     *
     * @param string $log
     * The string to log
     * @param string $module
     * The origin of the log
     * @param int $level
     * The level of the log
     */
    private function log($log, $level, $module, $function)
    {
        if ($level > self::getInstance()->level) {
            return false;
        }
        $this->lastMessage = $log;
        $log = self::formatLog($log, $level, $module, $function);
        file_put_contents(self::getInstance()->file, $log . "\n", FILE_APPEND);
        return true;
    }

    private static function formatLog($message, $level, $module, $function)
    {
        $log = date('Y-m-d H:i:s') . '|';
        $log .= self::$LEVEL_NAMES[$level] . '|';
        if (isset($module) && !empty($module)) {
            $log .= $module;
            if (isset($function) && !empty($function)) {
                $log .= '()->' . $function;
            }
            $log .= '|';
        }
        $log .= $message;
        return $log;
    }

    /**
     * TODO gestion des exception a étudier
     */
    private static function show_backtrace()
    {
        debug_print_backtrace();
    }
}
