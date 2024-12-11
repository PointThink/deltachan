<?php
include_once "../../staff_session.php";
include_once "../../database.php";

if (!staff_session_is_valid())
    die("You are not allowed here");

$database = new Database();

$board = $_POST["board"];
$post = $_POST["id"];

$database->query(
    "update posts_$board
    set approved = 1
    where id = $post;"
);

header("Location: " . $_SERVER["HTTP_REFERER"]);