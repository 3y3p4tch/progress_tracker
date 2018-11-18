<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (isset($_POST['ldap']) && isset($_POST['password'])) {
		$conn = sqlsrv_connect('LAPTOP-DJ46JC9S', array( "Database"=>"voodle", "UID"=>"voodle", "PWD"=>"KanekiK" ));
		if ($conn === false) {
			echo json_encode(array('message' => "Server not Reachable"));
			exit();
		}
		$sql = 'SELECT keys FROM students WHERE LDAP = ? AND passwd = ?';
		$stmt = sqlsrv_query($conn, $sql, array($_POST['ldap'], $_POST['password']));
		if ($stmt == false) {
			echo json_encode(array('message' => "Server Error"));
			exit();
		}
		if ($row = sqlsrv_fetch_array($stmt)) {
			$keys = $row['keys'];
			$sql = 'SELECT instructors.username, sessions_.session_name, start_time, duration FROM sessions_ INNER JOIN instructors ON sessions_.userID = instructors.userID';
			$stmt = sqlsrv_query($conn, $sql);
			if ($stmt == false) {
				echo json_encode(array('message' => "Server Error"));
				exit();
			}
			$answer = array();
			while ($row = sqlsrv_fetch_array($stmt)) {
				array_push($answer, array('instructor' => $row[0], 'session' => $row[1], 'start' => $row[2], 'time' => $row[3]));
			}
			echo json_encode(array('keys' => $keys, 'sessions' => $answer));
			exit();
		}
		else {
			echo json_encode(array('message' => 'Invalid Credentials'));
			exit();
		}
	}
}
?>