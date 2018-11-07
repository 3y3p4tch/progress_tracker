<?php
// for logout
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
	setcookie('username','', time() - 3600);
	setcookie('passwd','', time() - 3600);
	session_start();
	session_unset();
	session_destroy();
	header('location: login.php');
	exit();
}

session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['userID'])) {
	header('Location: login.php');
	exit();
}

if(isset($_POST['update_sidebar'])) {
	$client_time = $_POST['update_sidebar'];
	$conn = sqlsrv_connect('LAPTOP-DJ46JC9S', array( "Database"=>"voodle", "UID"=>"voodle", "PWD"=>"KanekiK" ));
	if ($conn === false) {
		echo json_encode(array('message' => "Server not Reachable"));
		exit();
	}
	$sql = "SELECT session_name, start_time, end_time FROM sessions_ INNER JOIN instructors ON sessions_.userID = instructors.userID WHERE sessions_.userID = ?";
	$stmt = sqlsrv_query($conn, $sql, array($_SESSION['userID']));
	if ($stmt === false) {
		echo "Server Error";
		exit();
	}
	$answer = array();
	while( $row = sqlsrv_fetch_array( $stmt) ) {
    	array_push($answer, array('name' => $row['session_name'], 'start' => $row['start_time'], 'end' => $row['end_time']));
	}
	echo json_encode(array('sessions' => $answer, 'now' => time()));
	exit();
}

if(isset($_POST['new_session'])) {
	$data = json_decode($_POST['new_session']);
	$conn = sqlsrv_connect('LAPTOP-DJ46JC9S', array( "Database"=>"voodle", "UID"=>"voodle", "PWD"=>"KanekiK" ));
	if ($conn === false) {
		echo "Server not Reachable";
		exit();
	}
	$sql = "INSERT INTO sessions_ (userID, session_name, start_time, duration, end_time, details, checkpoints) OUTPUT INSERTED.session_id VALUES (?, ?, ?, ?, DATEADD(minute, ?, ?), ?, ?)";
	$stmt = sqlsrv_query($conn, $sql, array($_SESSION['userID'], $data->session_name, $data->start_time, $data->duration, $data->duration, $data->start_time, $data->main_text, json_encode($data->checkpoints)));
	if ($stmt === false) {
		echo "Server Error";
		exit();
	}
	if (sqlsrv_fetch($stmt) === false) {
		echo "Server Error";
		exit();
	}
	$id = sqlsrv_get_field( $stmt, 0);
	$sql = "INSERT INTO questions (userID, session_id, question_no, problem, options, correct) VALUES (?, ?, ?, ?, ?, ?)";
	for($i = 0; $i < sizeof($data->questions); $i++) {
		$stmt = sqlsrv_query($conn, $sql, array($_SESSION['userID'], $id, $i+1, $data->questions[$i]->problem_statement, json_encode($data->questions[$i]->options), json_encode($data->questions[$i]->correct)));
		if ($stmt === false) {
			echo "Server Error";
			exit();
		}
	}
	echo "Successfully submitted details";
	exit();
}

?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Voodle</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- using jquery for easy scripting -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<!-- using font awesome for icons -->
	<link rel="stylesheet" href="./assets/fontawesome/css/all.min.css">
	<link href="https://fonts.googleapis.com/css?family=Cinzel+Decorative|Josefin+Slab:400,700|Quicksand:400,700" rel="stylesheet">
	<link rel="stylesheet" type="text/css" media="screen" href="dashboard.css" />
	<script src='./assets/chart_module/chart.js/dist/Chart.js'></script>
	<script src="dashboard.js"></script>
</head>
<body>
	<input id='sidebar-toggle-checkbox' type='checkbox' style='display: none'>
	<div id="topnav" class='clearfix'>
		<label for='sidebar-toggle-checkbox'><h3 id='sidebar-toggle' style='color: white; float: left; margin: 0 25px; cursor: pointer; line-height: 48px'><i class="fas fa-bars"></i></h3></label>
		<div id='logo' style="background-color: rgba(0,0,0,0); color: white; float: left; padding: 0 16px; font-family: 'Cinzel Decorative'; user-select: none;">
			Voodle
		</div>
		<div id='_' style='float: right; color: white; position: relative;'>
			<a id='logout' href="<?php echo $_SERVER['PHP_SELF'] .'?logout=true'?>" style='float: left; color: white; text-decoration: none; text-align: center; padding: 0 4px;'><i style='padding: 0 16px' class="fas fa-sign-out-alt"></i>Logout</a>
		</div>
	</div>
	<div style='display: flex; position: absolute; width: 100%; flex-direction: row; height: calc(100% - 48px); overflow: hidden'>
		<div id='right-side-bar'>
			<span style='color: white; text-align: center; display: block; cursor: default; padding: 12px 4px;'><i style='padding: 0 16px' class='fas fa-user'></i><?php echo $_SESSION['username']?></span>
			<ul id='sessions_list' class='expand-dropdown'><span><i class="fas fa-angle-down"></i>Your Sessions</span>
			</ul>
		</div>
		<!-- For updating sidebar every 2 seconds -->
		<script>
		async function update_sidebar () {
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					var response = JSON.parse(this.responseText);
					$('#sessions_list li').remove();
					if ('message' in response)
						$('#sessions_list').append('<li style="text-align: center; padding-left: 0; color: #ef9a9a">' + response['message'] + '</li>');
					else {
						var sessions = response['sessions'];
						if (sessions.length === 0) {
							$('#sessions_list').append('<li><i class="fas fa-plus" style="margin: 0 8px 0 0"></i>Create a session</li>');
						}
						else for(var i = 0; i < sessions.length; i++) {
							if ((new Date(sessions[i]['start']['date']) <= new Date(response['now']*1000)) && (new Date(response['now']*1000) <= new Date(new Date(sessions[i]['end']['date'])))) {
								$('#sessions_list').append('<li><span style="display: inline-block; overflow: hidden; max-width: calc(100% - 2em); white-space: nowrap; text-overflow: ellipsis">'+sessions[i]['name']+'</span><i class="fas fa-feather-alt clearfix" style="float: right;"></i></li>');
								$('#sessions_list li:nth-child('+(i+2)+')').on('click', function() {
									window.location.assign('/results.php?name='+escape($(this).children(0).html()));
								});
							}
							else {
								$('#sessions_list').append('<li style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; padding-right: 0.5em">'+sessions[i]['name']+'</li>');
								$('#sessions_list li:nth-child('+(i+2)+')').on('click', function() {
									window.location.assign('/results.php?name='+escape($(this).html()));
								});
							}
						}
					}
				}
			};
			xhttp.open("POST", "<?php echo $_SERVER['PHP_SELF']?>" , true);
			xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhttp.send("update_sidebar");
		}
		update_sidebar();
		setInterval(update_sidebar, 2000);
		</script>
		<div id='site'>
			<h2 style='color: cornflowerblue; margin-bottom: 32px'>Create New Session:</h2>
			<div style='display: flex; flex-direction: row; flex-wrap: wrap; justify-content: space-between'>
				<div style='margin: 20px 20px 20px 0'><h3 style='display: inline-block; margin: 0 10px 0 0'>Session name </h3><input id='session_name' placeholder='For ex. SSL Project'></div>
				<div style='margin: 20px 0;'><h3 style='display: inline-block; margin: 0 10px 0 0'>Session duration <i class='far fa-clock' style='padding: 0 2px'></i></h3><input id='duration1' style='width: 2rem; text-align: center; overflow: hidden' placeholder='hh' type='number' min='0' max='23' onchange="if(parseInt(this.value,10)<10)this.value='0'+this.value;"><b> : </b><input id='duration2' style='width: 2rem; text-align: center; overflow: hidden' placeholder='mm' type='number' min='0' max='59' onchange="if(parseInt(this.value,10)<10)this.value='0'+this.value;"></div>
			</div>
			<h3 style='display: inline-block; margin: 0 10px 0 0'>Start Time <i class='fas fa-calendar-alt' style='padding: 0 2px'></i></h3><input id='start_time' style='width: auto; overflow: hidden' placeholder='mm-dd-yyyy hh:mm'>
			<div style='width: 100%; margin: 20px 0;'>
				<h3>Enter main text below</h3>
				<div style='box-sizing: border-box; box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 1px 1px rgba(0,0,0,0.16); border-radius: 2px;'>
					<ul id='editor_topbar' class='clearfix'>
						<li class='bold'><i class='fa fa-bold'></i></li>
						<li class='italic'><i class='fa fa-italic'></i></li>
						<li class='underline'><i class='fa fa-underline'></i></li>
						<li class='strikethrough'><i class='fa fa-strikethrough'></i></li>
						<li class='subscript'><i class='fa fa-subscript'></i></li>
						<li class='superscript'><i class='fa fa-superscript'></i></li>
						<li class='insertUnorderedList'><i class='fa fa-list-ul'></i></li>
						<li class='insertOrderedList'><i class='fa fa-list-ol'></i></li>
						<li class='indent'><i class='fa fa-indent'></i></li>
						<li class='outdent'><i class='fa fa-outdent'></i></li>
						<li class='justifyLeft'><i class='fa fa-align-left'></i></li>
						<li class='justifyCenter'><i class='fa fa-align-center'></i></li>
						<li class='justifyFull'><i class='fa fa-align-justify'></i></li>
						<li class='justifyRight'><i class='fa fa-align-right'></i></li>
						<span id='preview_latex'>Preview LaTeX</span>
					</ul>
					<iframe id='iframe' width='100%'></iframe>
				</div>
			</div>
			<div id='add_question' style='display: inline-block; margin: 20px 0; background: #E91E63; cursor: pointer; padding: 8px 32px; color: black; border-radius: 16px; position: relative'>
				<span style='display: block; color: whitesmoke;'><i class='fas fa-plus' style='padding: 0 8px 0 0'></i>Add Question</span>
				<div id='add_div'>
					<p id='scq'>Single Correct MCQ</p>
					<p id='mcq'>Multiple Correct MCQ</p>
					<p id='sat'>Short Answer Type</p>
				</div>
			</div><br>
			<span id='submit' style="background: blanchedalmond; padding: 10px 20px; box-sizing: border-box; margin: 10px 0; display: inline-block; cursor: pointer; user-select: none; border-radius: 2px;">Submit<i class="fa fa-chevron-right" style='margin-left: 8px'></i></span>
			<script>
				// For add question button
				$('#add_question').on('click', function () {
					if ($('#add_div').css('display') === 'none')
						$('#add_div').css('display','block');
					else $('#add_div').css('display','none');
				});
				// For SCQ
				var question = $('#add_question');
				var count = 1;
				

				function resize_textarea(input) {
					input.css('height', 'auto');
					input.css('height', input.prop('scrollHeight') + 'px');
				}
				function delayedResize_textarea(input) {
					setTimeout(function () { resize_textarea(input) }, 0);
				}

				function construct_scq(c) {
					count++;
					return '<div id="temp'+c+'" style=\'position: relative\' type_of_question="scq"><h3>Question '+c+'</h3>\
					<textarea placeholder="write your question here" rows="1" spellcheck=false></textarea>\
					<i id="icon'+c+'" class="far fa-trash-alt" style="font-size:large; position: absolute; right: 5px; top: 5px; cursor: pointer" ></i>\
					<i id="checkpoint'+c+'" class="fas fa-flag" style="font-size:large; position: absolute; right: 5px; bottom: 5px; cursor: pointer"></i>\
					<div><label class="container"><input name="'+c+'"type="radio"><span class="radiobttn"></span><input></label></div>\
					<div><label class="container"><input name="'+c+'"type="radio"><span class="radiobttn"></span><input></label></div>\
					<div><label class="container"><input name="'+c+'"type="radio"><span class="radiobttn"></span><input></label></div>\
					<div><label class="container"><input name="'+c+'"type="radio"><span class="radiobttn"></span><input></label></div>\
					</div>';
				}
				function construct_mcq(c) {
					count++;
					return '<div id="temp'+c+'" style=\'position: relative\' type_of_question="mcq"><h3>Question '+c+'</h3>\
					<textarea placeholder="write your question here" rows="1" spellcheck=false></textarea>\
					<i id="icon'+c+'" class="far fa-trash-alt" style="font-size:large; position: absolute; right: 5px; top: 5px; cursor: pointer" ></i>\
					<i id="checkpoint'+c+'" class="fas fa-flag" style="font-size:large; position: absolute; right: 5px; bottom: 5px; cursor: pointer"></i>\
					<div><label class="container"><input name="'+c+'1"type="checkbox"><span class="checkmark"></span><input></label></div>\
					<div><label class="container"><input name="'+c+'2"type="checkbox"><span class="checkmark"></span><input></label></div>\
					<div><label class="container"><input name="'+c+'3"type="checkbox"><span class="checkmark"></span><input></label>\
					<div><label class="container"><input name="'+c+'4"type="checkbox"><span class="checkmark"></span><input></label>\
					</div>';
				}
				function construct_sat(c) {
					count++;
					return '<div id="temp'+c+'" style=\'position: relative\' type_of_question="sat"><h3>Question '+c+'</h3>\
					<textarea placeholder="write your question here" rows="1" spellcheck=false></textarea>\
					<i id="icon'+c+'" class="far fa-trash-alt" style="font-size:large; position: absolute; right: 5px; top: 5px; cursor: pointer" ></i>\
					<i id="checkpoint'+c+'" class="fas fa-flag" style="font-size:large; position: absolute; right: 5px; bottom: 5px; cursor: pointer"></i>\
					</div>';
				}
				function remove_question(num) {
					$('#temp'+num).remove();
					for(var i = num+1; i <= count; i++) {
						$("#temp"+i+" h3").html('Question '+(i-1));
						$("#temp"+i+" .fa-trash-alt").attr('id', 'icon'+(i-1));
						$("#temp"+i+" .fa-flag").attr('id', 'checkpoint'+(i-1));
						$('#temp'+i).attr('id','temp'+(i-1));
					}
					count--;
				}
				$('#scq').on('click', function () {
					$(construct_scq(count)).insertBefore(question);
					delayedResize_textarea($('#temp'+(count-1)+' textarea'));
					$('#icon'+(count-1)).on('click', function () {remove_question(parseInt($(this).attr('id').substr(4)));});
					$('#temp'+(count-1)+' textarea').on('cut', function () { delayedResize_textarea($(this)); });
					$('#temp'+(count-1)+' textarea').on('keydown', function () { delayedResize_textarea($(this)); });
					$('#temp'+(count-1)+' textarea').on('drop', function () { delayedResize_textarea($(this)); });
					$('#temp'+(count-1)+' textarea').on('paste', function () { delayedResize_textarea($(this)); });
					$('#checkpoint'+(count-1)).on('click', function () {$(this).toggleClass('checkpoint')});
					// De-select radio button on ctrl-click
					$('#temp'+(count-1)+' input[type=radio]').on('click', function (e) {
						if (e.ctrlKey) $(this).prop('checked', false);
					});
				});
				$('#mcq').on('click', function () {
					$(construct_mcq(count)).insertBefore(question);
					delayedResize_textarea($('#temp'+(count-1)+' textarea'));
					$('#icon'+(count-1)).on('click', function () {remove_question(parseInt($(this).attr('id').substr(4)));});
					$('#temp'+(count-1)+' textarea').on('cut', function () { delayedResize_textarea($(this)); });
					$('#temp'+(count-1)+' textarea').on('keydown', function () { delayedResize_textarea($(this)); });
					$('#temp'+(count-1)+' textarea').on('drop', function () { delayedResize_textarea($(this)); });
					$('#temp'+(count-1)+' textarea').on('paste', function () { delayedResize_textarea($(this)); });
					$('#checkpoint'+(count-1)).on('click', function () {$(this).toggleClass('checkpoint')});
				});
				$('#sat').on('click', function () {
					$(construct_sat(count)).insertBefore(question);
					delayedResize_textarea($('#temp'+(count-1)+' textarea'));
					$('#icon'+(count-1)).on('click', function () {remove_question(parseInt($(this).attr('id').substr(4)));});
					$('#temp'+(count-1)+' textarea').on('cut', function () { delayedResize_textarea($(this)); });
					$('#temp'+(count-1)+' textarea').on('keydown', function () { delayedResize_textarea($(this)); });
					$('#temp'+(count-1)+' textarea').on('drop', function () { delayedResize_textarea($(this)); });
					$('#temp'+(count-1)+' textarea').on('paste', function () { delayedResize_textarea($(this)); });
					$('#checkpoint'+(count-1)).on('click', function () {$(this).toggleClass('checkpoint')});
				});
				// For submit button
				document.getElementById('submit').addEventListener('click', function() {
					var questions = [];
					for(var i = 1; i < count; i++) {
						var options = [];
						var correct = [];
						if ($('#temp'+i).attr('type_of_question') === 'scq' || $('#temp'+i).attr('type_of_question') === 'mcq') {
							$('#temp'+i+' input:not([type])').each( function(i) {
								options.push($(this).val());
							});
							$('#temp'+i+' input[type]').each( function(i) {
								if ($(this).prop('checked'))
									correct.push(i);
							});
						}
						questions.push({'problem_statement': $('#temp'+i+' textarea').val(), 'type': $('#temp'+i).attr('type_of_question'), 'options' : options, 'correct' : correct});
					}
					var checkpoints = [];
					for(var i = 1; i < count; i++) {
						if ($('#checkpoint'+i).hasClass('checkpoint'))
							checkpoints.push(i);
					}
					var data_to_be_sent = {
						'session_name': $('#session_name').val(),
						'duration': parseInt($('#duration1').val()) * 60 + parseInt($('#duration2').val()),
						'main_text': $('iframe').contents().find('body').html(),
						'questions': questions,
						'checkpoints': checkpoints,
						'start_time': $('#start_time').val()
					};
					$.ajax({
						type: 'POST',
						url: <?php echo "'".$_SERVER['PHP_SELF']."'";?>,
						data: {'new_session': JSON.stringify(data_to_be_sent)},
						success: function(msg) {update_sidebar(); console.log(msg);}
					});
				})
			</script>
		</div>
 	</div>
</body>
</html>
