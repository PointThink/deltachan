<?php
include_once "internal/ui.php";
include_once "internal/staff_session.php";

if (staff_session_is_valid())
{
	header("Location: /staff_dashboard.php");
	die();
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Staff login</title>
		<?php include "internal/link_css.php"; ?>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>

	<body>
		<?php
			include "topbar.php";
		
			if (isset($_GET["result"]))
			{
				if ($_GET["result"] == "success")
				{
					header("Location: /staff_dashboard.php");
					die();
				}
				else if ($_GET["result"] == "invalid_password")
					echo '<script async>alert("Wrong password")</script>';
				else if ($_GET["result"] == "invalid_username")
					echo '<script async>alert("This user does not exist")</script>';

			}
			
			echo "<div class=post_form>";
			(new PostForm("/internal/actions/staff_login.php", "POST"))
				->add_text_field("Username", "username")
				->add_password_field("Password", "password")
				->finalize();
			echo "</div>";

			include "footer.php";
		?>
	</body>
</html>
