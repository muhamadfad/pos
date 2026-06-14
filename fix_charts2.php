<?php
$files = glob('resources/views/admin/finance/*.blade.php');
foreach($files as $f) {
    $c = file_get_contents($f);
    
    // Replace listener with setTimeout
    $c = preg_replace('/document\.addEventListener\(\'turbo:load\',\s*function\s*\(\)\s*\{/i', 'setTimeout(function() {', $c);
    
    // Replace the closing tag. Using a simpler regex that just looks for });</script> at the end of the script block
    $c = str_replace("});\n</script>", "}, 150);\n</script>", $c);
    $c = str_replace("});\r\n</script>", "}, 150);\r\n</script>", $c);
    
    // Let's also check for Chart variable redeclarations like `let chart = ` to `var chart = `
    $c = preg_replace('/let\s+([a-zA-Z0-9_]+Chart)\s+=/i', 'var $1 =', $c);
    $c = preg_replace('/const\s+([a-zA-Z0-9_]+Chart)\s+=/i', 'var $1 =', $c);
    
    file_put_contents($f, $c);
}
echo "Done fixing finance charts.\n";
