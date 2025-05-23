<?php

class ChanInfo
{
    public $chan_name;
    public $welcome;
    public $rules;
    public $faq;
    public $motd;
    public $footer;
    public $default_theme = "yotusuba-blue.css";
    public $default_nsfw_theme = "yotsuba.css";
    public $password_salt;
    public $turnslite_site_key;
    public $turnslite_secret_key;
    public $show_ban_list = false;
    public $locale;
    public $rate_limiting_enabled = false;
    public $rate_limit_range;
    public $rate_limit_max_threads;
    public $allow_text_only_ops = false;
}

function chan_info_read()
{
    $data = json_decode(file_get_contents(__DIR__ . "/chaninfo.json"));
    $class = new ChanInfo();
    foreach ($data as $key => $value) $class->{$key} = $value;
    return $class;
}

function chan_info_write($chan_info)
{
    $json = json_encode($chan_info);
    $file = fopen(__DIR__ . "/chaninfo.json", "w");
    fwrite($file, $json);
    fclose($file);
}
