<!DOCTYPE html>
<html>
	<head>
		<?php
		include "internal/chaninfo.php"; 
	
		$chan_info = chan_info_read();
		echo "<title>$chan_info->chan_name FAQ</title>";

		include "internal/link_css.php";
		?>
	</head>

	<body>
		<?php
			include "topbar.php";
		?>

		<div class=list>
			<h3 class=list_title>Frequently asked questions</h3>
			<div class=list_content>
			<?php
                echo "<p>$chan_info->faq</p>"
			?>
			</div>
		</div>

		<?php include "footer.php"; ?>
	</body>
</html>
