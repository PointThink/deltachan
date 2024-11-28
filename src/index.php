<?php
	if (is_file("first_run"))
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

		<div class=list>
			<table class="boards_table">
				<tr>
				<?php echo"
					<th>" . localize("board") . "</th>
					<th>" . localize("title") . "</th>
					<th>" . localize("subtitle") . "</th>
					<th>" . localize("posts") . "</th>
					<th>" . localize("unique_posters") . "</th>
				"; ?>
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

		<div class=line>
			<?php include "index_stats.php" ?>
			<?php include "index_recent_images.php" ?>
		</div>

		<?php include "footer.php"; ?>
	</body>
</html>
