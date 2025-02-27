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

<div class="recent_images list">
<?php echo "<h3 class=list_title>" . localize("index_recent_images") . "</h3>"; ?>
	<div class=list_content>
	
	<?php
	$boards_list = board_list();
	$posts = array();

	// gather list of nsfw boards
	$boards = board_list();
	$nsfw_boards = array();

	foreach ($boards as $board)
	{
		if ($board->nsfw)
			$nsfw_boards[] = $board->id;
	}

	foreach ($boards_list as $b)
	{
		$posts = array_merge($posts, $b->get_posts());
	}

	// sort posts by number of replies
	function cmp($a, $b) {
		return count($a->replies) < count($b->replies);
	}

	$posts_sorted = usort($posts, "cmp");
	
	
	$i = 0;
	$desired_posts = 6;
	foreach ($posts as $post)
	{
		if (!$post->approved || in_array($post->board, $nsfw_boards))
			continue;

		$post->display_index();
		$i++;

		if ($i == $desired_posts)
			break;
	}
	?>
	</div>
</div>