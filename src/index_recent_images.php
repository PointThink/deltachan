<?php
function str_starts_with_array_element($needles, $haystack)
{
	foreach ($needles as $needle)
	{
		if (str_starts_with($haystack, $needle))
			return true;
	}

	return false;
}
?>

<div class="recent_images col">
<?php echo "<h3 class=list_title>" . localize("index_recent_images") . "</h3>"; ?>
	<div class=list_content>
	
	<?php
		$recent_image_count = 4;

		// gather list of nsfw boards
		$boards = board_list();
		$nsfw_boards = array();

		foreach ($boards as $board)
		{
			if ($board->nsfw)
				$nsfw_boards[] = $board->id;
		}

		function scan_dir_sorted($dir) {
		    global $nsfw_boards;
			
			$ignored = array('.', '..');

		    $files = array();    
		    foreach (scandir($dir) as $file) {
		        if (in_array($file, $ignored)) continue;
		        if (!str_contains($file, "thumb")) continue;
		        if (!str_starts_with(mime_content_type(__DIR__ . "/uploads/$file"), "image")) continue;
				if (str_starts_with_array_element($nsfw_boards, $file)) continue;

		        $files[$file] = filemtime($dir . '/' . $file);
		    }

		    arsort($files);
		    $files = array_keys($files);

		    return $files;
		}

		$files = scan_dir_sorted(__DIR__ . "/uploads/");

		for ($i = 0; $i < $recent_image_count && $i < count($files); $i++)
		{
			$parts = explode(".", $files[$i]);
			$parts = explode("-", $parts[0]);
			$board = $parts[0];
			$post = $parts[1];

			echo "<a href=/$board/post.php?id=$post><img class=recent_image src=/uploads/" . $files[$i] . "></a>";
		}
	?>

	</div>
</div>