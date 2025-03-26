<?php
include_once "../../database.php";
include_once "../../staff_session.php";
include_once "../../ui.php";

function error_die($error)
{	
	header("Location: /internal/error_pages/error.php?message=" . urlencode($error));
	die();
}

if (!staff_session_is_valid()) 
	die("You are not allowed here");

if (count($_POST) > 0)
{
    $account = staff_get_current_user();
    if ($account->needs_update || staff_hash_password_new($_POST["old_password"]) == $account->password_hash)
    {
        update_staff_account_password($account->username, staff_hash_password_new($_POST["new_password"]));
    }
    else
    {
        error_die("Invalid old password");
    }

	header("Location: /admin.php");
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Changing password</title>
		<?php include "../../link_css.php" ?>
	</head>

	<body>
		<?php include "../../../topbar.php" ?>

		<h1 class="title">Change password</h1>
		<?php
			$database = new Database();
			$user = read_staff_account($_GET["username"]);
		?>

		<div class=post_form>
			<?php
			(new PostForm("", "POST"))
                ->add_text_field("Old password", "old_password")
                ->add_text_field("New password", "new_password")
				->finalize();
			?>
		</div>

		<br>

		<?php echo "<a class=manage_link href=/internal/actions/staff/delete_account.php?username=" . $_GET["username"] . ">Delete account</a>" ?>

		<?php include "../../../footer.php" ?>
	</body>
</html>
