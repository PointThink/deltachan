<div class=list>
    <h3 class="list_title">Boards</h3>
    <div class="list_content">
    <?php
    $boards = board_list();

    foreach ($boards as $board)
    {
        $query_result = $database->query("select count(*) from posts_$board->id");
        $post_count = intval($query_result->fetch_assoc()["count(*)"]);

        $query_result = $database->query("select count(distinct poster_ip) from posts_$board->id");
        $unique_posters = intval($query_result->fetch_assoc()["count(distinct poster_ip)"]);
        $nsfw_text = "";

        if ($board->nsfw)
            $nsfw_text = "(NSFW)";
        
        echo "<a href=$board->id/>$board->title</a><p class=nsfw>$nsfw_text</p>";
        unset($query_result);
        unset($post_count);
    }

    ?>
    </div>
</div>