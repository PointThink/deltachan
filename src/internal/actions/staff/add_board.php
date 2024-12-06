<?php
include_once "../../board.php";
include_once "../../ui.php";
include_once "../../staff_session.php";

if (!staff_session_is_valid() || !staff_is_admin()) 
	die("You are not allowed in here");

if (count($_POST) > 0)
{
	board_create($_POST["id"], $_POST["title"], $_POST["subtitle"], isset($_POST["nsfw"]));
	header("Location: /" . $_POST["id"] . "/");
	die();
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>New board</title>
		<?php include "../../link_css.php" ?>
	</head>

	<body>
		<?php include "../../../topbar.php" ?>

		<h1 class=title>Creating new board</h1>

		<div class=post_form>
		<?php
			(new PostForm("", "POST"))
				->add_text_field("ID", "id")
				->add_text_field("Title", "title")
				->add_text_field("Subtitle", "subtitle")
				->add_checkboxes("Options", array("NSFW" => "nsfw"))
				->finalize();
		?>
		</div>

		<?php include "../../../footer.php" ?>
	</body>
</html>
