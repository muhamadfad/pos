<?php
$content = file_get_contents('routes/web.php');
$content = preg_replace('/^.*?use App\\\\Http\\\\Controllers\\\\(Report|SuperDashboard|SystemSetting|Database)Controller;.*\n/m', '', $content);
$content = preg_replace('/^\s*Route::(prefix|get|post).*?(Report|SuperDashboard|SystemSetting|Database)Controller::class.*\n/m', '', $content);
$content = preg_replace('/^\s*Route::prefix\(\'(?:monitoring|settings|database)\'\).*?function \(\) \{\s*\}\);\n/ms', '', $content);
$content = preg_replace('/^\s*Route::prefix\(\'reports\'\).*?function \(\) \{\s*\}\);\n/ms', '', $content);
file_put_contents('routes/web.php', $content);
echo "Fixed web.php";
