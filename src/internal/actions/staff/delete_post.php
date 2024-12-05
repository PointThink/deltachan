<?php
include_once "../../database.php";
include_once "../../staff_session.php";
include_once "../../report.php";

$board = $_POST["board"];
$id = $_POST["id"];

if (!staff_session_is_valid()) 
	die("You're not allowed to do that!");

$database = new Database();

post_delete($board, $id);

header("Location: " . $_SERVER["HTTP_REFERER"]);
