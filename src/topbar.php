<?php
include_once "internal/board.php";
include_once "internal/staff_session.php";
?>
<div id=topbar>
	<?php
		if (staff_session_is_valid())
		{
			echo "<p>[</p>";
			echo "<p>Logged in as " . staff_get_current_user()->username . "</p>";
			echo "<a href=/internal/actions/staff/logout.php>Logout</a>";
			echo "<a href=/staff_dashboard.php>Dashboard</a>";
			echo "<p>]</p>";
		}
	?>

	<p>[</p>
	<a href=/>home</a>
	<a href=/settings.php>settings</a>
	<a href=/rules.php>rules</a>
	<a href=/faq.php>faq</a>
	<p>]</p>

	<p>[</p>
	<?php
		$boards = board_list();

		foreach ($boards as $b)
		{
			echo "<a href=/$b->id/>$b->id</a>";
		}
	?>
	<p>]</p>

	<div id="theme_selector_section">
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
	</div>
</div>

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
