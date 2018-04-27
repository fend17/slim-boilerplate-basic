<?php
/**
 * Require the autoload script, this will automatically load our classes
 * so we don't have to require a class everytime we use a class. Evertime
 * you create a new class, remember to runt 'composer update' in the terminal
 * otherwise your classes may not be recognized.
 */
require '../../vendor/autoload.php';
require '../App/config.php';


/**
 * Here we are creating the app that will handle all the routes. We are storing
 * our database config inside of 'settings'. This config is later used inside of
 * the container inside 'App/container.php'
 */
$app = new \Slim\App(['settings' => $config]);

/**
 * https://www.slimframework.com/docs/v3/concepts/di.html
 * The container injects our database into our routes so we can reference it
 * by writing '$this->get('db')'
 */
require '../App/container.php';


/********************************
 *          ROUTES              *
 *******************************/

/*
 * Render views/index.php
 * https://www.slimframework.com/docs/v3/features/templates.html#the-slimphp-view-component
 */
$app->get('/', function ($request, $response, $args) {
    return $this->view->render($response, 'index.php');
});

/**
 * Placeholder route
 * https://www.slimframework.com/docs/v3/objects/router.html
 */

$app->get('/about', function ($request, $response, $args) {
    // Create an array in PHP
    $arr = [
        "name"          => "Jesper",
        "description"   => "Teacher"
    ];
    // Send it as an JSON-object
    return $response->withJson($arr);
});

// $app->get('/{name}', function ($request, $response, $args) {
//     $parameters = $request->getQueryParams();
//     //die(var_dump($parameters));
//     return $response->withJson(['name' => $args['name']]);
// });

$app->get('/books', function ($request, $response, $args) {
    $getAll = $this->db->prepare("SELECT * FROM books");
    $getAll->execute();
    $allBooks = $getAll->fetchAll();
    return $response->withJson($allBooks);
});

$app->post('/books', function ($request, $response, $args) {
    $data = $request->getParsedBody();
    $insertOne = $this->db->prepare(
        "INSERT INTO books (title, authorID) 
        VALUES (:title, :authorID)"
    );
    $insertOne->execute([
        ':title'    => $data['title'],
        ':authorID' => $data['authorID']
    ]);
    return $response->withJson($data);
});

$app->get('/books/{id}', function ($request, $response, $args) {
    $getOne = $this->db->prepare("SELECT * FROM books WHERE id = :id");
    $getOne->execute([
        ":id" => $args['id']
    ]);
    $oneBook = $getOne->fetch();
    return $response->withJson($oneBook);
});

$app->run();
