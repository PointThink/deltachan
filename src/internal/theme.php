<?php
session_start();
header("Content-type: text/css");

// this will be loaded from cookies later
if (!isset($_COOKIE["theme"]))
{
    setcookie("theme", "yotsuba-blue.css", time() + 60*60*24*30, "/");
    $css_file_name = "yotsuba-blue.css";
}
else
    $css_file_name = $_COOKIE["theme"];

$base_style_file = fopen(__DIR__ . "/base_style.css", "r");
$base_style = fread($base_style_file, filesize(__DIR__ . "/base_style.css")) . "\n";

$css_file = fopen(__DIR__ . "/styles/" . $css_file_name, "r");
$css = fread($css_file, filesize(__DIR__ . "/styles/" . $css_file_name));

echo $base_style . $css . "\n" . $_SESSION["user_css"];
