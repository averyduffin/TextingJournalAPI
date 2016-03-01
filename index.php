<?php
/**
 * DB Connection
 *
 */
 
function getConnection() {
	global $conn;
    try {
        $db_username = "textjoun_user";
        $db_password = "onCo256*";
		$db_string = 'mysql:host=198.71.227.91:3306;dbname=averyduffin_textingjournal';
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
require 'twilio-php-master/Services/Twilio.php';

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
$config = array(
    'twilio' => array(
        'account_sid'  => 'AC53d97902672ef59e5d89d0520bd7a7bc',
        'auth_token'   => '3c699c6a5bfc442fd9e7cc530a53d220',
        'phone_number' => '+13852090140',
    ),
);
// instantiate the Twilio client	
$twilio = new Services_Twilio(				
    $config['twilio']['account_sid'],
    $config['twilio']['auth_token']
);
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
$app->post('/users','addUser');
$app->post('/entries','addEntry');

$app->post('/receiveText','setText');
$app->post('/send', 'sendMessage');
// PUT route
//$app->put('/participants','addUser');
$app->put('/receiveText','setText');

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
    $sql_query = "select * FROM users_dev ORDER BY id";
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
    $sql = "select * FROM users_dev WHERE id=:id";
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
	
    $sql = "INSERT INTO users_dev (`fullname`,`phonenumber`,`emailaddress`, `username`, `password`, `timezone`, `questionfrequencyid`) VALUES (:fullname, :phonenumber, :emailaddress, :username, :password, :timezone, :questionfrequencyid)";
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

    $sql_query = "select * FROM entries_dev ORDER BY id";
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
    $sql = "select * FROM entries_dev WHERE userid=:id";
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
	
    $sql = "INSERT INTO entries_dev (`datetime`,`phonenumber`, `text`, `questionid`, `userid`) VALUES (:datetime, :phonenumber, :text, :questionid, :userid)";
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

    $sql_query = "select * FROM question_dev ORDER BY id";
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

    $sql_query = "select * FROM questionfrequency_dev ORDER BY id";
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

function setText(){
       global $app;
	   $app->response()->header("Content-Type", "text/xml");
		$post = $app->request();
		$MessageSid = $post->post('MessageSid');
		$SmsSid = $post->post('SmsSid');
		$AccountSid = $post->post('AccountSid');
		$MessagingServiceSid = $post->post('MessagingServiceSid');
		$From = $post->post('From');
		$To	 = $post->post('To');
		$Body = $post->post('Body');
		$NumMedia = $post->post('NumMedia');
		$datetime = date("m-d-Y h:i:sa");
		$questionid = 1;
		$userid = 3;
		
		
    $sql = "INSERT INTO entries_dev (`datetime`,`phonenumber`, `text`, `questionid`, `userid`, `messageSid`, `smsid`, `accountsid`, `messagingservicesid`, `nummedia`) VALUES (:datetime, :phonenumber, :text, :questionid, :userid, :messageSid, :smsid, :accountsid, :messagingservicesid, :nummedia)";
    try {
        $dbCon = getConnection();
        $stmt = $dbCon->prepare($sql);  
        $stmt->bindParam("datetime", $datetime);
        $stmt->bindParam("phonenumber", $From);
        $stmt->bindParam("text", $Body);
		$stmt->bindParam("questionid", $questionid);
		$stmt->bindParam("userid", $userid);
		
		$stmt->bindParam("messageSid", $MessageSid);
		$stmt->bindParam("smsid", $SmsSid);
		$stmt->bindParam("accountsid", $AccountSid);
		$stmt->bindParam("messagingservicesid", $MessagingServiceSid);
		$stmt->bindParam("nummedia", $NumMedia);
        $stmt->execute();
        $dbCon = null;
		echo '<?xml version="1.0" encoding="UTF-8"?><Response><Message>Journal entry recieved. Check website to view all entries.</Message></Response>';
		//return $app -> render('response.xml');
    }
    catch(PDOException $e) {
        echo '<?xml version="1.0" encoding="UTF-8"?><Response><Message>Journal Message wasnt recieved, please try and resend it later.</Message></Response>';
    }  
        
}

function sendMessage(){
	global $twilio;
	global $config;
	global $app;
	$app->response()->header("Content-Type", "application/json");
    $post = json_decode($app->request()->getBody());
	$number = $post->number;
	$message = $post->message;
	
	//$sender  = "+18012101494";
	//$response = "Here's a text!";
	try {
		$sms = $twilio->account->sms_messages->create(
			$config['twilio']['phone_number'],  // the number to send from
			$number,
			$message
		);
		echo '{"Success": "200"}';
    }
    catch(Exception $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }    
	
}