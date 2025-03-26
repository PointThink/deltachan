<?php
include_once "../../database.php";
include_once "../../staff_session.php";

if (!staff_session_is_valid() || !staff_is_janny())
{
    die("You are not allowed here");
}

$database = new Database();

if (count($_POST) > 0)
{
    echo var_dump($_POST);
    $thread = post_read($database, $_POST["id"], $_POST["board"]);

    // recreate the thread
    // create original post
    $new_thread = post_create($database, $_POST["moveto"], false, 0, $thread->name, $thread->title, $thread->body, $thread->poster_ip, $thread->poster_country, $thread->is_staff_post);
    post_update_file($database, $new_thread->board, $new_thread->id, $thread->image_file);

    foreach ($thread->replies as $reply)
    {
        $new_reply = post_create(
            $database,
            $new_thread->board, true, $new_thread->id,
            $reply->name, $reply->title, $reply->body, $reply->poster_ip,
            $reply->poster_country, $reply->is_staff_post
        );

        post_update_file($database, $new_thread->board, $new_reply->id, $reply->image_file);
        header("Location: /$new_thread->board/post.php?id=$new_thread->id");
    }

    // delete old thread
    post_delete($database, $thread->board, $thread->id, false);
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Editing board</title>
		<?php include "../../link_css.php" ?>
	</head>

	<body>
		<?php include "../../../topbar.php" ?>

		<h1 class="title">Moving thread</h1>
		<?php
            $thread = post_read($database, $_GET["id"], $_GET["board"]);
            $thread->display(false, false, true);

            $board_names = array();
            foreach (board_list() as $board)
            {
                $board_names[] = $board->id;
            }
		?>

		<div class=post_form>
			<?php
			(new PostForm("", "POST"))
                ->add_dropdown("Move to board", "moveto", $board_names, $_GET["board"])
                ->add_hidden_data("board", $_GET["board"])
                ->add_hidden_data("id", $_GET["id"])
				->finalize();
			?>
		</div>
		<br>
		<?php include "../../../footer.php" ?>
	</body>
</html>
