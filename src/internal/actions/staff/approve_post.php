<?php
include_once "../../staff_session.php";
include_once "../../database.php";

if (!staff_session_is_valid() || !staff_is_janny())
    die("You are not allowed here");

$database = new Database();

$board = $_GET["board"];
$post = $_GET["id"];

$database->query(
    "update posts_$board
    set approved = 1
    where id = $post;"
);

header("Location: " . $_SERVER["HTTP_REFERER"]);