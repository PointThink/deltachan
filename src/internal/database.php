<?php
include_once __DIR__ . "/config.php";
include_once __DIR__ . "/post.php";

class Database
{
	public $mysql_connection;

	public function __construct($host = "", $user = "", $password = "")
	{
		global $deltachan_config;
		$key = file_get_contents($deltachan_config["crypt_key_path"]);

		if ($host == "" || $user == "" || $password == "")
		{
			// decrypt credentials
			$host = openssl_decrypt($deltachan_config["database_host"], "aes-256-ecb", $key);
			$user = openssl_decrypt($deltachan_config["database_user"], "aes-256-ecb", $key);
			$password = openssl_decrypt($deltachan_config["database_password"], "aes-256-ecb", $key);
		}

		$this->mysql_connection = new mysqli($host, $user, $password);
	}

	public function setup_meta_info_database()
	{
		global $deltachan_config;
		// $this->mysql_connection->query("create database if not exists " . $deltachan_config["database_name"] . ";");
		
		$this->query("
			create table if not exists board_info (
				id varchar(255) not null primary key,
				title varchar(255) not null,
				subtitle varchar(255),
				nsfw int default 0 not null
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
		return $this->mysql_connection->real_escape_string($str);
	}

	public function query($str)
	{
		global $deltachan_config;
		$this->mysql_connection->select_db($deltachan_config["database_name"]);
		return $this->mysql_connection->query($str);
	}
}
