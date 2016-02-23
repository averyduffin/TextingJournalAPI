<?php
/**
 * DB Connection
 *
 */
 
function getConnection() {
    try {
        $db_username = "texting_user";
        $db_password = "34dfrigkcf$98RF(*S";
		$db_string = 'mysql:host=localhost:3307;dbname=textingjournal';
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
$app->get('/users/','getUsers');
$app->get('/users/:id','getUser');

$app->get('/entries/','getEntries');
$app->get('/entries/:id','getEntry');

$app->get('/questions/','getQuestions');
$app->get('/frequency/','getFrequency');
// POST route
$app->post('/user','addUser');
$app->post('/user','addEntry');

// PUT route
//$app->put('/participants','addUser');

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

    $sql_query = "select * FROM users ORDER BY id";
    try {
        $dbCon = getConnection();
        $stmt   = $dbCon->query($sql_query);
        $users  = $stmt->fetchAll(PDO::FETCH_OBJ);
        $dbCon = null;
        echo '{"users": ' . json_encode($users) . '}';
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }    
}

function getUser($id) {
	global $app;
    $sql = "select * FROM users WHERE id=:id";
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

function addUser() {
	global $app;
	$app->response()->header("Content-Type", "application/json");
    $post = json_decode($app->request()->getBody());
	$name = $post->name;
	$phone = $post->phone;
	$email = $post->email;
	$username = $post->username;
	$password = $post->password;
	$questionFrequency = $post->questionFrequency;
	$timezone = $post->timezone;
	
    $sql = "INSERT INTO users (`fullname`,`phonenumber`,`emailaddress`, `username`, `password`, `timezone`, `questionfrequencyid`) VALUES (:fullname, :phonenumber, :emailaddress, :username, :password, :timezone, :questionfrequencyid)";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);  
        $stmt->bindParam("fullname", $name);
        $stmt->bindParam("phonenumber", $phone);
		$stmt->bindParam("emailaddress", $email);
        $stmt->bindParam("username", $username);
		$stmt->bindParam("password", $password);
		$stmt->bindParam("timezone", $timezone);
		$stmt->bindParam("questionfrequencyid", $questionFrequency);
        $stmt->execute();
        //$user->id = $dbCon->lastInsertId();
        $dbCon = null;
        echo json_encode("Success"); 
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}



function getEntries() {
	global $app;

    $sql_query = "select * FROM entries ORDER BY id";
    try {
        $dbCon = getConnection();
        $stmt   = $dbCon->query($sql_query);
        $users  = $stmt->fetchAll(PDO::FETCH_OBJ);
        $dbCon = null;
        echo '{"entries": ' . json_encode($users) . '}';
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }    
}

function getEntry($id) {
	global $app;
    $sql = "select * FROM entries WHERE userid=:id";
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


function addEntry() {
	global $app;
	$app->response()->header("Content-Type", "application/json");
    $post = json_decode($app->request()->getBody());
	$datetime = $post->datetime;
	$phonenumber = $post->phonenumber;
	$text = $post->text;
	$questionid = $post->questionid;
	$userid = $post->userid;
	
    $sql = "INSERT INTO entries (`datetime`,`phonenumber`, `text`, `questionid`, `userid`) VALUES (:datetime, :phonenumber, :text, :questionid, :userid)";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);  
        $stmt->bindParam("datetime", $datetime);
        $stmt->bindParam("phonenumber", $phonenumber);
        $stmt->bindParam("text", $text);
		$stmt->bindParam("questionid", $questionid);
		$stmt->bindParam("userid", $userid);
        $stmt->execute();
        //$user->id = $dbCon->lastInsertId();
        $dbCon = null;
        echo json_encode("Success"); 
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

function getQuestions() {
	global $app;

    $sql_query = "select * FROM question ORDER BY id";
    try {
        $dbCon = getConnection();
        $stmt   = $dbCon->query($sql_query);
        $users  = $stmt->fetchAll(PDO::FETCH_OBJ);
        $dbCon = null;
        echo '{"questions": ' . json_encode($users) . '}';
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }    
}


function getFrequency() {
	global $app;

    $sql_query = "select * FROM questionfrequency ORDER BY id";
    try {
        $dbCon = getConnection();
        $stmt   = $dbCon->query($sql_query);
        $users  = $stmt->fetchAll(PDO::FETCH_OBJ);
        $dbCon = null;
        echo '{"frequency": ' . json_encode($users) . '}';
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }    
}