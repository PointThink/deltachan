<?php
session_start();

include_once "database.php";

class StaffAccountInfo
{
	public $username;
	public $password_hash;
	public $role;
	public $contact_email;
}

enum LoginResult
{
	case SUCCESS;
	case FAILED_INVALID_USER;
	case FAILED_INVALID_PASSWORD;
}

function staff_login($username, $password_hash)
{
	$account = read_staff_account($username);

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

function write_staff_account($username, $password_hash, $role, $contact_email = "")
{
	$database = new Database();
	$database->query("
		insert into staff_accounts (
			username, password_hash, role, contact_email
		) values (
			'$username', '$password_hash', '$role', '$contact_email'
		);
	");
}

function update_staff_account($old_username, $username, $role, $contact_email = "")
{
	$database = new Database();
	$database->query("
		update staff_accounts
		set username = '$username', role = '$role', contact_email = '$contact_email'
		where username = '$old_username';
	");
}

function update_staff_account_password($username, $password_hash)
{
	$database = new Database();
	$database->query("
		update staff_accounts
		set password_hash = '$password_hash'
		where username = '$username';
	");
}

function delete_staff_account($username)
{
	$database = new Database();
	$database->query("
		delete from staff_accounts where username = '$username';
	");
}

function read_staff_account($username)
{
	$database = new Database();
	$account_info = new StaffAccountInfo();
	$username = $database->mysql_connection->real_escape_string($username);

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