<?php
include_once "internal/board.php";
include_once "internal/staff_session.php";
$chan_info = chan_info_read();
ini_set("display_errors", 0);

?>
<script defer src="/internal/deltachan.js"></script>

<div id=topbar>
	<?php
		if (staff_session_is_valid())
		{
			echo "<p>Logged in as " . staff_get_current_user()->username . "</p>";
			echo "<a href=/admin.php?action=logout>Logout</a>";
			echo "<a href=/admin.php>Dashboard</a>";
			echo "<p>|</p>";
		}
	?>

	<a href=/>Home</>
	<a href=/settings.php>Settings</a> 
	<a href=/rules.php>Rules</a>
	<a href=/faq.php>FAQ</a>
	<a href="/admin.php">Account</a>
	<?php
		if ($chan_info->show_ban_list)
			echo "<a href=/bans.php>Bans</a>"
	?>
	<p>|</p>
	<?php
		$boards = board_list();

		foreach ($boards as $b)
		{
			echo "<a href=/$b->id/>/$b->id/</a>";
		}
	?>

	<div style="display: none;" id="theme_selector_section">
		<p>Theme:</p>

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
		echo "<img id=banner src='/static/banners/$banner'>";
	?>
</div>

<?php
if ($chan_info->motd != null)
{
	echo "<h3 class=motd>$chan_info->motd</h3>";
}
?>