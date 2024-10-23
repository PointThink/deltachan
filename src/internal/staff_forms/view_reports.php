<?php
include_once "../database.php";
include_once "../report.php";
include_once "../staff_session.php";

if (!staff_session_is_valid() || !staff_is_moderator()) 
	die("You are not allowed here");
?>

<!DOCTYPE html>
<html>
	<head>
		<title>View reports</title>
		<?php include "../link_css.php" ?>
	</head>

	<body>
		<?php include "../../topbar.php" ?>
		
		<h1 class=title>View reports</h1>

        <div id="posts">
        <?php
            $database = new Database();
            $reports = report_list();

            foreach ($reports as $report)
            {
                echo "<hr>";
                echo "<p class=report_header>Reported for:<br>" . htmlspecialchars($report->reason);
                $post = $database->read_post($report->post_board, $report->post_id);
                $post->display(false, false, true);
            }
        ?>
        </div>

		<?php include "../../footer.php" ?>
	</body>
</html>
