<?php
$directory = dirname(__FILE__);

$cwd = str_replace("\\", "/", getcwd());
if(strpos($cwd, "/examples") !== false) {
	$examplePath = "";
	$indexPath = "../index.php";
	$versionPath = "../version";
	$libraryPath = "../libraries";
	$stylePath = "../styles";
	$scriptPath = "../scripts";
	$registerPath = "../register.php";
}
else {
	$examplePath = "examples/";
	$indexPath = "/index.php";
	$versionPath = "version";
	$libraryPath = "libraries";
	$stylePath = "styles";
	$scriptPath = "scripts";
	$registerPath = "register.php";
}

$version = file_get_contents($versionPath);

function prettyprint($code) {
	echo '<pre class="prettyprint linenums">', str_replace("\t", str_repeat("&nbsp", 4), htmlspecialchars($code)), '</pre>';
}
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Summit Kids Online Registration System</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<style type="text/css">
			body {
				padding-top: 60px;
				padding-bottom: 40px;
			}
			.sidebar-nav {
				padding: 9px 0;
			}
		</style>

		<link href="//maxcdn.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">
		<!--<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">-->
		<!--<link href="<?php echo $libraryPath; ?>/bootstrap/css/bootstrap.min.css" rel="stylesheet">-->

		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		
		<!-- Add Font Awesome library -->
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

		<!-- Add jQuery library -->
		<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>-->
		<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>

		<!-- Add Bootstrap library -->
		<script src="//maxcdn.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>
		<!--<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>-->
		<!--<script src="<?php echo $libraryPath; ?>/bootstrap/js/bootstrap.min.js"></script>-->

		<!-- Add Prettify library -->
		<link href="<?php echo $libraryPath; ?>/prettify/prettify.css" rel="stylesheet">
		<script src="<?php echo $libraryPath; ?>/prettify/prettify.js"></script>

		<!-- Add DateTimePicker library -->
		<link href="<?php echo $libraryPath; ?>/datetimepicker/jquery.datetimepicker.css" rel="stylesheet">
		<script src="<?php echo $libraryPath; ?>/datetimepicker/jquery.datetimepicker.js"></script>

		<!-- Add autoNumeric library -->
		<script src="<?php echo $libraryPath; ?>/autoNumeric/autoNumeric.js"></script>

		<!-- Add Accounting library -->
		<script src="<?php echo $libraryPath; ?>/accounting/accounting.min.js"></script>

		<!-- Add scrollTo library -->
		<script src="<?php echo $libraryPath; ?>/jquery.scrollTo/jquery.scrollto.min.js"></script>
		
		<!-- Add mousewheel plugin (this is optional) -->
		<script type="text/javascript" src="<?php echo $libraryPath; ?>/fancybox/lib/jquery.mousewheel-3.0.6.pack.js"></script>

		<!-- Add fancyBox -->
		<link rel="stylesheet" href="<?php echo $libraryPath; ?>/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
		<script type="text/javascript" src="<?php echo $libraryPath; ?>/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>
		
		<!-- Optionally add helpers - button, thumbnail and/or media -->
		<link rel="stylesheet" href="<?php echo $libraryPath; ?>/fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" type="text/css" media="screen" />
		<script type="text/javascript" src="<?php echo $libraryPath; ?>/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
		<script type="text/javascript" src="<?php echo $libraryPath; ?>/fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.6"></script>
		
		<link rel="stylesheet" href="<?php echo $libraryPath; ?>/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" type="text/css" media="screen" />
		<script type="text/javascript" src="<?php echo $libraryPath; ?>/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>

		<!-- Add form plugin -->
		<script src="<?php echo $scriptPath; ?>/jquery.form.js"></script>
<?php /*
		<!-- Add KendoUI -->
    <link href="<?php echo $libraryPath; ?>/kendoui/styles/kendo.common.min.css" rel="stylesheet" />
    <link href="<?php echo $libraryPath; ?>/kendoui/styles/kendo.default.min.css" rel="stylesheet" />
    <script src="<?php echo $libraryPath; ?>/kendoui/js/kendo.all.min.js"></script>
*/ ?>
<?php if (basename($_SERVER['PHP_SELF']) == "register.php" || basename($_SERVER['PHP_SELF']) == "child.php") { ?>
		<!-- Add Valum's file uploader -->
		<link rel="stylesheet" href="<?php echo $libraryPath; ?>/Valums-file-uploader/client/fileuploader.css" type="text/css" media="screen" />
		<script src="<?php echo $libraryPath; ?>/Valums-file-uploader/client/fileuploader.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				function createUploader(){            
					var uploader = new qq.FileUploader({
						element: document.getElementById('register-form-general-child-photo'),
						action: 'file-upload.php',
						// additional data to send, name-value pairs
						params: {},
						// validation    
						// ex. ['jpg', 'jpeg', 'png', 'gif'] or []
						//allowedExtensions: [],        
						// each file size limit in bytes
						// this option isn't supported in all browsers
						//sizeLimit: 0, // max size   
						//minSizeLimit: 0, // min size
						abortOnFailure: true, // Fail all files if one doesn't meet the criteria
						// set to true to output server response to console
						debug: false,
						// events         
						// you can return false to abort submit
						onSubmit: function(id, fileName){},
						onProgress: function(id, fileName, loaded, total){},
						onComplete: function(id, fileName, responseJSON){
							$('input[name="ChildPhoto"]').val(responseJSON.saveFile);
						},
						onCancel: function(id, fileName){},
						onError: function(id, fileName, xhr){},
						messages: {
							// error messages, see qq.FileUploaderBasic for content            
						},
						showMessage: function(message){ alert(message); }        
					});           
				}
				// in your app create uploader as soon as the DOM is ready
				// don't wait for the window to load  
				window.onload = createUploader;
			});
		</script>
<?php } ?>

		<!-- Add Labelify -->
		<!--	<script type="text/javascript" src="/scripts/jquery.labelify.js"></script> -->
		<script type="text/javascript">
		/*
		$(document).ready(function(){
			$("input.labelify").labelify({
				text: "label"
			});
		});
		*/
		</script>

		<!-- Add custom scripts -->
  	<!--<link href="/<?php echo $stylePath; ?>/style.login.css" rel="stylesheet" />-->
		<link href="<?php echo $stylePath; ?>/style.register.css" rel="stylesheet">
		<script src="<?php echo $scriptPath; ?>/jquery.functions.js"></script>
<?php
switch (basename($_SERVER['PHP_SELF'])) {
	case "index.php":
		echo '<script src="'.$scriptPath.'/jquery.dashboard.js"></script>';
		break;
	case "child.php":
		echo '<script src="'.$scriptPath.'/jquery.child.js"></script>';
		break;
	case "guardian.php":
		echo '<script src="'.$scriptPath.'/jquery.guardian.js"></script>';
		break;
	case "user.php":
		echo '<script src="'.$scriptPath.'/jquery.user.js"></script>';
		break;
	case "program.php":
		echo '<script src="'.$scriptPath.'/jquery.program.js"></script>';
		break;
	case "location.php":
		echo '<script src="'.$scriptPath.'/jquery.location.js"></script>';
		break;
	case "course.php":
		echo '<script src="'.$scriptPath.'/jquery.course.js"></script>';
		break;
	case "activity.php":
		echo '<script src="'.$scriptPath.'/jquery.activity.js"></script>';
		break;
	case "applications.php":
		echo '<script src="'.$scriptPath.'/jquery.applications.js"></script>';
		break;
	default:
		echo '<script src="'.$scriptPath.'/jquery.register.js"></script>';
		break;
}
?>
		<script type="text/javascript">
			$(document).ready(function(){
				$("form.login input, form.signup input")
					.bind("focus.labelFx", function(){
						$(this).prev().hide();
					})
					.bind("blur.labelFx", function(){
						$(this).prev()[!this.value ? "show" : "hide"]();
					})
						.trigger("blur.labelFx");
			});
		</script>
	</head>

	<body onload="prettyPrint()">

<?php
// show potential errors / feedback (from login object)
if (isset($login)) {
    if ($login->errors) {
        foreach ($login->errors as $error) {
            echo $error;
        }
    }
    if ($login->messages) {
        foreach ($login->messages as $message) {
            echo $message;
        }
    }
}
?>

<?php
// show potential errors / feedback (from registration object)
if (isset($registration)) {
    if ($registration->errors) {
        foreach ($registration->errors as $error) {
            echo $error;
        }
    }
    if ($registration->messages) {
        foreach ($registration->messages as $message) {
            echo $message;
        }
    }
}
?>

		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container-fluid">
					<a class="brand" href="<?php echo $indexPath; ?>">Summit Kids Online Registration System</a>
					<div class="nav-collapse collapse">
						<ul class="nav navbar-nav">
<?php
	if ($_SESSION['user_role'] != "admin") {
?>
							<li><a href="register.php"><?php echo WORDING_ENROLL; ?></a></li>
<?php /*
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">Forms <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="files/documents/2014/CC_Authorization_2014.pdf" target="_blank">Standard CC Authorization</a></li>
									<li><a href="files/documents/2014/EFT_Authorization_2014.pdf" target="_blank">Standard EFT Authorization</a></li>
									<li><a href="files/documents/2014/Waivers_2014-15.pdf" target="_blank">Standard Waivers</a></li>
									<li class="divider"></li>
									<li><a href="files/documents/2014/CC_Authorization_Summer_2014.pdf" target="_blank">Summit Spring CC Authorization</a></li>
									<li><a href="files/documents/2014/Waivers_Spring_2014.pdf" target="_blank">Summit Spring Waivers</a></li>
									<li class="divider"></li>
									<li><a href="files/documents/2014/CC_Authorization_Summer_2014.pdf" target="_blank">Summit Summer CC Authorization</a></li>
									<li><a href="files/documents/2014/Waivers_Summer_2014.pdf" target="_blank">Summit Summer Waivers</a></li>
								</ul>
							</li>
<?php
*/
	}
?>
<?php
	if ($_SESSION['user_role'] == "admin" || $_SESSION['user_role'] == "manager" || $_SESSION['user_role'] == "staff") {
/*
?>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">Reports <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="course-listing.php">Course Listing</a></li>
									<li><a href="applications.php">Application Search</a></li>
								</ul>
							</li>
<?php
*/
		if ($_SESSION['user_role'] == "admin" || $_SESSION['user_role'] == "manager") {
			if ($_SESSION['user_location'] != 0 && $_SESSION['user_location'] != NULL && $_SESSION['user_location'] != '') {
				$from = "location AS l";
				$where = "l.id = '".$_SESSION['user_location']."'";
				$where .= " AND (l.disabled IS NULL OR l.disabled = '0000-00-00 00:00:00')";
				$where .= " AND (l.deleted IS NULL OR l.deleted = '0000-00-00 00:00:00')";
				if ($result = $db->select($from, $where)) {
					$user_location = $result[0];
?>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">Manage <?php echo $user_location['name']; ?> <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="applications.php?status=submitted&locationID=<?php echo $_SESSION['user_location']; ?>">Applications Pending Approval</a></li>
									<li><a href="applications.php?locationID=<?php echo $_SESSION['user_location']; ?>">Application Search</a></li>
									<li class="divider"></li>
									<li><a href="location.php?locationID=<?php echo $_SESSION['user_location']; ?>">Edit Location</a></li>
									<li><a href="courses.php?order=location&&locationID=<?php echo $_SESSION['user_location']; ?>">Courses At This Location</a></li>
								</ul>
							</li>
<?php
				}
			} else {
?>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">Manage All Locations <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="applications.php?status=submitted">Applications Pending Approval</a></li>
									<li><a href="applications.php">Application Search</a></li>
									<li><a href="locations.php">Locations</a></li>
									<li><a href="courses.php">Courses</a></li>
								</ul>
							</li>
<?php
			}
		}
		if ($_SESSION['user_role'] == "admin") {
?>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">Administration <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="programs.php">Programs</a></li>
									<li><a href="activities.php">Activities</a></li>
									<li class="divider"></li>
									<li><a href="users.php">User Accounts</a></li>
								</ul>
							</li>
<?php
		}
?>
<?php
	}
?>
						</ul>
						<ul class="nav navbar-nav navbar-right" style="float: right;">
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">Welcome, <?php echo $_SESSION['user_first_name'] . ' ' . $_SESSION['user_last_name']; ?> <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="user.php?userID=<?php echo $_SESSION['user_id']; ?>"><?php echo WORDING_EDIT_USER_DATA; ?></a></li>
									<li class="divider"></li>
									<li><a href="index.php?logout"><?php echo WORDING_LOGOUT; ?></a></li>
								</ul>
							</li>
						</ul>
						<?php /*
						<p class="navbar-text pull-right">
							<img src="images/summitkids_logofinal.png" alt="Summit Kids" style="height: 55px; margin-top: 0px; padding: 5px; cursor: pointer;" onclick="window.location = 'http://www.summitkids.ca/';"/>
						</p>
						*/ ?>
					</div>
				</div>
			</div>
		</div>
		
		<div class="container-fluid">
			<div class="row-fluid">
				<a href="http://www.summitkids.ca" target="_blank"><img id="logo" class="center-block" src="images/summitkids_logofinal.png" alt="Summit Kids" /></a>
			</div>
			<div class="row-fluid">