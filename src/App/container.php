<?php
/**
 * The container is responsible for 'injecting' all our dependecies.
 * If we want to use some class or database when using our routes
 * we can inject it here. WE must first get the container from our $app.
 * $app is the new Slim(); we declared in `index.php`
 */
$container = $app->getContainer();

/**
 * The container is an associative array of different dependecies that
 * our $app needs. Below we are storing our database connection inside
 * of the app. This will result in that we can call our database
 * in our routes like: $this->get('db'). The config is sent in
 * when we are creating our new Slim App in index.php.
 */
$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO(
      'mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
      $db['user'],
      $db['pass']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // We must always return what we want to inject
    return $pdo;
};

$container['view'] = function ($container) {
    return new \Slim\Views\PhpRenderer('../public/views/');
};
