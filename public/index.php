<?php
// Load Composer Autoloader
if (file_exists('../vendor/autoload.php')) {
    require_once '../vendor/autoload.php';
}

require_once '../app/core/config.php';
require_once '../app/core/database.php';
require_once '../app/core/helpers.php';
require_once '../app/core/router.php';
require_once '../app/core/session.php';
require_once '../app/core/auth.php';

// Init Session
session_start();

// Route the request
route_request();
