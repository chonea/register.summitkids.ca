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

$version = file_get_contents($versionPath); ?><!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Summit Kids Registration - Login</title>

		<link href="/styles/style.login.css" rel="stylesheet" />

		<!-- Add jQuery library -->
		<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
		
		<!-- Add mousewheel plugin (this is optional) -->
		<script type="text/javascript" src="<?php echo $libraryPath; ?>/fancybox/lib/jquery.mousewheel-3.0.6.pack.js"></script>
		
		<!-- Add fancyBox -->
		<link rel="stylesheet" href="<?php echo $libraryPath; ?>/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
		<script type="text/javascript" src="<?php echo $libraryPath; ?>/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>

		<!-- Add Bootstrap library -->
		<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.2.1/js/bootstrap.min.js"></script>
		<!--<script src="<?php echo $libraryPath; ?>/bootstrap/js/bootstrap.min.js"></script>-->
		
		<!-- Optionally add helpers - button, thumbnail and/or media -->
		<link rel="stylesheet" href="<?php echo $libraryPath; ?>/fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" type="text/css" media="screen" />
		<script type="text/javascript" src="<?php echo $libraryPath; ?>/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
		<script type="text/javascript" src="<?php echo $libraryPath; ?>/fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.6"></script>
		
		<link rel="stylesheet" href="<?php echo $libraryPath; ?>/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" type="text/css" media="screen" />
		<script type="text/javascript" src="<?php echo $libraryPath; ?>/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>
		
		<script type="text/javascript">
			$(document).ready(function() {
				$(".fancybox").fancybox({
					maxWidth	: 300,
					maxHeight	: 400,
					fitToView	: true,
					autoSize	: true,
					closeClick	: false,
					openEffect	: 'none',
					closeEffect	: 'none'
				});
			});
/*
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
*/
		</script>
	</head>
	<body>