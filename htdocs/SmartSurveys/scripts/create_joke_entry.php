<?php

header('Content-Type: application/json');


function getDB($db){
	global $file;

	$servername = 'localhost';
	$username = 'root';
	$password = '';

	$conn = new mysqli($servername, $username, $password, $db);
	
	if ($conn->connect_error) {
	    die('Connection failed: ' . $conn->connect_error);
	}

	return $conn;
}


if( isset($_GET['callid']) && isset($_GET['uid']) && isset($_GET['app']) ) {

	$callid = $_GET['callid'];
	$uid    = $_GET['uid'];
	$app    = $_GET['app'];

	$conn = getDB('jokeline');

	$sql = 'INSERT INTO user_jokes (uid, Call_ID, app) VALUES("'.$uid.'", "'.$callid.'", "'.$app.'")';

	if ($result = $conn->query($sql)) {
		echo json_encode(array('result' =>  array('error' => false, 'id' => $conn->insert_id, 'message' => "Joke Created")));
	}else{
		echo json_encode(array('result' =>  array('error' => true, 'message' => "DB Insert Error")));
	}

	$conn->close();
}else{
	echo json_encode(array('result' => array('error' => true, 'message' => "Parameters Incomplete")));
}
?>