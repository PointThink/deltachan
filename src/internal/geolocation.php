<?php
class IPLocationInfo
{
    public $timezone;
    public $country;

    public function __construct($ip)
    {
        $data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=$ip"), true);
        $this->timezone = $data["geoplugin_timezone"];
        $this->country = $data["geoplugin_countryCode"];
    }
}
