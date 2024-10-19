<?php
include_once "../../database.php";
include_once "../../staff_session.php";
include_once "../../ui.php";
include_once "../../chaninfo.php";

if (!staff_session_is_valid() || !staff_is_admin()) 
	die("You are not allowed here");

if (count($_POST) > 0)
{
    $chan_info = new ChanInfo();
    $chan_info->chan_name = $_POST["name"];
    $chan_info->rules = $_POST["rules"];
    $chan_info->welcome = $_POST["welcome"];
	$chan_info->faq = $_POST["faq"];

    chan_info_write($chan_info);

	header("Location: /staff_dashboard.php");
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Editing imageboard info</title>
		<?php include "../../link_css.php" ?>
	</head>

	<body>
		<?php include "../../../topbar.php" ?>

		<h1 class="title">Setup imageboard</h1>
		<?php
			$database = new Database();
            $chan_info = chan_info_read();
		?>

		<div class=post_form>
			<?php
			(new PostForm("/internal/actions/staff/chan_setup.php", "POST"))
                ->add_text_field("Chan name", "name", $chan_info->chan_name)
                ->add_text_area("Welcome message", "welcome", $chan_info->welcome)
                ->add_text_area("Rules", "rules", $chan_info->rules)
				->add_text_area("FAQ", "faq", $chan_info->faq)
				->finalize();
			?>
		</div>

		<br>

		<?php include "../../../footer.php" ?>
	</body>
</html>

