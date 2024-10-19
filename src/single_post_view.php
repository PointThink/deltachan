<?php
include_once "internal/database.php";
include_once "internal/board.php";
include_once "internal/ui.php";
include_once "internal/staff_session.php";

$database = new Database();
$post = $database->read_post($board_id, $_GET["id"]);
?>

<!DOCTYPE html>
<html>
	<head>
		<?php
			$database = new Database();
			$board = board_get($board_id);
			echo "<title>/$board->id/ - $post->title</title>";
			
			include "internal/link_css.php";
		?>

		<script src=/internal/post_display.js></script>
	</head>

	<body>
		<?php
		include "topbar.php";
		
		if (isset($_GET["error"]))
				echo "<script>alert('" . $_GET["error"] . "')</script>"
		?>

		<div class="title">
			<?php
				echo "<h2>/$board->id/ - $board->title</h2>";
				echo "<h4>$board->subtitle</h4>";
			?>
		</div>

		<div class=post_form>
			<?php
				if (staff_session_is_valid())
					echo "<p id=staff_disclaimer>Posting as staff</p>";
				echo "<p id=reply_disclaimer>Replying to >$post->id</p>";

				$form = (new PostForm("/internal/actions/post.php", "POST"));

				if (!staff_session_is_valid())
					$form->add_text_field("Name", "name", "Anonymous");
					
				$form
					->add_text_area("Comment", "comment")
					->add_file("File", "file")
					->add_hidden_data("board", "$board_id")
					->add_hidden_data("is_reply", 1)
					->add_hidden_data("replies_to", $post->id)
					->finalize();
			?>
		</div>

		<div id=posts>
			<?php
				$post->display();
			?>
		</div>

		<?php include "footer.php" ?>
	</body>
</html>
