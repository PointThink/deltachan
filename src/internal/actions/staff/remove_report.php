<?php
include_once "../../staff_session.php";
include_once "../../report.php";

if (staff_session_is_valid() && staff_is_moderator())
	report_delete($_POST["report_id"]);

header("Location: " . $_SERVER["HTTP_REFERER"]);
