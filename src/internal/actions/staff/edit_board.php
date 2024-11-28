<?php
include_once "../../board.php";
include_once "../../staff_session.php";
include_once "../../ui.php";

if (!staff_session_is_valid() || !staff_is_admin()) 
	die("You are not allowed here");

if (count($_POST) > 0)
{
	board_edit_info($_POST["id"], $_POST["title"], $_POST["subtitle"], isset($_POST["nsfw"]));
	header("Location: /internal/staff_forms/manage_boards.php");
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Editing board</title>
		<?php include "../../link_css.php" ?>
	</head>

	<body>
		<?php include "../../../topbar.php" ?>

		<h1 class="title">Editing board</h1>
		<?php
			$board = board_get($_GET["id"]);
		?>

		<div class=post_form>
			<?php
			(new PostForm("", "POST"))
				->add_text_field("Title", "title", $board->title)
				->add_text_field("Subtitle", "subtitle", $board->subtitle)
				->add_hidden_data("id", $board->id)
				->add_checkboxes("Options", array("NSFW" => "nsfw"))
				->finalize();
			?>
		</div>

		<br>

		<?php echo "<a class=manage_link href=/internal/actions/staff/delete_board.php?id=" . $_GET["id"] . ">Delete board</a>" ?>

		<?php include "../../../footer.php" ?>
	</body>
</html>
