<?php

@ini_set('expose_php', 'off');

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include 'hawk/kernel/Autoloader.class.php';

error_log('Product');
Api::execute('Product', 'db/product.json');
