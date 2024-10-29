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