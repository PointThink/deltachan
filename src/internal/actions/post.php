<?php
session_set_cookie_params(3600 * 24 * 30); // 30 days
session_start();

include_once "../database.php";
include_once "../bans.php";
include_once "../staff_session.php";
include_once "../geolocation.php";

function error_die($error)
{
	if ($_POST["is_reply"])
		header("Location: /" . $_POST["board"] . "/post.php?error=" . htmlspecialchars($error) . "&id=" . $_POST["replies_to"]);
	else		
		header("Location: /" . $_POST["board"] . "?error=" . htmlspecialchars($error));
	
	die();
}


if (is_user_banned())
{
	header("Location: /internal/error_pages/ban.php");
	die();
}

if ($_FILES["file"]["size"] <= 0 && trim($_POST["comment"]) == "")
{
	error_die("Your post must contain an image or comment");
}

$file_upload_dir = "uploads/";
$target_file = "";

$database = new Database();

// If user is logged in as staff create a staff post
$is_mod_post = "0";
$name = $_POST["name"];

if (staff_session_is_valid())
{
	$user = staff_get_current_user();
	$name = $user->username;
	$is_mod_post = "1";
}

$geolocation = new IPLocationInfo($_SERVER["REMOTE_ADDR"]);

$result = $database->write_post(
	$_POST["board"], $_POST["is_reply"], $_POST["replies_to"], $name, trim($_POST["title"]), trim($_POST["comment"]),
	$_SERVER["REMOTE_ADDR"], $geolocation->country, $is_mod_post
);

if (!is_dir(__DIR__ . "/../../" . $file_upload_dir))
	mkdir(__DIR__ . "/../../" . $file_upload_dir);

if ($_FILES["file"]["size"] > 0) 
{	
	$target_file = $file_upload_dir . "$result->board-" . strval($result->id) . "." . pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
	move_uploaded_file($_FILES["file"]["tmp_name"], __DIR__ . "/../../" . $target_file);
	$database->update_post_file($result->board, $result->id, $target_file);

	if (str_starts_with($_FILES["file"]["type"], "image"))
	{
		// create image thumbnail
		$image_data = file_get_contents(__DIR__ . "/../../" . $target_file);
		$image = imagecreatefromstring($image_data);
			
		$width = imagesx($image);
		$height = imagesy($image);

		$desired_width = 200;
		$desired_height = floor($height * ($desired_width / $width));

		$virtual_image = imagecreatetruecolor($desired_width, $desired_height);
		imagealphablending($virtual_image, false);
		imagesavealpha($virtual_image, true);
		$color = imagecolorallocatealpha($virtual_image, 0, 0, 0, 127);
		imagefill($virtual_image, 0, 0, $color);
		imagecopyresampled($virtual_image, $image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
		imagewebp($virtual_image, __DIR__ . "/../../" . $file_upload_dir . "$result->board-$result->id-thumb.webp");
	}
}

// keep track of created posts
if (!isset($_SESSION["users_posts"]))
	$_SESSION["users_posts"] = array();

array_push($_SESSION["users_posts"], $result->id);

if ($_POST["is_reply"])
{
	$database->bump_post($result->board, $result->replies_to);
	header("Location: /$result->board/post.php?id=$result->replies_to");
}
else
	header("Location: /$result->board/post.php?id=$result->id");
