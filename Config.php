<?php
/**
 * Following variables are for logging
 */
define('LOG_FOLDER', 'log');
/* Default log file used by apps */
define('LOG_FILE', 'default.log');
/**
 * 6 SYSTEM,
 * 5 DEBUG,
 * 4 INFO,
 * 3 WARNING,
 * 2 ERROR,
 * 1 FATAL,
 * 0 OFF
 */
/* Default log level used by apps */
define('LOG_LEVEL', 6);
/* Folder(s) where are the classses, can be a string or an array */
define('WEBAPP_ROOT', 'webapp');
define('WEBAPP_CONF', 'Config.php');
/**
 * Database
 */
/* Defines the JsonDb folder */
define('DB_FOLDER', 'db');
/* Enables JsonDb autosave on disk when on modifications */
define('DB_AUTOSAVE', false);
/* Enables JsonDb autosave on disk when scripts end */
define('DB_SAVE_ON_EXIT', true);