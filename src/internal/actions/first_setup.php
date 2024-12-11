<?php
function handle_error($errno, $errstr, $errfile, $errline)
{
	$error_string = "An error has occured on line <b>$errline</b> in file <b>$errfile</b>:<br> $errstr";
	header("Location: /first_setup.php?error=" . urlencode($error_string));
	return true;
}

function handle_critical_error()
{
	$error = error_get_last();
	// echo var_dump($error_string);
	$error_line = $error["line"];
	$error_file = $error["file"];
	$error_message = $error["message"];
	$error_string = "An error has occured on line <b>$error_line</b> in file <b>$error_file</b>:<br> $error_message";
	header("Location: /first_setup.php?error=" . urlencode($error_string));
}

set_error_handler("handle_error");
register_shutdown_function("handle_critical_error");

include_once "../chaninfo.php";

// generate crypt key
$key = bin2hex(openssl_random_pseudo_bytes(16));

// shove the key in the path decided by the user
$key_file = fopen($_POST["crypt_key_path"], "w");
fwrite($key_file, $key);

$host = $_POST["database_host"];
$user = $_POST["database_user"];
$password = $_POST["database_password"];

// encrypt our database credentials
$_POST["database_host"] = openssl_encrypt($_POST["database_host"], "aes-256-ecb", $key);
$_POST["database_user"] = openssl_encrypt($_POST["database_user"], "aes-256-ecb", $key);
$_POST["database_password"] = openssl_encrypt($_POST["database_password"], "aes-256-ecb", $key);

if (!is_file(__DIR__ . "/../../first_run"))
	die("The site has already been set up");

$config_template_path = __DIR__ . "/../config.template.php";
$config_path = __DIR__ . "/../config.php";

$config_template_file = fopen($config_template_path, "r");
$config_template = fread($config_template_file, filesize($config_template_path));

foreach ($_POST as $key => $value)
	$config_template = str_replace("%$key%", $value, $config_template);

$config_file = fopen($config_path, "w");
fwrite($config_file, $config_template);
fclose($config_file);

include_once "../database.php";
include_once "../staff_session.php";
include_once "../board.php";

$database = new Database($host, $user, $password);
$database->setup_meta_info_database();

if (!file_exists(__DIR__ . "/../chaninfo.json"))
{
	$chan_info = new ChanInfo();
	$chan_info->chan_name = "DeltaChan";
	$chan_info->welcome = "Welcome to DeltaChan!";
	$chan_info->rules = "Your rules go here.";
	$chan_info->faq = file_get_contents(__DIR__ . "/default_faq.txt");
	$chan_info->default_theme = "yotsuba-blue.css";
	chan_info_write($chan_info);
}

$chan_info = chan_info_read();

function generate_salt($length)
{
	$characters = "abcdefghijklmnopqrtsuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*()";
	$string = "";
	$strlen = strlen($characters);

	for ($i = 0; $i < $length; $i++)
	{
		$string = $string . substr($characters, mt_rand(0, $strlen - 1), 1);
	}

	return $string;
}

if (!isset($chan_info->password_salt))
{
	$chan_info->salt = generate_salt(64);
	chan_info_write($chan_info);
}

// if there are existing staff accounts in the db like when updating skip this step
if (count(get_staff_accounts()) <= 0)
	write_staff_account("admin", staff_hash_password_new("admin"), "admin");

include_once "../update/board.php";
include_once "../update/account.php";
update_add_board_catalogs();
update_account_passwords();
update_boards_nsfw_row();
update_ban_list();

unlink(__DIR__ . "/../../first_run");
header("Location: /index.php");
