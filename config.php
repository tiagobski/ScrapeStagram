<?php

// Definitions
$config = array(
    'database' => [
        'host' => 'localhost',
        'name' => 'scrapestagram',
        'user' => 'root',
        'password' => ''
    ],
    'path_logs' => __DIR__ . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR
);

// Time
date_default_timezone_set('Europe/Lisbon'); 

// Debugging
ini_set('xdebug.var_display_max_depth', -1);
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);