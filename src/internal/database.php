<?php
include_once __DIR__ . "/config.php";
include_once __DIR__ . "/post.php";

$key = file_get_contents($deltachan_config["crypt_key_path"]);
$host = openssl_decrypt($deltachan_config["database_host"], "aes-256-ecb", $key);
$user = openssl_decrypt($deltachan_config["database_user"], "aes-256-ecb", $key);
$password = openssl_decrypt($deltachan_config["database_password"], "aes-256-ecb", $key);

$mysql_connection = new mysqli($host, $user, $password);

class Database
{
	public function setup_meta_info_database()
	{		
		$this->query("
			create table if not exists board_info (
				id varchar(255) not null primary key,
				title varchar(255) not null,
				subtitle varchar(255),
				nsfw int default 0 not null,
				ids int default 0 not null
			);
		");

		$this->query("
			create table if not exists staff_accounts (
				username varchar(30) not null primary key,
				password_hash varchar(128) not null,
				role varchar(128) not null,
				contact_email varchar(128),
				needs_update int default 0 not null
			);
		");

		$this->query("
			create table if not exists bans (
				ip varchar(255) not null primary key,
				reason text not null,
				date datetime not null default current_timestamp,
				duration int not null default 0,
				banned_by varchar(30) default null
			);
		");

		$this->query("
			create table if not exists reports (
				report_id int not null auto_increment primary key,
				report_date datetime not null default current_timestamp,
				reporter_ip varchar(255) not null, 
				reported_post_id int not null,
				reported_post_board varchar(255) not null,
				report_reason text not null
			);
		");
	}

	public function sanitize($str)
	{
		global $mysql_connection;
		return $mysql_connection->real_escape_string($str);
	}

	public function query($str)
	{
		global $deltachan_config;
		global $mysql_connection;
		$mysql_connection->select_db($deltachan_config["database_name"]);
		return $mysql_connection->query($str);
	}

	public function get_unix_time($str)
	{
		$str = $this->sanitize($str);
		$result = $this->query("select UNIX_TIMESTAMP('$str');");
		return intval($result->fetch_assoc()["UNIX_TIMESTAMP('$str')"]);
	}

	public function insert_id()
	{
		global $mysql_connection;
		return $mysql_connection->insert_id;
	}
}
