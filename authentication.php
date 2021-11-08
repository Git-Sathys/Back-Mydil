<?php
/**
 * FICHIER DE TEST POUR L'AUTHENTIFICATION
 */

@ini_set('expose_php', 'off');

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include 'hawk/kernel/Autoloader.class.php';

error_log('Authentication');
Api::execute('AuthManager', 'db/session.json');
