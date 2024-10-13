<?php
include_once "../../staff_session.php";
include_once "../../bans.php";
include_once "../../board.php";

if (!staff_is_moderator() || !staff_session_is_valid())
    die("You are not allowed here");

if (count($_POST) > 0)
{   
    $ip = $_POST["ip"];
    $duration = 0;
    if (intval($_POST["duration"]) > 0)
        $duration = intval($_POST["duration"]);

    create_ban($_POST["ip"], $_POST["reason"], $duration);


    if (isset($_POST["delete_posts"]))
    {
        $database = new Database();

        function delete_post($id, $board)
        {
            global $database;

            // first delete the file
            $post = $database->read_post($board, $id);
            $file_parts = explode(".", $post->image_file);
            $thumbnail_path = $file_parts[0] . "-thumb.jpg";
            unlink(__DIR__ . "/../../../$post->image_file");
            unlink(__DIR__ . "/../../../$thumbnail_path");	
            
            $database->remove_post($board, $id);

            foreach ($post->replies as $reply)
                delete_post($reply->id, $board);
        }

        foreach (board_list() as $board)
        {
            $result = $database->query("
                select id from posts_$board->id where poster_ip = '$ip';
            ");

            if ($result->num_rows > 0)
            {
                while ($post_array = $result->fetch_assoc())
                    delete_post($post_array["id"], $board->id);
            }
        }
    }

    header("Location: /internal/staff_forms/manage_bans.php");
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Ban user</title>
        <?php include "../../link_css.php"; ?>
    </head>

    <body>
        <?php include "../../../topbar.php" ?>
        <h1 class="title">Banning user</h1>

        <div class="post_form">
            <?php
                include_once "../../ui.php";

                (new PostForm("", "POST"))
                    ->add_text_area("Ban reason", "reason")
                    ->add_number("Duration (days)(Leave blank for permanent)", "duration")
                    ->add_checkbox("Delete all posts", "delete_posts", true)
                    ->add_hidden_data("ip", $_GET["ip"])
                    ->finalize();
            ?>
        </div>

        <?php include "../../../footer.php" ?>
    </body>
</html>