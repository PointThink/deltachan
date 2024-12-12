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