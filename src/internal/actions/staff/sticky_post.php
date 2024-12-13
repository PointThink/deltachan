<?php
include_once "../../staff_session.php";
include_once "../../database.php";

if (!staff_is_moderator() || !staff_session_is_valid())
    die("You are not allowed here");

$database = new Database();

$board = $_GET["board"];
$post = $_GET["id"];

$database->query("
			update posts_$board
			set sticky = not sticky
			where id = $post;
");

header("Location: " . $_SERVER["HTTP_REFERER"]);
