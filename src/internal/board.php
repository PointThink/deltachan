<?php
include_once "database.php";

function board_create($id, $title, $subtitle = "", $nsfw = 0)
{
	setup_board_table($id);
	board_add_info_row($id, $title, $subtitle, $nsfw);

	if (!is_dir(__DIR__ . "/../$id"))
	{
		mkdir(__DIR__ . "/../$id");

		$index_file = fopen(__DIR__ . "/../$id/index.php", "w");
		fwrite($index_file, "<?php
\$board_id = '$id';
include __DIR__ . '/../board_index.php';");

		fclose($index_file);

		$post_view_file = fopen(__DIR__ . "/../$id/post.php", "w");
		fwrite($post_view_file, "<?php
\$board_id = '$id';
include __DIR__ . '/../single_post_view.php';");

		fclose($post_view_file);

		$catalog_file = fopen(__DIR__ . "/../$id/catalog.php", "w");
        fwrite($catalog_file, "<?php
\$board_id = '$id';
include __DIR__ . '/../catalog.php';
");
	}
}

class Board
{
	public $id;
	public $title;
	public $subtitle;
	public $nsfw;

	public $posts = array();

	public function get_pages_count()
	{
		$database = new Database();
		$query_result = $database->query("select count(*) from posts_$this->id where is_reply = 0;");
		$post_count = intval($query_result->fetch_assoc()["count(*)"]);

		return ceil($post_count / 10.0);
	}
}

// Sets up a database with necesary tables for a board 
function setup_board_table($board_id)
{
	$database = new Database();
	$database->query("
		create table if not exists posts_$board_id (
			id int not null auto_increment primary key,
			is_reply int not null,
			replies_to int,

			creation_time datetime not null default current_timestamp,
			bump_time datetime not null default current_timestamp,

			is_staff_post int not null,
			name varchar(255) not null default 'Anonymous',
			title varchar(255),
			post_body text,
			image_file_name varchar(255),

			poster_ip varchar(255) not null,
			poster_country varchar(2),

			sticky int default 0,
			approved int not null default 0
		);
	");
}

function board_add_info_row($id, $title, $subtitle, $nsfw)
{
	$database = new Database();
	$result = $database->query("
		select * from board_info where id = '$id';
	");

	if ($result->num_rows > 0)
		return;
	$nsfw = intval($nsfw);
	$database->query("
		insert into board_info (
			id, title, subtitle, nsfw
		) values (
			'$id', '$title', '$subtitle', $nsfw
		);
	");
}

// Fetches all the boards on the chan
function board_list()
{
	$database = new Database();
	$query_result = $database->query("select * from board_info;");
	$boards = array();

	while ($board_array = $query_result->fetch_assoc())
	{
		$board = new Board();
		$board->id = $board_array["id"];
		$board->title = $board_array["title"];
		$board->subtitle = $board_array["subtitle"];
		$board->nsfw = $board_array["nsfw"];
		array_push($boards, $board);
	}

	return $boards;
}

function board_edit_info($id, $title, $subtitle, $nsfw)
{
	$database = new Database();
	$nsfw = intval($nsfw);
	$query = "
		update board_info
		set title = '$title', subtitle = '$subtitle', nsfw = $nsfw
		where id = '$id';
	";
	$database->query($query);
}

function board_get($board_id, $page = 0)
{
	$max_pages = 10;
	$database = new Database();

	$board_id = $database->sanitize($board_id);
	$page = $database->sanitize($page);

	$query_result = $database->query("select * from board_info where id = '$board_id';");
	$board_array = $query_result->fetch_array();
	$board = new Board();

	$board->id = $board_array["id"];
	$board->title = $board_array["title"];
	$board->subtitle = $board_array["subtitle"];
	$board->nsfw = $board_array["nsfw"];

	$delete_post_range_begin = 10 * $max_pages;
	
	// delete posts that have been bumped off
	// hacky but works
	$query_result = $database->query("
			select id from posts_$board_id
			where is_reply = 0
			order by sticky desc, bump_time desc
			limit $delete_post_range_begin, 18446744073709551615;
		");

	$posts_to_delete = array();

	while ($post_id = $query_result->fetch_assoc())
	{
		post_delete($board_id, $post_id["id"]);
	}

	$post_range_begin = 10 * intval($page);
	$post_range_end = 10 * intval($page) + 10;

	if ($page >= 0)
	{
		$query_result = $database->query("
			select id from posts_$board_id
			where is_reply = 0
			order by sticky desc, bump_time desc
			limit $post_range_begin, $post_range_end;
		");
	}
	else
	{
		$query_result = $database->query("
			select id from posts_$board_id
			where is_reply = 0
			order by sticky desc, bump_time desc;
		");
	}

	while ($post_id = $query_result->fetch_assoc())
	{
		array_push($board->posts, post_read($database, $post_id["id"], $board_id));
	}

	return $board;
}

function board_remove($board_id)
{	
	$database = new Database();
	$database->query("
		drop table posts_$board_id;
	");
	
	$database->query("
		delete from board_info where id = '$board_id';
	");
}
