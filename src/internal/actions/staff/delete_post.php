<?php
include_once "../../database.php";
include_once "../../staff_session.php";
include_once "../../report.php";

$board = $_GET["board"];
$id = $_GET["id"];

if (!staff_session_is_valid() || !staff_is_janny()) 
	die("You're not allowed to do that!");

$database = new Database();

post_delete($database, $board, $id);

header("Location: " . $_SERVER["HTTP_REFERER"]);
