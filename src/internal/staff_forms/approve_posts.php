<?php
include_once "../database.php";
include_once "../staff_session.php";

if (!staff_session_is_valid() || !staff_is_moderator()) 
	die("You are not allowed here");
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Approve posts</title>
		<?php include "../link_css.php" ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>

	<body>
		<?php include "../../topbar.php" ?>
		
		<h1 class=title>Approve posts</h1>

        <div id="posts">
        <?php
            $database = new Database();
            $boards = board_list();
            $posts = array();

            foreach ($boards as $board)
            {
                $result = $database->query("select id from posts_$board->id where approved = 0;");

                while ($post = $result->fetch_assoc())
                {
                    $posts[] = post_read($database, $post["id"], $board->id);
                }
            }

            foreach ($posts as $post)
            {
                echo "<hr>";

                if ($post->is_reply)
                {
                    post_read($database, $post->replies_to, $post->board)->display(false, false, true, true);
                    echo "<div class=post>";
                    $post->display();
                    echo "</post>";
                }
                else
                {
                    $post->display(false, false, true, true);
                }
            }
        ?>
        </div>

		<?php include "../../footer.php" ?>
	</body>
</html>
