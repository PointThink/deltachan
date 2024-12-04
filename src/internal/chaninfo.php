<?php

class ChanInfo
{
    public $chan_name;
    public $welcome;
    public $rules;
    public $faq;
    public $default_theme;
    public $password_salt;
    public $turnslite_site_key;
    public $turnslite_secret_key;
}

function chan_info_read()
{
    return json_decode(file_get_contents(__DIR__ . "/chaninfo.json"));
}

function chan_info_write($chan_info)
{
    $json = json_encode($chan_info);
    $file = fopen(__DIR__ . "/chaninfo.json", "w");
    fwrite($file, $json);
    fclose($file);
}
