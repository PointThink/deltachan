<?php
session_start();
include_once "internal/ui.php";

if (count($_POST) > 0)
{
    $_SESSION["user_css"] = $_POST["user_css"];
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Settings</title>
        <?php include "internal/link_css.php"; ?>
    </head>

    <body>
        <?php include "topbar.php" ?>
        
        <h1 class=title>Settings</h1>
        <div class=post_form>
        <?php
            (new PostForm("", "POST"))
                ->add_text_area("User CSS", "user_css", $_SESSION["user_css"])
                ->finalize();
        ?>
        </div>
        
        <?php include "footer.php" ?>
    </body>
</html>
