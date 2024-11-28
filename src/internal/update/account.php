<?php
include_once "../database.php";

function update_account_passwords()
{
    $database = new Database();

    $result = $database->query("SHOW COLUMNS FROM `staff_accounts` LIKE 'needs_update'");
    $exists = $result->num_rows ? TRUE:FALSE;
    
    if (!$exists)
    {
        $database->query("
            alter table staff_accounts add needs_update int default 1 not null;        
        ");
    }
}
