<?php
function pick_random_file_from_dir($path)
{
    $dir = scandir($path);
    $dir = array_filter($dir, function($item) use ($path) {
        return !is_dir("$path/$item");
    });
    $dir = array_values($dir);

    return $path . "/" . $dir[rand(0, count($dir) - 1)];
}

function format_bytes($size, $precision = 2)
{
    $base = log($size, 1024);
    $suffixes = array('', 'K', 'M', 'G', 'T');   

    return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)] . "B";
}