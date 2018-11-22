<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (isset($_POST['ldap']) && isset($_POST['password'])) {
		$conn = sqlsrv_connect('LAPTOP-DJ46JC9S', array( "Database"=>"voodle", "UID"=>"voodle", "PWD"=>"KanekiK" ));
		if ($conn === false) {
			echo json_encode(array('message' => "Server not Reachable"));
			exit();
		}
		$sql = 'SELECT [name], keys FROM students WHERE LDAP = ? AND passwd = ?';
		$stmt = sqlsrv_query($conn, $sql, array($_POST['ldap'], $_POST['password']));
		if ($stmt == false) {
			echo json_encode(array('message' => "LDAP must be integer"));
			exit();
		}
		if ($row = sqlsrv_fetch_array($stmt)) {
			$name = $row['name'];
			$keys_b4 = json_decode($row['keys']);
			$keys_after = array();
			for($i = 0; $i < sizeOf($keys_b4); $i++) {
				if (new DateTime($keys_b4[$i]->time) >= new DateTime()) {
					array_push($keys_after, $keys_b4[$i]);
				}
			}
			$sql = 'UPDATE students SET keys = ? WHERE LDAP = ?';
			$stmt = sqlsrv_query($conn, $sql, array(json_encode($keys_after), $_POST['ldap']));
			if ($stmt == false) {
				echo json_encode(array('message' => "Server Error"));
				exit();
			}
			$answer = array();
			$sql = 'SELECT instructors.username, sessions_.session_name, start_time, duration, session_id FROM sessions_ INNER JOIN instructors ON sessions_.userID = instructors.userID WHERE start_time <= GETDATE() AND GETDATE() <= end_time';
			$stmt = sqlsrv_query($conn, $sql);
			if ($stmt == false) {
				echo json_encode(array('message' => "Server Error"));
				exit();
			}
			while ($row = sqlsrv_fetch_array($stmt)) {
				array_push($answer, array('instructor' => $row[0], 'session' => $row[1], 'time' => $row[3], 'identifier' => $row[4]));
			}
			echo json_encode(array('name' => $name, 'sessions' => $answer, 'keys' => $keys_after));
			exit();
		}
		else {
			echo json_encode(array('message' => 'Invalid Credentials'));
			exit();
		}
	}
	else if (isset($_POST['who']) && isset($_POST['comment'])) {
		$id = json_decode($_POST['who'])[0];
		$ques_no = json_decode($_POST['who'])[1];
		$conn = sqlsrv_connect('LAPTOP-DJ46JC9S', array( "Database"=>"voodle", "UID"=>"voodle", "PWD"=>"KanekiK" ));
		if ($conn === false) {
			echo json_encode(array('message' => "Server not Reachable"));
			exit();
		}
		$sql = 'UPDATE questions SET comments = JSON_MODIFY(comments, "append $", ?) WHERE session_id = ? AND question_no = ?';
		$stmt = sqlsrv_query($conn, $sql, array($_POST['comment'], $id, $ques_no));
		if ($stmt == false) {
			echo json_encode(array('message' => "Server Error"));
			exit();
		}
		echo '{"done": 1}';
	}
	else if (isset($_POST['ldap']) && isset($_POST['identifier'])) {
		$conn = sqlsrv_connect('LAPTOP-DJ46JC9S', array( "Database"=>"voodle", "UID"=>"voodle", "PWD"=>"KanekiK" ));
		if ($conn === false) {
			echo json_encode(array('message' => "Server not Reachable"));
			exit();
		}
		$sql = 'SELECT username, duration, session_name, details, (SELECT COUNT(DISTINCT question_no) FROM questions WHERE session_id = ?) AS count FROM sessions_ INNER JOIN instructors ON instructors.userID = sessions_.userID WHERE sessions_.session_id = ? AND start_time <= GETDATE() AND GETDATE() <= end_time';
		$stmt = sqlsrv_query($conn, $sql, array($_POST['identifier'], $_POST['identifier']));
		if ($stmt == false) {
			echo json_encode(array('message' => "Server Error"));
			exit();
		}
		if ($row = sqlsrv_fetch_array($stmt)) {
			echo json_encode(array('instructor' => $row['username'], 'instruction' => $row['details'], 'time' => $row['duration'], 'session' => $row['session_name'], 'total' => $row['count']));
			exit();
		}
		else {
			echo json_encode(array('message' => 'The session has expired, please refresh'));
			exit();
		}
	}
	else if (isset($_POST['ldap']) && isset($_POST['question_data'])) {
		$conn = sqlsrv_connect('LAPTOP-DJ46JC9S', array( "Database"=>"voodle", "UID"=>"voodle", "PWD"=>"KanekiK" ));
		if ($conn === false) {
			echo json_encode(array('message' => "Server not Reachable"));
			exit();
		}
		$sql = 'SELECT problem, options, [type], comments FROM questions INNER JOIN sessions_ ON sessions_.session_id = questions.session_id WHERE question_no = ? AND questions.session_id = ? AND start_time <= GETDATE() AND GETDATE() <= end_time';
		$question = json_decode($_POST['question_data'])[1];
		$session = json_decode($_POST['question_data'])[0];
		$stmt = sqlsrv_query($conn, $sql, array($question, $session));
		if ($stmt == false) {
			echo json_encode(array('message' => "Server Error"));
			exit();
		}
		if ($row = sqlsrv_fetch_array($stmt)) {
			echo json_encode(array('problem' => $row['problem'], 'options' => json_decode($row['options']), 'comments' => json_decode($row['comments']), 'type' => $row['type']));
			exit();
		}
		else {
			echo json_encode(array('message' => 'You are not allowed to enter this session or the session has expired'));
			exit();
		}
	}
}
?>