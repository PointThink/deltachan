<?php
include_once "internal/board.php";
include_once "internal/staff_session.php";
?>
<!DOCTYPE html>
<html>
	<head>
		<?php
		include "internal/chaninfo.php"; 
	
		$chan_info = chan_info_read();
		echo "<title>$chan_info->chan_name rules</title>";

		include "internal/link_css.php";
		?>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>

	<body>
		<?php
			include "topbar.php";
		?>

		<div class=list>
			<h3 class=list_title>Rules</h3>
			<div class=list_content>
			<?php
                echo "<pre>$chan_info->rules</pre>"
			?>
			</div>
		</div>

		<?php include "footer.php"; ?>
	</body>
</html>
