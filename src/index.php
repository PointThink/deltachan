<?php
	if (!is_file("internal/config.php"))
	{
		header("Location: /first_setup.php");
		die();
	}

	include_once "internal/board.php";
	include_once "internal/bans.php";

	$database = new Database();

include_once "internal/locale.php";
?>

<!DOCTYPE html>
<html>
	<head>
		<?php
		include_once "internal/chaninfo.php"; 
	
		$chan_info = chan_info_read();
		echo "<title>$chan_info->chan_name</title>";

		include "internal/link_css.php";
		?>
		<link rel="icon" href="/static/favicon.png">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>

	<body>
		<?php
			include "topbar.php";
		?>

		<div class="list">
			<?php echo "<h3 class=\"list_title\">" . localize("welcome") . " $chan_info->chan_name</h3>" ?>
			<div class="list_content">
				<?php
					echo "<pre>$chan_info->welcome</pre>"
				?>
			</div>
		</div>

		<br>

		<?php 
			include "board_list_detail.php"
		?>
		
		<br>
		<?php include "index_recent_images.php" ?><br>
		<?php include "index_stats.php" ?>
		

		<?php include "footer.php"; ?>
	</body>
</html>
