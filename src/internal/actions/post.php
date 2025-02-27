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
	header("Location: /internal/error_pages/error.php?message=" . urlencode($error));
	die();
}

function generate_tripcode($key_phrase)
{
	$salt = chan_info_read()->password_salt;
	return substr(md5($salt . $key_phrase), -10);
}

if (turnslite_is_enabled())
{
	if (!isset($_POST["cf-turnslite-response"]))
		error_die("Captcha response token not provided");

	if (!turnslite_verify_response($_POST["cf-turnstile-response"]))
		error_die("Captcha failed!");
}	
if (is_user_banned())
{
	header("Location: /internal/error_pages/ban.php");
	die();
}

$database = new Database();
$chan_info = chan_info_read();

if ($chan_info->rate_limiting_enabled)
{
	$user_ip = $_SERVER["REMOTE_ADDR"];
	$board = $_POST["board"];
	// check if user has made too many threads recently
	// gather recent threads
	$result = $database->query("select count(*) from posts_$board where poster_ip = '$user_ip' and creation_time >= NOW() - interval $chan_info->rate_limit_range second;");
	$timestamps = array();

	$count = $result->fetch_assoc()["count(*)"];
	echo $count;

	if ($count > $chan_info->rate_limit_max_threads)
		error_die("Slow down! You're making too many posts!");
}

if ($_SERVER['CONTENT_LENGTH'] > file_upload_max_size())
	error_die("Your file is too big. Max size is " . ini_get("upload_max_filesize"));

if (!$chan_info->allow_text_only_ops)
	if (trim($_POST["comment"]) == "" && $_FILES["file"]["size"] <= 0)
		error_die("Your post must containt a comment or image");
else
	if ( $_FILES["file"]["size"] <= 0 && !isset($_POST["is_reply"]) )
		error_die("Your post must contain an image");

if (isset($_POST["is_reply"]) && trim($_POST["comment"]) == "" && $_FILES["file"]["size"] <= 0)
	error_die("Your post must containt a comment or image");

// get mime type of uploaded file

$allowed_mime_types = array(
	"image/",
	"video/",
	"audio/"
);

if ($_FILES["file"]["size"] > 0)
{
	$upload_mime = mime_content_type($_FILES["file"]["tmp_name"]);
	
	$allowed = false;
	foreach ($allowed_mime_types as $allowed_mime_type)
	{
		if (str_starts_with(strtolower($upload_mime), $allowed_mime_type))
		{
			$allowed = true;
			break;
		}
	}

	if (!$allowed)
		error_die("Files of type $upload_mime are not allowed");
}

if (str_contains($_FILES["file"]["name"], "|"))
	error_die("File names cannot contain |");

$file_upload_dir = "uploads/";
$target_file = "";

// If user is logged in as staff create a staff post
$is_mod_post = "0";

if (staff_session_is_valid())
{
	$user = staff_get_current_user();
	$name = $user->username;
	$is_mod_post = "1";
}
else
{
	if (str_contains($_POST["name"], "!"))
		error_die("Name cannot contain !");

	$name = $_POST["name"];
	if (str_contains($name, "#"))
	{
		// tripcode detected
		$trip_part = explode("#", $name, 2);
		$key_phrase = $trip_part[1];
		$name = $trip_part[0] . "!!" . generate_tripcode($key_phrase);
	}
}

if (isset($_POST["sage"]))
	$name .= " SAGE!";

$geolocation = new IPLocationInfo($_SERVER["REMOTE_ADDR"]);

$replies_to = 0;
if (isset($_POST["is_reply"]))
	$replies_to = $_POST["replies_to"];

$title = "";
if (isset($_POST["title"]))
	$title = $_POST["title"];

$result = post_create(
	$database,
	$_POST["board"], isset($_POST["is_reply"]), $replies_to, $name, trim($title), trim($_POST["comment"]),
	$_SERVER["REMOTE_ADDR"], $geolocation->country, $is_mod_post
);

if (!is_dir(__DIR__ . "/../../" . $file_upload_dir))
	mkdir(__DIR__ . "/../../" . $file_upload_dir);

if ($_FILES["file"]["size"] > 0) 
{	
	$target_file = $file_upload_dir . "$result->board-" . strval($result->id) . "." . pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
	move_uploaded_file($_FILES["file"]["tmp_name"], __DIR__ . "/../../" . $target_file);
	post_update_file($database, $result->board, $result->id, "$target_file|" . $_FILES["file"]["name"]);
	$result->image_file = $target_file;

	if (str_starts_with($_FILES["file"]["type"], "image"))
	{
		post_generate_thumbnail($result);
	}
}

// keep track of created posts
if (!isset($_SESSION["users_posts"]))
	$_SESSION["users_posts"] = array();

array_push($_SESSION["users_posts"], $result->id);

if (isset($_POST["is_reply"]))
{
	if (!isset($_POST["sage"]))
		post_bump($database, $result->board, $result->replies_to);
	header("Location: /$result->board/post.php?id=$result->replies_to");
}
else
	header("Location: /$result->board/post.php?id=$result->id");
