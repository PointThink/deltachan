<?php
	if (is_file("first_run"))
	{
		header("Location: /first_setup.php");
		die();
	}

	include_once "internal/board.php";
	include_once "internal/bans.php";

	$database = new Database();
?>

<!DOCTYPE html>
<html>
	<head>
		<?php
		include "internal/chaninfo.php"; 
	
		$chan_info = chan_info_read();
		echo "<title>$chan_info->chan_name</title>";

		include "internal/link_css.php";
		?>
	</head>

	<body>
		<?php
			include "topbar.php";
		?>

		<div class="list">
			<?php echo "<h3 class=\"list_title\">Welcome to $chan_info->chan_name</h3>" ?>
			<div class="list_content">
				<?php
					echo "<pre>$chan_info->welcome</pre>"
				?>
			</div>
		</div>

		<br>

		<div class=list>
			<table class="boards_table">
				<tr>
					<th>Board</th>
					<th>Title</th>
					<th>Subtitle</th>
					<th>Posts</th>
					<th>Unique posters</th>
				</tr>

				<?php
				$boards = board_list();

				foreach ($boards as $board)
				{
					$query_result = $database->query("select count(*) from posts_$board->id");
					$post_count = intval($query_result->fetch_assoc()["count(*)"]);

					$query_result = $database->query("select count(distinct poster_ip) from posts_$board->id");
					$unique_posters = intval($query_result->fetch_assoc()["count(distinct poster_ip)"]);

					echo "<tr>
					<td class=table_board_id><a href=$board->id/>/$board->id/</a></td>
					<td class=table_board_title><a href=$board->id/>$board->title</a></td>
					<td class=table_board_subtitle>$board->subtitle</td>
					<td class=table_board_post_count>$post_count</td>
					<td class=table_board_post_count>$unique_posters</td>
					</tr>";

					unset($query_result);
					unset($post_count);
				}

				?>
			</table>
		</div>
		
		<br>

		<div class="list">
			<h3 class="list_title">Stats</h3>
			<div class="list_content">
				<?php
					function formatBytes($size, $precision = 2)
					{
						$base = log($size, 1024);
						$suffixes = array('', 'K', 'M', 'G', 'T');   
					
						return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)] . "B";
					}

					$post_count = 0;

					foreach (board_list() as $board)
					{
						$result = $database->query("select count(*) from posts_$board->id");
						$post_count += intval($result->fetch_assoc()["count(*)"]);
					}

					echo "<p>$post_count posts</p>";

					$uploaded_files = scandir("uploads");
					$file_count = 0;
					$file_size = 0;

					foreach($uploaded_files as $file)
					{
						if (!str_contains($file, "-thumb") && !str_starts_with($file, "."))
						{
							$file_size += filesize("uploads/$file");
							$file_count++;
						}
					}

					$file_size = formatBytes($file_size, 0);

					echo "<p>$file_count uploaded files</p>";
					echo "<p>$file_size of content</p>";
				?>
			</div>
		</div>

		<br>

		<div class=list>
			<h3 class=list_title>Recent images</h3>
			<div class=list_content>
			
			<?php
				$recent_image_count = 4;

				function scan_dir_sorted($dir) {
				    $ignored = array('.', '..');

				    $files = array();    
				    foreach (scandir($dir) as $file) {
				        if (in_array($file, $ignored)) continue;
				        if (!str_contains($file, "thumb")) continue;
				        if (!str_starts_with(mime_content_type(__DIR__ . "/uploads/$file"), "image")) continue;

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

					echo "<a href=/$board/post.php?id=$post><img class=recent_image src=/uploads/" . $files[$i] . ">";
				}
			?>

			</div>
		</div>

		<?php include "footer.php"; ?>
	</body>
</html>
