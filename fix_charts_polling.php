<?php

function replaceInFile($file, $isChartJs = true) {
    $c = file_get_contents($file);
    
    if (strpos($file, 'dashboard.blade.php') !== false && strpos($file, 'kaspro') === false) {
        // Main Dashboard (ApexCharts & jQuery)
        $c = preg_replace('/setTimeout\(\(\)\s*=>\s*\{.*?\},\s*150\);/is', 
            "function renderWhenReady() {\n        if (typeof ApexCharts !== 'undefined' && typeof $ !== 'undefined') {\n            if (window.Iconify) window.Iconify.scan();\n            renderCharts(); \n            loadActivities(); \n        } else {\n            setTimeout(renderWhenReady, 50);\n        }\n    }\n    renderWhenReady();", $c);
    } elseif (strpos($file, 'kaspro/dashboard.blade.php') !== false) {
        // KasPro Dashboard (ApexCharts)
        // Need to check what it uses. It uses turbo:load previously.
        // Let's replace whatever is calling initCashFlowChart
        $c = preg_replace('/document\.addEventListener\(\'turbo:load\',\s*function\(\)\s*\{.*?\setTimeout\(initCashFlowChart,\s*150\);\s*\}\);\s*if\s*\(document\.readyState.*?\s*initCashFlowChart\(\);\s*\}/is', 
            "function renderWhenReady() {\n        if (typeof ApexCharts !== 'undefined') {\n            initCashFlowChart();\n        } else {\n            setTimeout(renderWhenReady, 50);\n        }\n    }\n    renderWhenReady();", $c);
            
        // Also if it was already modified to something else, try to catch it:
        $c = preg_replace('/setTimeout\(function\(\)\s*\{\s*initCashFlowChart\(\);\s*\},\s*150\);/is', 
            "function renderWhenReady() {\n        if (typeof ApexCharts !== 'undefined') {\n            initCashFlowChart();\n        } else {\n            setTimeout(renderWhenReady, 50);\n        }\n    }\n    renderWhenReady();", $c);
    } else {
        // Chart.js (Prediction, Finance Daily/Weekly/Monthly)
        $c = preg_replace('/setTimeout\(function\(\)\s*\{/is', 'function renderWhenReady() {', $c);
        $c = preg_replace('/\},\s*150\);/is', "}\n    function checkLib() { if(typeof Chart !== 'undefined') { renderWhenReady(); } else { setTimeout(checkLib, 50); } } checkLib();", $c);
        
        // In case it used setTimeout without 150
        $c = preg_replace('/\}\);(\s*@endif)?(\s*<\/script>)/is', "}\n    function checkLib() { if(typeof Chart !== 'undefined') { renderWhenReady(); } else { setTimeout(checkLib, 50); } } checkLib();$1$2", $c);
    }
    
    file_put_contents($file, $c);
}

$files = glob('resources/views/{admin/finance,kaspro}/*.blade.php', GLOB_BRACE);
$files[] = 'resources/views/dashboard.blade.php';

foreach($files as $file) {
    if (is_file($file)) {
        replaceInFile($file);
    }
}
echo "Done.\n";
