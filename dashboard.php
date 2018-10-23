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
	$sql = "SELECT session_name, start_time, duration FROM sessions_ INNER JOIN instructors WHERE userID = ?";
	$stmt = sqlsrv_query($conn, $sql, array($_SESSION['userID']));
	if ($stmt === false) {
		echo json_encode(array('message' => "Server Error"));
		exit();
	}
	$name = sqlsrv_get_field($stmt, 0);
	$start = sqlsrv_get_field($stmt, 1);
	$duration = sqlsrv_get_field($stmt, 2);
	echo json_encode(array('name' => $name, 'start' => $start, 'duration' => $duration, 'now' => time()));
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
			<ul class='expand-dropdown'><span><i class="fas fa-angle-down"></i>Your Sessions</span>
				<li>Voodle<i class="fas fa-feather-alt clearfix" style='float: right;'></i></li>
				<li>Temp<i class="fas fa-feather-alt clearfix" style='float: right;'></i></li>
				<li>Android</li>
				<li>Kaneki Ken</li>
			</ul>
		</div>
		<!-- For updating sidebar every 2 seconds -->
		<script>
		async function update_sidebar () {
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					var response = JSON.parse(this.responseText);
					if (response['message'] == '') {
						// response['name'], response['start'], response['duration']
						// if (time() - response['start'] <= response['duration']){
						// 	<i class="fas fa-angle-down"></i>response['name']</span>
						// }
						// else
						// echo("Yash Parmar");
					}
					// else console.log(response['message']);
				}
			};
			xhttp.open("POST", "<?php echo $_SERVER['PHP_SELF']?>" , true);
			xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhttp.send("update_sidebar");
			setTimeout(update_sidebar, 2000);
		}
		update_sidebar();
		</script>
		<div id='site'>
			<h2 style='color: cornflowerblue; margin-bottom: 32px'>Create New Session:</h2>
			<div style='display: flex; flex-direction: row; flex-wrap: wrap; justify-content: space-between'>
				<div style='margin: 20px 20px 20px 0'><h3 style='display: inline-block; margin: 0 10px 0 0'>Session name </h3><input placeholder='For ex. SSL Project'></div>
				<div style='margin: 20px 0;'><h3 style='display: inline-block; margin: 0 10px 0 0'>Session duration <i class='far fa-clock' style='padding: 0 2px'></i></h3><input style='width: 2rem; text-align: center; overflow: hidden' placeholder='hh' type='number' min='0' max='23' onchange="if(parseInt(this.value,10)<10)this.value='0'+this.value;"><b> : </b><input style='width: 2rem; text-align: center; overflow: hidden' placeholder='mm' type='number' min='0' max='59' onchange="if(parseInt(this.value,10)<10)this.value='0'+this.value;"></div>
			</div>
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
				function construct_scq(c) {
					count++;
					return '<div id="temp'+c+'"><h3>Question '+c+'</h3>\
					<input placeholder="write your question here"><i id="icon'+c+'" class="far fa-trash-alt" style="font-size:large;" ></i></a></div>';
				}
				function construct_mcq(c) {
					count++;
					return '<div id="temp'+c+'"><h3>Question '+c+'</h3>\
					<input placeholder="write your question here"><i id="icon'+c+'" class="far fa-trash-alt" style="font-size:large;" ></i></a></div>';
				}
				function construct_sat(c) {
					count++;
					return '<div id="temp'+c+'"><h3>Question '+c+'</h3>\
					<input placeholder="write your question here"><i id="icon'+c+'" class="far fa-trash-alt" style="font-size:large;" ></i></a></div>';
				}
				function remove_question(num) {
					$('#temp'+num).remove();
					for(var i = num+1; i <= count; i++) {
						$("#temp"+i+" h3").html('Question '+(i-1));
						$("#temp"+i+" i").attr('id', 'icon'+(i-1));
						$('#temp'+i).attr('id','temp'+(i-1));
					}
					count--;
				}
				$('#scq').on('click', function () {
					$(construct_scq(count)).insertBefore(question);
					$('#icon'+(count-1)).on('click', function () {remove_question(parseInt($(this).attr('id').substr(4)));});
				});
				$('#mcq').on('click', function () {
					$(construct_mcq(count)).insertBefore(question);
				});
				$('#sat').on('click', function () {
					$(construct_sat(count)).insertBefore(question);
				});
				// For submit button
				document.getElementById('submit').addEventListener('click', function() {
					$.ajax({
						type: 'POST',
						url: <?php echo "'".$_SERVER['PHP_SELF']."'";?>,
						data: {'user':<?php echo $_SESSION['username']?>,'details': $('#iframe').html()},
						success: function(msg) {alert('Successfully submitted details.')}
					});
				})
			</script>
		</div>
 	</div>
</body>
</html>
