<?php
include_once "../../database.php";
include_once "../../staff_session.php";
include_once "../../ui.php";

if (!staff_session_is_valid() || !staff_is_admin()) 
	die("You are not allowed here");

if (count($_POST) > 0)
{
	echo var_dump($_POST);
	$database = new Database();

    update_staff_account(
        $_POST["old_username"],
        $_POST["username"],
        $_POST["role"],
        $_POST["contact_email"]
    );

    if (isset($_POST["password"]) && $_POST["password"] != "")
        update_staff_account_password($_POST["username"], staff_hash_password_new($_POST["password"]));

	header("Location: /internal/staff_forms/manage_accounts.php");
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Editing account</title>
		<?php include "../../link_css.php" ?>
	</head>

	<body>
		<?php include "../../../topbar.php" ?>

		<h1 class="title">Edit account</h1>
		<?php
			$database = new Database();
			$user = read_staff_account($_GET["username"]);
		?>

		<div class=post_form>
			<?php
			(new PostForm("", "POST"))
				->add_text_field("Username", "username", $user->username)
                ->add_password_field("New password (optional)", "password")
                ->add_text_field("Contact email (Optional)", "contact_email", $user->contact_email)
                ->add_dropdown("Role", "role", array(
					"admin",
					"mod",
					"janny"), $user->role)
                ->add_hidden_data("old_username", $_GET["username"])
				->finalize();
			?>
		</div>

		<br>

		<?php echo "<a class=manage_link href=/internal/actions/staff/delete_account.php?username=" . $_GET["username"] . ">Delete account</a>" ?>

		<?php include "../../../footer.php" ?>
	</body>
</html>
