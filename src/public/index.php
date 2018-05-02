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


$app->get('/books', function ($request, $response, $args) {
    // Base Query
    // Everything after ? is stored in 'getQueryParams()'
    $query = $request->getQueryParams();
    $sql = "SELECT * FROM BOOKS ";
    $executeParams = [];
    // If /books?limit=5 enter if-statement
    if (isset($query['limit'])) {
        $sql .= "LIMIT :limitParam";
        $executeParams[':limitParam'] = (int) $query['limit'];
    }

    if (isset($query['author'])) {
        $sql .= " WHERE authorID = :author";
        $executeParams[':author'] = $query['author'];
    }

    // SELECT * FROM BOOKS WHERE title LIKE %rätte%;
    if (isset($query['title'])) {
        $sql .= " WHERE title LIKE :title";
        $executeParams[':title'] = "%" . $query['title'] . "%";
    }
    
    // The query must be built when we execute it
    $getAll = $this->db->prepare($sql);
    $getAll->execute($executeParams);
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

// DELETE
$app->delete('/books/{id}', function ($request, $response, $args) {
    $deleteOne = $this->db->prepare("DELETE FROM books WHERE id = :id");
    $deleteOne->execute([
        ":id" => $args['id']
    ]);
    return $response->withJson("Success");
});

// PATCH
$app->patch('/books/{id}', function ($request, $response, $args) {
    $patchOne = $this->db->prepare("DELETE FROM books WHERE id = :id");
    $patchOne->execute([
        ":id" => $args['id']
    ]);
    return $response->withJson("Success");
});




$app->run();
