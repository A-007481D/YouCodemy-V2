<?php 
require_once __DIR__ . '/vendor/autoload.php';

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\controllers\HomeController;
use routes\Router;

require_once './utils.php';

Router::get('/home', 'HomeController', 'index');
Router::get('/', 'HomeController', 'index');
Router::resolve($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);