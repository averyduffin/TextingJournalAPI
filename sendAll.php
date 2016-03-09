<?php

/*
 * Author:   Avery Duffin
 * License:  http://creativecommons.org/licenses/MIT/ MIT
 */
 //$response = file_get_contents('http://www.textingjournal.com/api/index.php/users');
 
 //$response = json_decode($response);
 
 
 
// Method: POST, PUT, GET etc
// Data: array("param" => "value") ==> index.php?param=value

function CallAPI($method, $url, $data = false)
{
    $curl = curl_init();

    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Optional Authentication:
    //curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    //curl_setopt($curl, CURLOPT_USERPWD, "username:password");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}

/*GET Random question and set it as the question of the day.*/
$question = CallAPI("GET", 'http://www.textingjournal.com/api/index.php/randomQuestion');
$question = json_decode($question);

$data = [
	'id' => $question->question[0]->id,
];
$data = json_encode($data);
$todaysQuestion = CallAPI("POST", 'http://www.textingjournal.com/api/index.php/questiondate', $data);



$response = CallAPI("GET", 'http://www.textingjournal.com/api/index.php/users');
$allUsers = json_decode($response);


foreach ($allUsers->users as $value) {
	$data = [
		'number' => $value->phonenumber,
		'message' => $question->question[0]->question,
	];
	$data = json_encode($data);
	$response = CallAPI("POST", 'http://www.textingjournal.com/api/index.php/send', $data);
}






