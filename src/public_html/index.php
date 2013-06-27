<?php
// Overhead: 3.90, 0.00390, 412K
define('START_TIME', microtime(true));
define('DOCROOT', dirname(__FILE__));
define('ROOT_PATH', dirname(DOCROOT));
require_once ROOT_PATH . '/application/library/App/Error.php';
$app = require_once ROOT_PATH . '/application/library/App/Bootstrap.php';
$app->run();
