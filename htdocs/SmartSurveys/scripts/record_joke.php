<?PHP

error_reporting (0);
header('Content-Type: application/json');
set_time_limit(30);

function getDB($db){

	$servername = 'localhost';
	$username = 'root';
	$password = '';

	$conn = new mysqli($servername, $username, $password, $db);
	
	if ($conn->connect_error) {
	    die('Connection failed: ' . $conn->connect_error);
	}

	return $conn;
}

if( isset($_GET['record_id'])) {

	$record_id = $_GET['record_id'];

	$conn = getDB('jokeline');

	$sql = 'UPDATE user_jokes SET recorded = 1 WHERE id = "'.$record_id.'"';

	if ($result = $conn->query($sql)) {
		echo json_encode(array('result' =>  array('error' => false, 'message' => "Updated Query Executed")));
	}else{
		echo json_encode(array('result' =>  array('error' =>  true, 'message' => "DB Update Error")));
	}

	$conn->close();
}else{
	echo json_encode(array('result' => array('error' => true, 'message' => "Parameters Incomplete")));
}


?>