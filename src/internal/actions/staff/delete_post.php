<?php
include_once "../../database.php";
include_once "../../staff_session.php";

$board = $_POST["board"];
$id = $_POST["id"];

if (!staff_session_is_valid()) 
	die("You're not allowed to do that!");

$database = new Database();

function delete_post($id)
{
	global $board;
	global $database;

	// first delete the file
	$post = $database->read_post($board, $id);
	$file_parts = explode(".", $post->image_file);
	$thumbnail_path = $file_parts[0] . "-thumb.webp";
	unlink(__DIR__ . "/../../../$post->image_file");
	unlink(__DIR__ . "/../../../$thumbnail_path");	
	

	$database->remove_post($board, $id);

	foreach ($post->replies as $reply)
		delete_post($reply->id);
}

delete_post($id);

header("Location: /$board/");
