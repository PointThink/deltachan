<?php
	include_once "internal/staff_session.php";
	include_once "internal/board.php";

	if (!staff_session_is_valid())
	{
		header("Location: /staff_login.php");
		die();
	}
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
				echo "<a href=/internal/actions/staff/logout.php>Log out</a>";

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
