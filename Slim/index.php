<?php
/**
 * DB Connection
 *
 */
 
function getConnection() {
    try {
        $db_username = "derrick6_devuser";
        $db_password = "novNG#91011";
		$db_string = 'mysql:host=localhost;dbname=derrick6_dev';
        $conn = new PDO($db_string, $db_username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
    return $conn;
}
/**
 * Step 1: Require the Slim Framework
 *
 * If you are not using Composer, you need to require the
 * Slim Framework and register its PSR-0 autoloader.
 *
 * If you are using Composer, you can skip this step.
 */
require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

/**
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */
//$app = new \Slim\Slim();
$app = new \Slim\Slim(array(
        ));

/*ENABLE CORS AND FIX PROBLEMS Angular js had WITH CORS*/
if (isset($_SERVER['HTTP_ORIGIN'])) {
	header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
	header('Access-Control-Allow-Credentials: true');
	//header('Access-Control-Max-Age: 86400');    // cache for 1 day
}
// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
		header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");         

	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
		header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

}

$app->map('/:x+', function($x) { http_response_code(200);})->via('OPTIONS');
	
/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, `Slim::patch`, and `Slim::delete`
 * is an anonymous function.
 */

// GET route
$app->get('/participants/','getUsers');
$app->get('/participants/:id','getUser');
// POST route
$app->post('/participants','addUser');

// PUT route
$app->put('/participants','addUser');

// PATCH route
$app->patch('/participants', function () { echo 'This is a PATCH route';});

// DELETE route
$app->delete('/participants',function () {echo 'This is a DELETE route';});

/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();

/*
 * Route functions
 *
 */
function getUsers() {
	global $app;

    $sql_query = "select `id`,`name`,`phone`,`email`, `numberOfTickets`, `paymentType`, `totalPaid`, `date` FROM participantsDev ORDER BY id";
    try {
        $dbCon = getConnection();
        $stmt   = $dbCon->query($sql_query);
        $users  = $stmt->fetchAll(PDO::FETCH_OBJ);
        $dbCon = null;
        echo '{"participants": ' . json_encode($users) . '}';
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }    
}

function getUser($id) {
	global $app;
    $sql = "select `id`,`name`,`phone`,`email`, `numberOfTickets`, `paymentType`, `totalPaid`, `date` FROM participantsDev WHERE id=:id";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);  
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $user = $stmt->fetchObject();  
        $dbCon = null;
        echo json_encode($user); 
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

function findByName($query) {
    $sql = "SELECT * FROM restAPI WHERE UPPER(name) LIKE :query ORDER BY name";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);
        $query = "%".$query."%";
        $stmt->bindParam("query", $query);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_OBJ);
        $dbCon = null;
        echo '{"participants": ' . json_encode($users) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

function addUser() {
	global $app;
	$app->response()->header("Content-Type", "application/json");
    $post = json_decode($app->request()->getBody());
	$name = $post->name;
	$phone = $post->phone;
	$email = $post->email;
	$address = $post->street + " " + $post->city + " " + $post->state + " " + $post->zip;
	$numberOfTickets = $post->numberOfTickets;
	$paymentType = 'Cash';
	$totalDue = $post->totalDue;
	
    $sql = "INSERT INTO participantsDev (`name`,`phone`,`email`, `numberOfTickets`, `paymentType`, `totalPaid`) VALUES (:name, :phone, :email, :numberOfTickets, :paymentType, :totalPaid)";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);  
        $stmt->bindParam("name", $name);
        $stmt->bindParam("phone", $phone);
		$stmt->bindParam("email", $email);
        $stmt->bindParam("numberOfTickets", $numberOfTickets);
		$stmt->bindParam("paymentType", $paymentType);
		$stmt->bindParam("totalPaid", $totalDue);
        $stmt->execute();
        //$user->id = $dbCon->lastInsertId();
        $dbCon = null;
        echo json_encode("Success"); 
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

function updateUser($id) {
    global $app;
    $req = $app->request();
	
    $paramName = $req->params('name');
    $paramEmail = $req->params('email');

    $sql = "UPDATE restAPI SET name=:name, email=:email WHERE id=:id";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);  
        $stmt->bindParam("name", $paramName);
        $stmt->bindParam("email", $paramEmail);
        $stmt->bindParam("id", $id);
        $status->status = $stmt->execute();

        $dbCon = null;
        echo json_encode($status); 
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

function deleteUser($id) {
    $sql = "DELETE FROM restAPI WHERE id=:id";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);  
        $stmt->bindParam("id", $id);
        $status->status = $stmt->execute();
        $dbCon = null;
        echo json_encode($status);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}