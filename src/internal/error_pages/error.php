<!DOCTYPE html>

<html>
    <head>
        <title>Error</title>

        <?php include "../../internal/link_css.php" ?>
    </head>

    <body>
        <?php include "../../topbar.php" ?>

        <div class="title">
            <h1>An error has occured</h1>
            <?php
                echo htmlspecialchars($_GET["message"]);
            ?>
        </div>

        <?php include "../../footer.php" ?>
    </body>
</html>