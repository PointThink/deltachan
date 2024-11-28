<?php
include_once "../staff_session.php";

$status = staff_login($_POST["username"], $_POST["password"]);

$status_str = "";

switch ($status)
{
	case LoginResult::SUCCESS: $status_str = "success"; break;
	case LoginResult::FAILED_INVALID_USER: $status_str = "invalid_username"; break;
	case LoginResult::FAILED_INVALID_PASSWORD: $status_str = "invalid_password"; break;
}

header("Location: /staff_login.php?result=$status_str");
