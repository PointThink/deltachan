<?php
include_once "chaninfo.php";

$locale_name = chan_info_read()->locale;

function load_locale($name)
{
    $jsonContent = file_get_contents(__DIR__ . "/locales/$name.json");
    return json_decode($jsonContent, true);
}

function localize($name)
{
    global $locale;
    global $fallback_locale;
    
    if (isset($locale[$name]))
        return $locale[$name];
    else
        return $fallback_locale[$name];
}

$fallback_locale = load_locale("en");
$locale = load_locale($locale_name);
