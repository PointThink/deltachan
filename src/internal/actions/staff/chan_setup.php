<?php
include_once "../../database.php";
include_once "../../staff_session.php";
include_once "../../ui.php";
include_once "../../chaninfo.php";
include_once "../../config.php";

if (!staff_session_is_valid() || !staff_is_admin()) 
	die("You are not allowed here");

if (count($_POST) > 0)
{
    $chan_info = new ChanInfo();
    $chan_info->chan_name = $_POST["name"];
    $chan_info->rules = $_POST["rules"];
    $chan_info->welcome = $_POST["welcome"];
	$chan_info->faq = $_POST["faq"];

	$key = file_get_contents($deltachan_config["crypt_key_path"]);
	$chan_info->turnslite_site_key = $_POST["turnslite_site_key"];
	$chan_info->turnslite_secret_key = openssl_encrypt($_POST["turnslite_secret_key"], "aes-256-ecb", $key);

    chan_info_write($chan_info);

	header("Location: /staff_dashboard.php");
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Editing imageboard info</title>
		<?php include "../../link_css.php" ?>
	</head>

	<body>
		<?php include "../../../topbar.php" ?>

		<h1 class="title">Setup imageboard</h1>
		<?php
			$database = new Database();
            $chan_info = chan_info_read();
		?>

		<div class=post_form>
			<?php
			$key = file_get_contents($deltachan_config["crypt_key_path"]);

			(new PostForm("/internal/actions/staff/chan_setup.php", "POST"))
                ->add_text_field("Chan name", "name", $chan_info->chan_name)
                ->add_text_area("Welcome message", "welcome", htmlspecialchars($chan_info->welcome))
                ->add_text_area("Rules", "rules", htmlspecialchars($chan_info->rules))
				->add_text_area("FAQ", "faq", htmlspecialchars($chan_info->faq))
				->add_text_field("Turnslite site key", "turnslite_site_key", $chan_info->turnslite_site_key)
				->add_text_field("Turnslite secret key", "turnslite_secret_key", openssl_decrypt($chan_info->turnslite_secret_key, "aes-256-ecb", $key))
				->finalize();
			?>
		</div>

		<br>

		<?php include "../../../footer.php" ?>
	</body>
</html>

