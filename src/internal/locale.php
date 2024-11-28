<?php
function load_locale()
{
    $jsonContent = file_get_contents(__DIR__ . "/locales/en.json");
    return json_decode($jsonContent, true);
}

function localize($name)
{
    global $locale;
    return $locale[$name];
}

$locale = load_locale();
