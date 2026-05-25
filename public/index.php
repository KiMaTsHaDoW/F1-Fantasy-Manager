<?php
// F1 Fantasy - Punto de entrada principal
define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/core/Router.php';
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/core/Model.php';

spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . '/app/controllers/' . $class . '.php',
        BASE_PATH . '/app/models/'      . $class . '.php',
        BASE_PATH . '/core/'            . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

$router = new Router();

$router->get('',                        'HomeController',   'index');
$router->get('home',                    'HomeController',   'index');

$router->get('auth/login',              'AuthController',   'loginForm');
$router->post('auth/login',             'AuthController',   'login');
$router->get('auth/register',           'AuthController',   'registerForm');
$router->post('auth/register',          'AuthController',   'register');
$router->get('auth/logout',             'AuthController',   'logout');

$router->get('drivers',                 'DriversController','index');
$router->get('drivers/show/{id}',       'DriversController','show');

$router->get('teams',                   'TeamsController',  'index');
$router->get('teams/show/{id}',         'TeamsController',  'show');

$router->get('races',                   'RacesController',  'index');
$router->get('races/show/{id}',         'RacesController',  'show');

$router->get('fantasy',                 'FantasyController','index');
$router->get('fantasy/create',          'FantasyController','create');
$router->post('fantasy/store',          'FantasyController','store');
$router->get('fantasy/edit',            'FantasyController','edit');
$router->get('fantasy/ranking',         'FantasyController','ranking');
$router->get('fantasy/myteam',          'FantasyController','myTeam');
$router->post('fantasy/update',         'FantasyController','update');

$router->get('profile',                 'ProfileController','show');
$router->post('profile/username',       'ProfileController','updateUsername');
$router->post('profile/password',       'ProfileController','updatePassword');

$router->get('leagues',                 'LeagueController', 'index');
$router->get('leagues/create',          'LeagueController', 'create');
$router->post('leagues/store',          'LeagueController', 'store');
$router->get('leagues/show/{id}',       'LeagueController', 'show');
$router->get('leagues/join/{id}',       'LeagueController', 'join');
$router->get('leagues/leave/{id}',      'LeagueController', 'leave');
$router->post('leagues/kick',           'LeagueController', 'kick');
$router->post('leagues/join-code',      'LeagueController', 'joinByCode');

$router->get('admin/recalculate',       'AdminController',  'recalculate');
$router->post('admin/recalculate',      'AdminController',  'doRecalculate');

// Auto-puntuación silenciosa (máx. cada 30 min)
(new AutoScore())->run();

$router->dispatch();
