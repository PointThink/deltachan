<?php
if (session_status() != PHP_SESSION_ACTIVE)
{
	session_set_cookie_params(3600 * 24 * 30); // 30 days
	session_start();
}

include_once "database.php";
include_once __DIR__ . "/report.php";
include_once "locale.php";
include_once "ui.php";

class Post
{
	public $id;
	public $board;
	public $is_reply;
	public $replies_to;
	
	public $creation_time;
	public $bump_time;

	public $name;
	public $title;
	public $body;
	public $image_file;

	public $poster_ip;
	public $poster_country;

	public $is_staff_post;

	public $approved;
	public $sticky;

	public $replies = array();

	public function display_attachment()
	{
		$mime_type = mime_content_type(__DIR__ . "/../$this->image_file");
		$base_name = basename($this->image_file);

		if (str_starts_with($mime_type, "video"))
			echo "<video class=post_attachment controls preload=metadata src='/$this->image_file'></video>";
		else if (str_starts_with($mime_type, "image"))
		{
			$file_parts = explode(".", $this->image_file);
			$thumb_file_name = $file_parts[0] . "-thumb.webp";
			echo "<img class=post_attachment id=post_image_$this->id
				src='/$thumb_file_name' onclick='expand_image($this->id)'
				full_size_image=/$this->image_file
				thumbnail_image=/$thumb_file_name
			>";
		}
		else
			echo "<a class=post_attachment_non_image href='/$this->image_file'><img class=post_attachment_non_image alt='attachment' src='/attachment.svg'>$base_name</a>";
	}

	public function format_and_show_text($text)
	{
		$text = htmlspecialchars($text);

		$text = preg_replace('#(script|about|applet|activex|chrome):#is', "\\1:", $text);

		$ret = ' ' . $text;
	
		// Replace Links with http://
		$ret = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"\\2\" target=\"_blank\" rel=\"nofollow\">\\2</a>", $ret);
	
		// Replace Links without http://
		$ret = preg_replace("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"http://\\2\" target=\"_blank\" rel=\"nofollow\">\\2</a>", $ret);

		// Replace Email Addresses
		$ret = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);
		$ret = substr($ret, 1);

		// bold
    	$ret = preg_replace('/\*\*(.+)\*\*/sU', '<b>$1</b>', $ret);
    	// italic
	    $ret = preg_replace('/\*(.+)\*/sU', '<i>$1</i>', $ret);

		preg_match_all('/&gt;&gt;[0-9]+/', $ret, $matches, PREG_OFFSET_CAPTURE);
		
		foreach ($matches[0] as $match)
		{
			$match_string = $match[0];
			$id = intval(substr($match[0], 8));
			$format = ">>$id ";

			if (isset($_SESSION["users_posts"]))
			{
				if (in_array($id, $_SESSION["users_posts"]))
					$format .= "(You)";
				if ($id == strval($this->replies_to))
					$format .= "(OP)";
			}

			$ret = preg_replace("/$match_string/", "<a href=# onclick=scroll_to_post('$id')>$format</a>", $ret);
		}

		$textParts = explode("\n", $ret);

		echo "<div class=post_text>\n";
    	foreach ($textParts as $part)
    	{
        	if (str_starts_with($part, "&gt"))
            	echo "<pre class='greentext'>$part</pre>";
        	else if (str_starts_with($part, "&lt"))
            	echo "<pre class='orangetext'>$part</pre>";
        	else if (str_starts_with($part, "^"))
            	echo "<pre class='bluetext'>$part</pre>";
        	else
            	echo "<pre>$part</pre>";
    	}
    	echo "</div>\n";
	}

	public function display($show_hide_replies_button = false, $report_mode = false, $report_view_mode = false)
	{
		if (!$this->is_reply || $report_mode || $report_view_mode)
			echo "<div class=post id=post_$this->id>";
		else
			echo "<div class=reply id=post_$this->id>";

		if ($this->image_file != "")
			if ($report_mode)
			{
				echo "<div class=report_attachment>";
				$this->display_attachment();
				echo "</div>";
			}
			else
				$this->display_attachment();

		if ($this->sticky)
			echo "<img class=pin src=/pin.png>";


		echo "<span class=name_segment>";

		if ($this->title != "")
			echo "<p class=post_title>$this->title</p>";	

		if (!$this->is_staff_post)
			echo "<p class=name>$this->name</p>";
		else
		{
			$role = read_staff_account($this->name)->role;
			echo "<p class='name staff_name'><b>$this->name</b> ## $role</p>";
		}

		if	(isset($_SESSION["user_posts"]) && $_SESSION["users_posts"] != NULL)
			if (in_array($this->id, $_SESSION["users_posts"]))
				echo "<p class=your_post>(You)</p>";
	
		echo "</span>";

		if (!$this->is_reply)
			echo "<a class=post_id href=/$this->board/post.php?id=$this->id>>>$this->id | $this->creation_time</a>";
		else
			echo "<p class=post_id>>>$this->id | $this->creation_time</p>";

		(new ActionLink("/internal/actions/report.php", "report_$this->id", localize("post_report"), "GET"))
			->add_data("id", $this->id)
			->add_data("board", $this->board)
			->finalize();

		if ($this->is_reply)
		{
			$quote_content = "";
			foreach (explode("\n", $this->body) as $line)
				$quote_content .= ">$line\n";

			(new ActionLink("/$this->board/post.php", "quote_$this->id", localize("post_quote"), "GET"))
				->add_data("id", $this->replies_to)
				->add_data("reply_field_content", urlencode($quote_content))
				->finalize();

			(new ActionLink("/$this->board/post.php", "reply_$this->id", localize("post_reply"), "GET"))
				->add_data("id", $this->replies_to)
				->add_data("reply_field_content", urlencode(">>$this->id"))
				->finalize();
		}

		if (staff_session_is_valid())
		{
			(new ActionLink("/internal/actions/staff/delete_post.php", "delete_$this->id", "Delete"))
				->add_data("board", $this->board)
				->add_data("id", $this->id)
				->finalize();
		}

		if (staff_session_is_valid() && staff_is_moderator())
		{
			(new ActionLink("/internal/actions/staff/ban.php", "ban_$this->id", "Ban", "GET"))
				->add_data("ip", $this->poster_ip)
				->finalize();

			if (!$this->approved)
			{
				(new ActionLink("/internal/actions/staff/approve_post.php", "approve_$this->id", "Approve"))
					->add_data("board", $this->board)
					->add_data("id", $this->id)
					->finalize();
			}

			if (!$this->is_reply)
			{
				if ($this->sticky)
				{
					(new ActionLink("/internal/actions/staff/sticky_post.php", "approve_$this->id", "Unstick"))
						->add_data("board", $this->board)
						->add_data("id", $this->id)
						->finalize();
				}
				else
				{
					(new ActionLink("/internal/actions/staff/sticky_post.php", "approve_$this->id", "Sticky"))
						->add_data("board", $this->board)
						->add_data("id", $this->id)
						->finalize();
				}
			}
		}

		echo "<div class=post_comment>";
		$this->format_and_show_text($this->body);
		echo "</div>";

		if (count($this->replies) > 0 & $show_hide_replies_button)
			echo "<a href='#' class=hide_replies_button id=hide_replies_$this->id onclick='hide_replies(\"$this->id\")'>" . localize("post_replies_hide") . "</a>";

		if (!$report_mode && !$report_view_mode)
		{
			echo "<div id=replies_$this->id>";
			foreach ($this->replies as $reply)
				$reply->display(true);
			echo "</div>";

		}

		echo "</div>";
	}

	public function display_catalog()
	{
		echo "<div href=/$this->board/?post=$this->id class=catalog_post>";

		if ($this->image_file)
			$this->display_attachment();
		
		echo "<a href=/$this->board/post.php?id=$this->id>>>$this->id ";
		echo "r: " . count($this->replies) . "</a>";

		echo "<br>";

		if ($this->title != "")
			echo "<b>$this->title</b>";

		echo "<div class=post_comment>";
		$this->format_and_show_text($this->body);
		echo "</div>"; 

		echo "</div>";
	}
}

function post_delete($board, $id)
{
	$database = new Database();
	
	// first delete the file
	$post = $database->read_post($board, $id);
	$file_parts = explode(".", $post->image_file);
	$thumbnail_path = $file_parts[0] . "-thumb.webp";
	unlink(__DIR__ . "/../$post->image_file");
	unlink(__DIR__ . "/../$thumbnail_path");	
	
	$database->remove_post($board, $id);
	report_delete_for_post($board, $id);

	foreach ($post->replies as $reply)
		delete_post($reply->id);
}
