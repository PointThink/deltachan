<?php
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

// if there are existing staff accounts in the db like when updating skip this step
if (count(get_staff_accounts()) <= 0)
	write_staff_account("admin", hash("sha512", "admin"), "admin");

if (!file_exists(__DIR__ . "../chaninfo.json"))
{
	$chan_info = new ChanInfo();
	$chan_info->chan_name = "DeltaChan";
	$chan_info->welcome = "Welcome to DeltaChan!";
	$chan_info->rules = "Your rules go here.";
	$chan_info->faq = file_get_contents(__DIR__ . "/default_faq.txt");
	chan_info_write($chan_info);
}

unlink(__DIR__ . "/../../first_run");
header("Location: /index.php");
