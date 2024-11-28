<?php
session_set_cookie_params(3600 * 24 * 30); // 30 days
session_start();

include_once "../database.php";
include_once "../bans.php";
include_once "../staff_session.php";
include_once "../geolocation.php";
include_once "../turnslite.php";

function file_upload_max_size() {
  static $max_size = -1;

  if ($max_size < 0) {
    // Start with post_max_size.
    $post_max_size = parse_size(ini_get('post_max_size'));
    if ($post_max_size > 0) {
      $max_size = $post_max_size;
    }

    // If upload_max_size is less, then reduce. Except if upload_max_size is
    // zero, which indicates no limit.
    $upload_max = parse_size(ini_get('upload_max_filesize'));
    if ($upload_max > 0 && $upload_max < $max_size) {
      $max_size = $upload_max;
    }
  }
  return $max_size;
}

function parse_size($size) {
  $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
  $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
  if ($unit) {
    // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
    return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
  }
  else {
    return round($size);
  }
}

function error_die($error)
{
	
	if (isset($_POST["is_reply"]))
		header("Location: /" . $_POST["board"] . "/post.php?error=" . urlencode($error) . "&id=" . $_POST["replies_to"]);
	else if (!isset($_POST["is_reply"]) && isset($_POST["board"]))		
		header("Location: /" . $_POST["board"] . "?error=" . urlencode($error));
	else
		header("Location:" . parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH) . "?error=" . urlencode($error));
	
	die();
}

if (!turnslite_verify_response($_POST["cf-turnstile-response"]))
	error_die("Captcha failed!");
	
if (is_user_banned())
{
	header("Location: /internal/error_pages/ban.php");
	die();
}

if ($_SERVER['CONTENT_LENGTH'] > file_upload_max_size())
	error_die("Your file is too big. Max size is " . ini_get("upload_max_filesize"));

if ( $_FILES["file"]["size"] <= 0 && !isset($_POST["is_reply"]) )
{
	error_die("Your post must contain an image");
}

if (isset($_POST["is_reply"]) && trim($_POST["comment"]) == "")
{
	error_die("Your post must containt a comment");
}

$file_upload_dir = "uploads/";
$target_file = "";

$database = new Database();

// If user is logged in as staff create a staff post
$is_mod_post = "0";

if (staff_session_is_valid())
{
	$user = staff_get_current_user();
	$name = $user->username;
	$is_mod_post = "1";
}
else
	$name = $_POST["name"];

if (isset($_POST["sage"]))
	$name .= " SAGE!";

$geolocation = new IPLocationInfo($_SERVER["REMOTE_ADDR"]);

$replies_to = 0;
if (isset($_POST["is_reply"]))
	$replies_to = $_POST["replies_to"];

$title = "";
if (isset($_POST["title"]))
	$title = $_POST["title"];

$result = $database->write_post(
	$_POST["board"], isset($_POST["is_reply"]), $replies_to, $name, trim($title), trim($_POST["comment"]),
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

if (isset($_POST["is_reply"]))
{
	if (!isset($_POST["sage"]))
		$database->bump_post($result->board, $result->replies_to);
	header("Location: /$result->board/post.php?id=$result->replies_to");
}
else
	header("Location: /$result->board/post.php?id=$result->id");
