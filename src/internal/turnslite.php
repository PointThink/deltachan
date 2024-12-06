<?php
include_once "chaninfo.php";
include_once "config.php";

function turnslite_get_site_key()
{
    $chan_info = chan_info_read();
    return $chan_info->turnslite_site_key;    
    //return "1x00000000000000000000AA";
}

function turnslite_get_secret_key()
{
    global $deltachan_config;
    $key = file_get_contents($deltachan_config["crypt_key_path"]);
    $chan_info = chan_info_read();
	$chan_info->turnslite_site_key = $chan_info->turnslite_site_key; 
    return openssl_decrypt($chan_info->turnslite_secret_key, "aes-256-ecb", $key);
    // return "1x0000000000000000000000000000000AA";
}

function turnslite_is_enabled()
{
    $chan_info = chan_info_read();
    return ($chan_info->turnslite_secret_key != null && $chan_info->turnslite_site_key != null);
}

function turnslite_verify_response($responseToken)
{
    if (!turnslite_is_enabled())
        return true;

    $verifyResponse = file_get_contents("https://challenges.cloudflare.com/turnstile/v0/siteverify", false, stream_context_create([
        'http' => [
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query([
                'secret'   => turnslite_get_secret_key(),
                'response' => $responseToken,
            ]),
        ],
    ]));

    $response = json_decode($verifyResponse);
    return $response->success;
}
