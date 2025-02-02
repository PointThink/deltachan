<?php
include_once "chaninfo.php";

$locale_name = chan_info_read()->locale;

function load_locale()
{
    global $locale_name;
    $jsonContent = file_get_contents(__DIR__ . "/locales/$locale_name.json");
    return json_decode($jsonContent, true);
}

function localize($name)
{
    global $locale;
    return $locale[$name];
}

$locale = load_locale();
