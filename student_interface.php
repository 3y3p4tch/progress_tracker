<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (isset($_POST['ldap']) && isset($_POST['password'])) {
		$conn = sqlsrv_connect('LAPTOP-DJ46JC9S', array( "Database"=>"voodle", "UID"=>"voodle", "PWD"=>"KanekiK" ));
		if ($conn === false) {
			echo json_encode(array('message' => "Server not Reachable"));
			exit();
		}
		$sql = 'SELECT keys, [name] FROM students WHERE LDAP = ? AND passwd = ?';
		$stmt = sqlsrv_query($conn, $sql, array($_POST['ldap'], $_POST['password']));
		if ($stmt == false) {
			echo json_encode(array('message' => "LDAP must be integer"));
			exit();
		}
		if ($row = sqlsrv_fetch_array($stmt)) {
			$name = $row['name'];
			$keys = json_decode($row['keys']);
			$sql = 'SELECT instructors.username, sessions_.session_name, start_time, duration, details FROM sessions_ INNER JOIN instructors ON sessions_.userID = instructors.userID WHERE start_time <= GETDATE() AND GETDATE() <= end_time';
			$stmt = sqlsrv_query($conn, $sql);
			if ($stmt == false) {
				echo json_encode(array('message' => "Server Error"));
				exit();
			}
			$answer = array();
			while ($row = sqlsrv_fetch_array($stmt)) {
				array_push($answer, array('instructor' => $row[0], 'session' => $row[1], 'start' => $row[2]->format('Y-m-d H:i:s'), 'time' => $row[3], 'details' => $row[4]));
			}
			echo json_encode(array('name' => $name, 'keys' => $keys, 'sessions' => $answer));
			exit();
		}
		else {
			echo json_encode(array('message' => 'Invalid Credentials'));
			exit();
		}
	}
}
?>