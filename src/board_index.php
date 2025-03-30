<?php
include_once "internal/board.php";
include_once "internal/staff_session.php";
include_once "internal/ui.php";
include_once "internal/bans.php";
include_once "internal/post.php";

function show_pages()
{
	echo "<div class=board_pages>Pages: ";
	global $board;	
	$page_count = $board->get_pages_count();

	$page = 0;
	if (isset($_GET["p"]))
		$page = $_GET["p"];

	for ($i = 0; $i < $page_count; $i++)
	{
		if ($i == $page)
			echo "<a class=selected_page href='?p=$i'>$i</a>";
		else
			echo "<a href='?p=$i'>$i</a>";
	}

	echo "<a class=catalog_link href=/$board->id/catalog.php>" . localize("catalog") . "</a>";

	echo "</div>";
}
?>

<!DOCTYPE html>
<html>
	<head>
		<?php
			$database = new Database();

			$page = 0;
			if (isset($_GET["p"]))
				$page = $_GET["p"];

			$board = board_get($board_id);
			echo "<title>/$board->id/ - $board->title</title>";
		
			echo "<link rel=stylesheet type=text/css href=/internal/theme.php?nsfw=$board->nsfw>";
		?>
		<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" defer></script>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="icon" href="/static/favicon.png">
	</head>

	<body>
		<?php
			include "topbar.php";
		?>

		<div class="title">
			<?php
				echo "<h2>/$board->id/ - $board->title</h2>";
				echo "<h4>$board->subtitle</h4>";
			?>
		</div>

		<div class="center">
			<button class="form_button link_button js_only" onclick="expand_post_field();">Create new thread</button>
		</div>

		<div class=post_form>
			<div class="form_topbar">
				<b>Creating new thread</b>
				<button onclick="hide_post_field();" class="js_only">Close</button>
			</div>

			<?php
				if (staff_session_is_valid() && staff_is_janny())
					echo "<p id=staff_disclaimer>Posting as staff</p>";

				if (!is_user_banned())
				{
					$form = (new PostForm("/internal/actions/post.php", "POST"));

					if (!staff_is_janny())
						$form->add_text_field("Name", "name", "Anonymous");
						
					$form
						->add_text_field("Title", "title")
						->add_text_area("Comment", "comment")
						->add_hidden_data("board", "$board_id")
						->add_captcha("Captcha", "turnslite")
						->add_file("File", "file")
						->finalize();

					echo "<p class=rules_disclaimer>Remember to follow the <a href=/rules.php>rules</a></p>";
				}
				else
				{
					echo "You cannot post because you have been banned!<br>";
					echo "<a href=/internal/error_pages/ban.php>Learn more</a>";
				}
			?>
		</div>

		<?php show_pages(); ?>

		<div id="posts">
			<?php
				$sticky_posts = array();
				$posts = array();
				$posts_unsorted = $board->get_posts($page);

				foreach ($posts_unsorted as $post)
				{
					if ($post->sticky)
						array_push($sticky_posts, $post);
					else
						array_push($posts, $post);
				}
				
				foreach ($sticky_posts as $post)
				{
					echo "<hr>";
					$post->display(true);
				}

				foreach ($posts as $post)
				{
					echo "<hr>";
					$post->display(true);
				}
			?>	
		</div>

		<?php show_pages(); ?>

		<?php include "footer.php" ?>
	</body>
</html>
