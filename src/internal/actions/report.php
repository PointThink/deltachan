<?php
include_once "../ui.php";
include_once "../database.php";
include_once "../report.php";

if (report_exists($_GET["board"], $_GET["id"], $_SERVER["REMOTE_ADDR"]))
{
	header("Location: " . $_SERVER["HTTP_REFERER"] . "?error=You have already reported this post");
	die();
}

if (count($_POST) > 0)
{
	$report = new Report();
	$report->reporter_ip = $_SERVER["REMOTE_ADDR"];
	$report->post_id = $_POST["id"];
	$report->post_board = $_POST["board"];
	$report->reason = $_POST["reason"];
	$report->write();

	echo "Reported post<br>Press <a href=/>here</a> to return to index";
	die();
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Reporting post</title>
		<?php include "../link_css.php" ?>
	</head>

	<body>
		<?php include "../../topbar.php" ?>

		<?php
			echo "<h1 class=title>Reporting post</h1>";

			$database = new Database();
			$post = post_read($database, $_GET["id"], $_GET["board"]);
			echo "<div id=posts>";
			$post->display(false, true);
			echo "</div>";
		?>

		<div class=post_form>
			<?php
			(new PostForm("", "POST"))
				->add_text_area("Reason", "reason")
				->add_hidden_data("id", $_GET["id"])
				->add_hidden_data("board", $_GET["board"])
				->finalize();
			?>
		</div>

		<?php include "../../footer.php" ?>
	</body>
</html>
