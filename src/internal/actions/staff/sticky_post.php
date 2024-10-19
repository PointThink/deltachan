<?php
include_once "../../staff_session.php";
include_once "../../database.php";

if (!staff_is_moderator() || !staff_session_is_valid())
    die("You are not allowed here");

$database = new Database();
$database->make_post_sticky($_POST["board"], $_POST["id"]);

header("Location: " . $_SERVER["HTTP_REFERER"]);
