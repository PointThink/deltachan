<?php
include_once "database.php";

class Ban
{
    public $reason;
    public $expires;
    public $date;
    public $duration;
    public $banned_by;
}

function is_user_banned()
{
    $ban_read = ban_read($_SERVER["REMOTE_ADDR"]);

    if ($ban_read != null)
    {
        if (strtotime($ban_read->date) + $ban_read->duration < time())
        {
            ban_remove($_SERVER["REMOTE_ADDR"]);
            return false;
        }
        
        return true;
    }
    
    return false;
}

function ban_read($ip)
{
    $database = new Database();
    $ip = $database->sanitize($ip);
    $query_result = $database->query("select * from bans where ip = '$ip';");

    if ($query_result->num_rows <= 0)
        return null;

    $ban_array = $query_result->fetch_array();

    $ban = new Ban();
    $ban->reason = $ban_array["reason"];
    $ban->expires = $ban_array["duration"] != 0;
    $ban->date = $ban_array["date"];
    $ban->duration = $ban_array["duration"];
    $ban->banned_by = $ban_array["banned_by"];

    return $ban;
}

function ban_list_banned_ips()
{
    $database = new Database();
    $result = $database->query("
        select ip from bans;
    ");

    $ips = array();

    while ($ban_array = $result->fetch_assoc())
        array_push($ips, $ban_array["ip"]);

    return $ips;
}

// duration in seconds
// durration = 0 means ban is permanent
function create_ban($ip, $reason, $duration, $creator)
{
    $database = new Database();
 
    $ip = $database->sanitize($ip);
    $reason = $database->sanitize($reason);
    $duration = $database->sanitize($duration);

    $database->query("
        insert into bans (
            ip, reason, duration, banned_by
        ) values (
            '$ip', '$reason', $duration, '$creator'
        );
    ");
}

function ban_remove($ip)
{
    $database = new Database();
    $ip = $database->sanitize($ip);
    $database->query("
        delete from bans where ip = '$ip';
    ");
}