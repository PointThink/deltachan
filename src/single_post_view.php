<?php
include_once "internal/database.php";
include_once "internal/board.php";
include_once "internal/ui.php";
include_once "internal/staff_session.php";
include_once "internal/bans.php";

$database = new Database();
$post = post_read($database, $_GET["id"], $board_id);

if ($post == null)
{
	header("Location: /internal/error_pages/error.php?message=The thread you're looking for does not exist");
}

if ($post->is_reply)
{
	header("Location: /$post->board/post.php?id=$post->replies_to");
	die();
}
?>

<!DOCTYPE html>
<html>
	<head>
		<?php
			$database = new Database();
			$board = board_get($board_id);
			echo "<title>/$board->id/ - $post->title</title>";
			
			echo "<link rel=stylesheet type=text/css href=/internal/theme.php?nsfw=$board->nsfw>";
		?>

		<script src=/internal/post_display.js defer></script>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" defer></script>
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
			<button class="form_button link_button js_only" onclick="expand_post_field();">Create new reply</button>
		</div>

		<div class=post_form>
			<div class="form_topbar">
				<?php echo "<b>Replying to >>$post->id</b>"?>
				<button onclick="hide_post_field();" class="js_only">Close</button>
			</div>

			<?php
				echo "<h3 id=reply_disclaimer></h3>";
				if (!is_user_banned())
				{
					if (staff_session_is_valid() && staff_is_janny())
						echo "<p id=staff_disclaimer>Posting as staff</p>";

					$form = (new PostForm("/internal/actions/post.php", "POST"));

					if (!staff_is_janny())
						$form->add_text_field("Name", "name", "Anonymous");
					
					$reply_field_content = "";
					if (isset($_GET["reply_field_content"]))
						$reply_field_content = urldecode($_GET["reply_field_content"]);

					$form
						->add_text_area("Comment", "comment", $reply_field_content)
						->add_captcha("Captcha", "turnslite")
						->add_file("File", "file")	
						->add_checkboxes("Options", array("Sage!" => "sage"))
						->add_hidden_data("board", "$board_id")
						->add_hidden_data("is_reply", 1)
						->add_hidden_data("replies_to", $post->id)
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

		<div id=posts>
			<div class="thread_actions">
			<?php
				echo "<hr>";
				
				echo "<a href=/$board->id>[Return]</a>";
				echo "<a href=/$board->id/catalog.php>[Catalog]</a>";
				echo "<a href=# onclick=\"window.scrollTo(0, document.body.scrollHeight); return false;\">[Bottom]</a>";

				echo "<hr></div>";

				$post->display(false, false, false, false, true);
			?>
			
		</div>

		<div class="center">
			<button class="form_button link_button js_only" onclick="expand_post_field();">Create new reply</button>
		</div>

		<?php include "footer.php" ?>
	</body>
</html>
