<?php

function replaceInFile($file) {
    $c = file_get_contents($file);
    
    // For Chart.js scripts
    if (strpos($c, 'function checkLib()') !== false) {
        $c = preg_replace(
            '/function checkLib\(\)\s*\{\s*if\s*\(typeof Chart !== \'undefined\'\)\s*\{\s*renderWhenReady\(\);\s*\}\s*else\s*\{\s*setTimeout\(checkLib,\s*50\);\s*\}\s*\}\s*checkLib\(\);/s',
            "window.checkLib = function() {
        if(typeof Chart !== 'undefined') {
            // Find canvas
            const canvases = document.getElementsByTagName('canvas');
            if (canvases.length > 0 && canvases[0].offsetWidth === 0) {
                setTimeout(window.checkLib, 50);
                return;
            }
            renderWhenReady(); 
        } else { 
            setTimeout(window.checkLib, 50); 
        } 
    };
    window.checkLib();",
            $c
        );
        file_put_contents($file, $c);
    }
}

$files = glob('resources/views/{admin/finance,kaspro}/*.blade.php', GLOB_BRACE);
foreach($files as $file) {
    if (is_file($file)) {
        replaceInFile($file);
    }
}
echo "Done.\n";
