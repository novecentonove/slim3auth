<?php

use Respect\Validation\Validator as v;

session_start();

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::create(__DIR__ . '/../');
$dotenv->load();

$configuration = [
    'settings' => [
        'determineRouteBeforeAppMiddleware' => true,
        'displayErrorDetails' => true,
        'db' => [
            'driver' => $_ENV['DB_DRIVER'],
            'host' => $_ENV['DB_HOST'],
            'database' => $_ENV['DB_DATABASE'],
            'username' => $_ENV['DB_USERNAME'],
            'password' => $_ENV['DB_PASSWORD'],
            'charset'   => $_ENV['DB_CHARSET'],
            'collation' => $_ENV['DB_COLLATION'],
            'prefix'    => $_ENV['DB_PREFIX'],
        ]
    ],
];

$container = new \Slim\Container($configuration);
$app = new \Slim\App($container);


// Fetch Container
$container = $app->getContainer();


// Illuminate Database
$container['db'] = function ($container){
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($container['settings']['db']);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};


// Initialize Eloquent
$app->getContainer()->get('db');


// Register Auth Class
$container['auth'] = function ($container){
    return new \App\Auth\Auth;
};


// Register flash provider
$container['flash'] = function ($container){
    return new \Slim\Flash\Messages();
};


// Register Csrf package
$container = $app->getContainer();
$container['csrf'] = function ($c){
    return new \Slim\Csrf\Guard;
};
$app->add($container->csrf);


// Register Twig View helper
$container['view'] = function ($container){
    $view = new \Slim\Views\Twig(__DIR__ . '/../resources/views', [
        'cache' => false
    ]);

    // Instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    // Register Auth method
    $view->getEnvironment()->addGlobal('auth', [
        'isLoggedIn' => $container->auth->check(),
        'user' => $container->auth->user()
    ]);

    // Register Flash Template
    $view->getEnvironment()->addGlobal('flash', $container->flash);

    // Register Csrf Extension
    $view->addExtension(new \App\Views\CsrfExtensions($container->csrf));

    return $view;
};


// Register Respect Validator
$container['validator'] = function ($container){
    return new \App\Validation\Validator;
};


// Register Validation Error Middleware
$app->add( new \App\Middleware\ValidationErrorMiddleware($container));


// Register Old Input Middleware 
$app->add( new \App\Middleware\OldInputMiddleware($container));


// Register custom Respect Rules
v::with('App\\Validation\\Rules\\');






//Register Controllers
$container['PagesController'] = function ($container){
    return new \App\Controllers\PagesController($container);
};

$container['AuthController'] = function ($container){
    return new \App\Controllers\Auth\AuthController($container);
};

//Register 404 page view
$container['notFoundHandler'] = function ($container){
    return function ($request, $response) use ($container) {

            $container->view->render($response, '404.twig');

            return $response->withStatus(404)
            ->withHeader('Content-Type', 'text/html');
            
    };
};


require __DIR__ . '/../app/routes.php';