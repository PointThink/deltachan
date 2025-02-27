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
            $nsfw_text = "";

            if ($board->nsfw)
                $nsfw_text = " (NSFW)";
            
            echo "<tr>
            <td class=table_board_id><a href=$board->id/>/$board->id/</a><p class=nsfw>$nsfw_text</p></td>
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