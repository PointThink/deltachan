<?php
include_once "internal/board.php";
include_once "internal/staff_session.php";
$chan_info = chan_info_read();
?>
<div id=topbar>
	<?php
		if (staff_session_is_valid())
		{
			echo "<p>[</p>";
			echo "<p>Logged in as " . staff_get_current_user()->username . "</p>";
			echo "<a href=/admin.php?action=logout>Logout</a>";
			echo "<a href=/admin.php>Dashboard</a>";
			echo "<p>]</p>";
		}
	?>

	<p>[</p>
	<a href=/>home</a><p>•</p>
	<a href=/settings.php>settings</a><p>•</p> 
	<a href=/rules.php>rules</a><p>•</p>
	<a href=/faq.php>faq</a>
	<?php
		if ($chan_info->show_ban_list)
			echo "<p>•</p><a href=/bans.php>bans</a>"
	?>
	<p>]</p>

	<p>[</p>
	<?php
		$boards = board_list();

		foreach ($boards as $b)
		{
			echo "<a href=/$b->id/>$b->id</a>";
			$last_board = $boards[count($boards) - 1];

			if ($b != $last_board)
				echo "<p>•</p>";
		}
	?>
	<p>]</p>

	<div style="display: none;" id="theme_selector_section">
		<p>Theme:</p>
		<script src="/internal/theme_selector.js"></script>

		<select onchange="selectThemeFromPicker();" id="theme_selector" autocomplete="off">
		<?php
			$themes = scandir(__DIR__ . "/internal/styles/");
			array_push($themes, "default");
			foreach ($themes as $theme)
			{
				if (is_dir(__DIR__ . "/internal/styles/$theme"))
					continue;

				if (($theme != "." && $theme != "..") || $theme == "custom" || $theme == "default")
				{
					if (!isset($_COOKIE["theme"]) && $theme == "default")
						echo "<option selected value='" . $theme . "'>" . $theme . "</option>";
					else if (isset($_COOKIE["theme"]) && $_COOKIE["theme"] == $theme)
						echo "<option selected value='" . $theme . "'>" . $theme . "</option>";
					else
						echo "<option value='" . $theme . "'>" . $theme . "</option>";
				}
			}
		?>
		</select>

		<script>
			document.getElementById("theme_selector_section").style.display = "block";
		</script>
	</div>
</div>

<div id="content">
<div id=site_banner>
	<?php
		// list all banners
		$banners = array_diff(scandir(__DIR__ . "/static/banners/"), array(".", ".."));
		sort($banners);
		$banner_number = rand(0, count($banners) - 1);
		$banner = $banners[$banner_number];
		echo "<img src='/static/banners/$banner'>";
	?>
</div>
