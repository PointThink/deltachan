<?php
include_once "internal/bans.php";
include_once "internal/ui.php";

$chan_info = chan_info_read();
if (!$chan_info->show_ban_list)
    die("This page has been disabled by the site administrator");
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Ban list</title>
        <?php include "internal/link_css.php" ?>
    </head>

    <body>
        <?php include "topbar.php" ?>

        <h1 class="title">Public Ban List</h1>

        <table class=manage_table>
			<tr>
				<th>IP adress</th>
				<th>Ban reason</th>
				<th>Expire date</th>
                <th>Banned by</th>
			</tr>

			<?php
				$bans = ban_list_banned_ips();

				foreach ($bans as $ip)
				{
                    $ban_info = ban_read($ip);

					echo "
					<tr>
						<td>$ip</td>
						<td>$ban_info->reason</td>
                    ";
                    
                    if ($ban_info->expires)
                    {
                        $end_date = strtotime($ban_info->date) + $ban_info->duration;
                        $end_date_format = date("d/m/y H:i", $end_date);
                        echo "<td>$end_date_format</td>";
                    }
                    else
                    {
                        echo "<td>Permanent</td>";
                    }

                    $banner = staff_get_current_user($ban_info->banned_by);
                    echo "<td>$banner->username ## $banner->role</td>";
				}
			?>
		</table>

        <?php include "footer.php" ?>
    </body>
</html>