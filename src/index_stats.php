<div class="stats col">
	<?php echo "<h3 class=list_title>" . localize("index_stats") . "</h3>"; ?>
	<div class="list_content">
		<?php
			include_once "internal/utils.php";

			$post_count = 0;
			$poster_count = 0;

			foreach (board_list() as $board)
			{
				$post_result = $database->query("select count(*) from posts_$board->id");
				$post_count += intval($post_result->fetch_assoc()["count(*)"]);

				$poster_result = $database->query("select count(distinct poster_ip) from posts_$board->id");
				$poster_count += intval($poster_result->fetch_assoc()["count(distinct poster_ip)"]);
			}

			echo "<p>$post_count posts</p>";
			echo "<p>$poster_count unique posters</p>";

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

			$file_size = format_bytes($file_size, 0);

			echo "<p>$file_count uploaded files</p>";
			echo "<p>$file_size of content</p>";
		?>
	</div>
</div>