<?php
include_once "../../database.php";
include_once "../../board.php";
include_once "../../staff_session.php";
include_once "../../ui.php";

if (!staff_session_is_valid() || !staff_is_admin()) 
	die("You are not allowed here");

if (count($_POST) > 0)
{
	$board = $_POST["id"];
	$database = new Database();

	// delete all posts from the board
	
	foreach (board_get($board)->posts as $post)
		post_delete($database, $board, $post->id);

	board_remove($board);

	unlink(__DIR__ . "/../../../$board/post.php");
	unlink(__DIR__ . "/../../../$board/index.php");
	unlink(__DIR__ . "/../../../$board/catalog.php");
	rmdir(__DIR__ . "/../../../$board");	

	header("Location: /internal/staff_forms/manage_boards.php");
	die();
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

		<?php
		echo "<h1 class=title>Are sure you want to delete /" . $_GET["id"] . "/</h1>";
		echo "<h4 class=title>This action cannot be reverted</h4>";
		?>

		<form action="" class=title method=POST>
			<?php echo "<input type=hidden name=id value=" . $_GET["id"] . ">" ?>
			<button type=submit>Yes</button>
		</form>
		
		<?php include "../../../footer.php" ?>
	</body>
</html>
