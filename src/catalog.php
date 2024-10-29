<?php
include_once "internal/board.php";
?>

<!DOCTYPE html>
<html>
    <head>
        <?php
            include "internal/link_css.php";
            echo "<title>/$board_id/ catalog</title>";
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>

    <body>
        <?php
        include "topbar.php";
        
        $board = board_get($board_id, -1);
        echo "<title>/$board->id/ - $board->title</title>";

        echo "
        <div class=title>
            <h2>/$board->id/ - $board->title</h2>
			<h4>$board->subtitle</h4>
            <a href=/$board->id/>Return to index</a>
        </div>
        ";
        ?>

        <div class=catalog_posts>
        <?php
            foreach ($board->posts as $post)
            {
                $post->display_catalog();
            }
        ?>
        </div>

        <?php include "footer.php"; ?>
    </body>
</html>