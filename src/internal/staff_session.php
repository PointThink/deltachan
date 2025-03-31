<?php
if (session_status() != PHP_SESSION_ACTIVE)
{
	session_set_cookie_params(3600 * 24 * 30); // 30 days
	session_start();
}

include_once "database.php";
include_once "config.php";
include_once "chaninfo.php";

class StaffAccountInfo
{
	public $username;
	public $password_hash;
	public $role;
	public $contact_email;
	public $needs_update;
}

enum LoginResult
{
	case SUCCESS;
	case FAILED_INVALID_USER;
	case FAILED_INVALID_PASSWORD;
}

function staff_hash_password_old($password)
{
	return hash("sha512", $password);
}

function staff_hash_password_new($password)
{
	$chan_info = chan_info_read();
	return hash("sha512", $chan_info->password_salt . $password);
}

function staff_login($username, $password)
{
	$account = read_staff_account($username);

	$password_hash = "";
	if ($account->needs_update)
		$password_hash = staff_hash_password_old($password);
	else
		$password_hash = staff_hash_password_new($password);

	if ($account == NULL)
		return LoginResult::FAILED_INVALID_USER;

	if ($password_hash != $account->password_hash)
		return LoginResult::FAILED_INVALID_PASSWORD;

	$_SESSION["staff_username"] = $username;
	$_SESSION["staff_password_hash"] = $password_hash;
	
	return LoginResult::SUCCESS;
}

function staff_session_is_valid()
{
	if (!isset($_SESSION["staff_username"]) || !isset($_SESSION["staff_password_hash"]))
	{
		staff_logout();
		return false;
	}
	
	$current_user = read_staff_account($_SESSION["staff_username"]);

	if ($current_user == NULL)
	{
		staff_logout();
		return false;
	}

	if ($current_user->password_hash != $_SESSION["staff_password_hash"])
	{
		staff_logout();
		return false;
	}

	return true;
}

function staff_logout()
{
	unset($_SESSION["staff_username"]);
	unset($_SESSION["staff_password_hash"]);
}

function staff_get_current_user()
{
	return read_staff_account($_SESSION["staff_username"]);
}

function staff_is_admin()
{
	return staff_get_current_user()->role == "admin";
}

function staff_is_moderator()
{
	$user = staff_get_current_user();
	return $user->role == "admin" || $user->role == "mod";
}

function staff_is_janny()
{
	$user = staff_get_current_user();
	return $user->role == "janny" || $user->role == "admin" || $user->role == "mod";;
}

function write_staff_account($username, $password_hash, $role, $contact_email = "")
{
	$database = new Database();

	$username = $database->sanitize($username);
	$password_hash = $database->sanitize($password_hash);
	$role = $database->sanitize($role);
	$contact_email = $database->sanitize($contact_email);

	$database->query("
		insert into staff_accounts (
			username, password_hash, role, contact_email, needs_update
		) values (
			'$username', '$password_hash', '$role', '$contact_email', 0
		);
	");
}

function update_staff_account($old_username, $username, $role, $contact_email = "")
{
	$database = new Database();

	$old_username = $database->sanitize($old_username);
	$role = $database->sanitize($role);
	$contact_email = $database->sanitize($contact_email);

	$database->query("
		update staff_accounts
		set username = '$username', role = '$role', contact_email = '$contact_email'
		where username = '$old_username';
	");
}

function update_staff_account_password($username, $password_hash)
{
	$database = new Database();

	$username = $database->sanitize($username);
	$password_hash = $database->sanitize($password_hash);

	$database->query("
		update staff_accounts
		set password_hash = '$password_hash', needs_update = 0
		where username = '$username';
	");
}

function delete_staff_account($username)
{
	$database = new Database();
	$username = $database->sanitize($username);
	$database->query("
		delete from staff_accounts where username = '$username';
	");
}

function read_staff_account($username)
{
	$database = new Database();
	$username = $database->sanitize($username);
	$account_info = new StaffAccountInfo();
	$username = $database->sanitize($username);

	$result = $database->query("
		select * from staff_accounts where username='$username'
	");

	
	if ($result->num_rows <= 0)
		return NULL;

	$account_array = $result->fetch_array();

	$account_info->username = $username;
	$account_info->password_hash = $account_array["password_hash"];
	$account_info->role = $account_array["role"];
	$account_info->contact_email = $account_array["contact_email"];
	$account_info->needs_update = $account_array["needs_update"];

	return $account_info;
}

function get_staff_accounts()
{
	$database = new Database();
	$result = $database->query("
		select username from staff_accounts;
	");

	$accounts = array();

	while ($account = $result->fetch_assoc())
		array_push($accounts, read_staff_account($account["username"]));

	return $accounts;
}
