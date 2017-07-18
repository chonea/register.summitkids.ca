<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Login Form Example</title>
</head>

<body>

<?php
session_start();
 
use PFBC\Form;
use PFBC\Element;
 
include("libraries/PFBC/Form.php");

$form = new Form("login");
$form->addElement(new Element\HTML('<legend>Login</legend>'));
$form->addElement(new Element\Hidden("form", "login"));
$form->addElement(new Element\Email("Email Address:", "Email", array(
		"required" => 1
)));
$form->addElement(new Element\Password("Password:", "Password", array(
		"required" => 1
)));
$form->addElement(new Element\Checkbox("", "Remember", array(
		"1" => "Remember me"
)));
$form->addElement(new Element\Button("Login"));
$form->addElement(new Element\Button("Cancel", "button", array(
		"onclick" => "history.go(-1);"
)));
$form->render();
?>

</body>
</html>