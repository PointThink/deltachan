<?php
include_once "../database.php";
include_once "../report.php";
include_once "../staff_session.php";
include_once "../ui.php";

if (!staff_session_is_valid() || !staff_is_moderator()) 
	die("You are not allowed here");
?>

<!DOCTYPE html>
<html>
	<head>
		<title>View reports</title>
		<?php include "../link_css.php" ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

                (new ActionLink("/internal/actions/staff/remove_report.php", "discard_$report->report_id", "Discard"))
                    ->add_data("report_id", $report->report_id)
                    ->finalize();

                $post = post_read($database, $report->post_id, $report->post_board);
                $post->display(false, false, true);
            }
        ?>
        </div>

		<?php include "../../footer.php" ?>
	</body>
</html>
