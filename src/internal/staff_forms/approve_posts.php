<?php
include_once "../database.php";
include_once "../staff_session.php";

if (!staff_session_is_valid() || !staff_is_admin()) 
	die("You are not allowed here");
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Approve posts</title>
		<?php include "../link_css.php" ?>
	</head>

	<body>
		<?php include "../../topbar.php" ?>
		
		<h1 class=title>Approve posts</h1>

        <div id="posts">
        <?php
            // find all boards
            $boards = board_list();
            $unapproved_posts = array();

            foreach ($boards as $board_no_posts)
            {
                $board = board_get($board_no_posts->id);
                foreach ($board->posts as $post)
                {
                    if (!$post->approved)
                        array_push($unapproved_posts, $post);
                }
            }

            foreach ($unapproved_posts as $post)
            {
                echo "<hr>";
                $post->display();
            }
        ?>
        </div>

		<?php include "../../footer.php" ?>
	</body>
</html>
