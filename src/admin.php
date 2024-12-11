<?php
include_once "internal/staff_session.php";

if (isset($_GET["action"]))
{
    // login
    if ($_GET["action"] == "login")
    {
        $status = staff_login($_POST["username"], $_POST["password"]);

        $status_str = "";

        switch ($status)
        {
            case LoginResult::SUCCESS: $status_str = "success"; break;
            case LoginResult::FAILED_INVALID_USER: $status_str = "invalid_username"; break;
            case LoginResult::FAILED_INVALID_PASSWORD: $status_str = "invalid_password"; break;
        }

        header("Location: /" . basename(__FILE__) . "?result=$status_str");
    }

    // logout
    if ($_GET["action"] == "logout")
    {
        echo "Logout!";
        staff_logout();
        header("Location: /" . basename(__FILE__));
    }
}

// login screen
if (!staff_session_is_valid())
{
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
					header("Location: /admin.php");
					die();
				}
				else if ($_GET["result"] == "invalid_password")
					echo '<script async>alert("Wrong password")</script>';
				else if ($_GET["result"] == "invalid_username")
					echo '<script async>alert("This user does not exist")</script>';

			}
			
			echo "<div class=post_form>";
			(new PostForm("/admin.php?action=login", "POST"))
				->add_text_field("Username", "username")
				->add_password_field("Password", "password")
				->finalize();
			echo "</div>";

			include "footer.php";
		?>
	</body>
</html>
    <?php
}
// dashboard
else
{
    ?>
<!DOCTYPE html>
<html>
	<head>
		<title>Staff dashboard</title>
		<?php include "internal/link_css.php"; ?>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>

	<body>
		<?php include "topbar.php"; ?>

		<div id=staff_dashboard_title>
			<h1>Staff dashboard</h2>

			<?php
				$current_user = staff_get_current_user();
				echo "<h4>Logged in as $current_user->username</h4>";
				echo "<a href=/admin.php?action=logout>Log out</a>";

				if ($current_user->needs_update)
				{
					echo "<p class=password_warning>Warning: Your account is currently using an insecure password storage method. Please change your password to update it</p>";
				}	
			?>
		</div>

		<br>

		<div class=list>
			<?php
				$database = new Database();

				
				echo "<h4 class=list_title>Boards</h4>";
				echo "<div class=list_content><ul>";

				foreach (board_list() as $b)
					echo "<li><a href=/$b->id/>/$b->id/ - $b->title</a></li>";

				echo "</ul></div>";
				

				if ($current_user->role == "admin")
				{
					echo "<h4 class=list_title>Admin actions</h4>
					<div class=list_content>
						<ul>
							<li><a href=/internal/actions/staff/chan_setup.php>Setup imageboard</a></li>
							<li><a href=/internal/staff_forms/manage_accounts.php>Manage staff accounts</a></li>
							<li><a href=/internal/staff_forms/manage_boards.php>Manage boards</a></li>
						</ul>
					</div>";
				}

				if (staff_is_moderator())
				{
					echo '
					<h4 class=list_title>Moderator actions</h4>
					<div class="list_content">
						<ul>
							<li><a href=/internal/staff_forms/approve_posts.php>View unnaproved posts</a></li>
							<li><a href=/internal/staff_forms/view_reports.php>View reported posts</a></li>
							<li><a href=/internal/staff_forms/manage_bans.php>Manage bans</a></li>
						</ul>
					</div>';
				}
			?>
		</div>

		<?php include "footer.php" ?>
	</body>
</html>
<?php
}