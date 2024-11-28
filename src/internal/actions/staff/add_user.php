<?php
include_once "../../ui.php";
include_once "../../staff_session.php";
include_once "../../database.php";

if (!staff_session_is_valid() || !staff_is_admin())
	die("You aren't allowed here!");

if (count($_POST) > 0)
{
	write_staff_account(
		$_POST["username"],
		staff_hash_password_new($_POST["password"]),
		$_POST["role"],
		$_POST["contact_email"]
	);

	header("Location: /internal/staff_forms/manage_accounts.php");
	die();
}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>Add account</title>
		<?php include "../../link_css.php"; ?>
	</head>

	<body>
		<?php include "../../../topbar.php"; ?>

		<h1 class=title>Add account</h1>
		<div class=post_form>
			<?php
			(new PostForm("", "POST"))
				->add_text_field("Username", "username")
				->add_password_field("Password", "password")
				->add_text_field("Contact email (optional)", "contact_email")
				->add_dropdown("Role", "role", array(
					"admin",
					"mod",
					"janny"), "admin")
				->finalize();
			?>
		</div>

		<?php include "../../../footer.php"; ?>
	</body>
</html>
