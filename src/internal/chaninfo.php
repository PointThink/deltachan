<?php

class ChanInfo
{
    public $chan_name;
    public $welcome;
    public $rules;
    public $faq;
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