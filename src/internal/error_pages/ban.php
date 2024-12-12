<?php include_once "../utils.php" ?>

<!DOCTYPE html>
<html>
    <head>
        <title>You are banned!</title>
        <?php include "../link_css.php" ?>
    </head>
    <body>
        <?php include "../../topbar.php" ?>

        <div class="list">
            <h1 class="list_title">Banned!</h1>

            <div class="list_content ban">
                <div class="ban_text">
                    <h3>You have been banned from posting on this imageboard</h3>
                    
                    <br>

                    <?php
                        include_once "../bans.php";
                        $ban_info = ban_read($_SERVER["REMOTE_ADDR"]);

                        echo "<p>You have been banned for:<br><pre>$ban_info->reason</pre></p>";

                        if ($ban_info->expires)
                        {
                            $end_date = strtotime($ban_info->date) + $ban_info->duration;
                            $end_date_format = date("d/m/y H:i", $end_date);
                            echo "<p>Your ban expires <b>$end_date_format</b>";
                        }
                        else
                        {
                            echo "<p><b>Your ban is permanent</b></p>";
                        }
                            
                    ?>

                    <br>
                    <br>
                    <p>To avoid getting banned again please follow the <a href="/rules.php">rules</a></p>
                    <p>If you belive this was a mistake please contact the site administration</p>
                </div>
                <div class="ban_image">
                    <?php echo "<img src=\"" . pick_random_file_from_dir("../../static/ban_images") . "\">"; ?>
                </div>
            </div>
        </div>
        <?php include "../../footer.php" ?>
    </body>
</html>