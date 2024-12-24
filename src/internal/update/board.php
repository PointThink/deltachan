<?php
function update_add_board_catalogs()
{
    $files = scandir(__DIR__ . "/../../");
    $ignored = array(".", "..", "internal", "static", "uploads");

    $files = array_diff($files, $ignored);

    foreach ($files as $board)
    {
        if (!is_dir(__DIR__ . "/../../$board"))
            continue;

        $catalog_file = fopen(__DIR__ . "/../../$board/catalog.php", "w");
        fwrite($catalog_file, "<?php
        \$board_id = '$board';
        include __DIR__ . '/../catalog.php';
    ");
    }
}

function update_boards()
{
    $database = new Database();

    $result = $database->query("SHOW COLUMNS FROM `board_info` LIKE 'nsfw'");
    $exists = $result->num_rows ? TRUE:FALSE;
    
    if (!$exists)
    {
        $database->query("
            alter table board_info add nsfw int default 0 not null;        
        ");
    }

    $result = $database->query("SHOW COLUMNS FROM `board_info` LIKE 'ids'");
    $exists = $result->num_rows ? TRUE:FALSE;
    
    if (!$exists)
    {
        $database->query("
            alter table board_info add ids int default 0 not null;        
        ");
    }
}

function update_ban_list()
{
    $database = new Database();

    $result = $database->query("SHOW COLUMNS FROM `bans` LIKE 'banned_by'");
    $exists = $result->num_rows ? TRUE:FALSE;
    
    if (!$exists)
    {
        $database->query("
            alter table bans add banned_by varchar(30) default null;        
        ");
    }
}

