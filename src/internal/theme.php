<?php
session_start();
header("Content-type: text/css");

include_once "chaninfo.php";

$chan_info = chan_info_read();

$css_file_name = "";
if (!isset($_COOKIE["theme"]))
{
    setcookie("theme", "default", time() + 60*60*24*30, "/");

    if ($_GET["nsfw"])
        $css_file_name = $chan_info->default_nsfw_theme;
    else
        $css_file_name = $chan_info->default_theme;
}
else if ($_COOKIE["theme"] == "default")
{
    if ($_GET["nsfw"])
        $css_file_name = $chan_info->default_nsfw_theme;
    else
        $css_file_name = $chan_info->default_theme;
}
else
    $css_file_name = $_COOKIE["theme"];

$base_style_file = fopen(__DIR__ . "/base_style.css", "r");
$base_style = fread($base_style_file, filesize(__DIR__ . "/base_style.css")) . "\n";

$css_file = fopen(__DIR__ . "/styles/" . $css_file_name, "r");
$css = fread($css_file, filesize(__DIR__ . "/styles/" . $css_file_name));

if (!isset($_SESSION["user_css"]))
    $_SESSION["user_css"] = "";
echo $base_style . "\n" . $css . "\n" . $_SESSION["user_css"];
