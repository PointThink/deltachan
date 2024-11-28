
<!DOCTYPE html>
<html>
	<head>
		<title>DeltaChan setup</title>
		<?php include "internal/link_css.php" ?>
	</head>

	<body>
		<div id="setup_page">
		<?php
			if (isset($_GET["error"]) && file_exists("first_run"))
				echo "<p class=setup_error>" . $_GET["error"] . "</p><br>";
			if (!file_exists("first_run"))
				header("Location: /");
		?>
		<form method=POST action=/internal/actions/first_setup.php>
			<h1>Welcome to DeltaChan!</h1>
			<p>This page will help you set up your imageboard.</p>
			<br>

			<h2>Database info</h2>
			<p>DeltaChan uses a MySQL database to store all data.</p>
			<p>Please give credentials to a MySQL account for DeltaChan to use.</p>
			<br>

			<label>Host</label><input type=text name=database_host><br>
			<label>DB Name</label><input type=text name=database_name><br>
			<label>User</label><input type=text name=database_user><br>
			<label>Password</label><input type=password name=database_password><br>
			<br>

			<h2>Secure storage</h2>
			<p>Select a path to a file outside of your web root to store secrets.</p>
			<label>Directory</label><input type=text name=crypt_key_path><br>

			<h2>Accounts (<div class=important>Important!</div>)</h2>
			<p>DeltaChan will create a default admin account with <b>username "admin" and password "admin"</b>.</p>
			<?php echo "<p>You can log in at <b>" . $_SERVER["HTTP_HOST"] . "/staff_login.php</b></p>" ?>
			<p>After logging in create a new account with admin privilages and remove the default account</p>
			<br>

			<button type=submit>Done</button>
		</form>
		</div>
	</body>
</html>
