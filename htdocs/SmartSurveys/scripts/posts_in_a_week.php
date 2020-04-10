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

if( isset($_GET['uid']) ) {

	$uid      = $_GET['uid'];
	$conn     = getDB('baang');

	$sql = 'SELECT COUNT(*) FROM `recording_table` WHERE dateofrecording >= DATE(NOW()) - INTERVAL 7 DAY AND userid = '.$uid;

	if ($result = $conn->query($sql)) {
		if($row = $result->fetch_row()) {
			$count = intval($row[0]);
			echo json_encode(array('result' => array('error' => false, "count" => $count)));
		}else{
			echo json_encode(array('result' => array('error' => true, 'message' => "Nothing Returned!")));
		}
	}else{
		echo json_encode(array('result' => array('error' => true, 'message' => "DB Error")));
	}
	$conn->close();
}else{
	echo json_encode(array('result' => array('error' => true, 'message' => "Parameters Incomplete")));
}
?>