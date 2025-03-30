<hr>
<div id=footer>
	<p><a href=https://github.com/pointThink/deltachan>DeltaChan</a> v1.9</p>
	<a href="http://www.geoplugin.com/geolocation/" target="_new">IP Geolocation</a> by <a href="http://www.geoplugin.com/" target="_new">geoPlugin</a><br>
	<br>
	<?php
	include_once "internal/chaninfo.php";
	$chan_info = chan_info_read();
	echo nl2br($chan_info->footer);
	?>
</div>
<br>