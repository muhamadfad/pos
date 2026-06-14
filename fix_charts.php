<?php
$files = glob('resources/views/{admin/finance,kaspro}/*.blade.php', GLOB_BRACE);
foreach($files as $f) {
    $c = file_get_contents($f);
    $c = str_replace("document.addEventListener('DOMContentLoaded'", "document.addEventListener('turbo:load'", $c);
    file_put_contents($f, $c);
}
echo "Done.\n";
