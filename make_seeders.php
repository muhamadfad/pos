<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

$tables = [
    'branches', 
    'users', 
    'units', 
    'categories', 
    'suppliers',
    'customers',
    'products', 
    'ingredients', 
    'recipe_items'
];

$seedersContent = "<?php\n\nnamespace Database\Seeders;\n\nuse Illuminate\Database\Seeder;\nuse Illuminate\Support\Facades\DB;\n\nclass BackupSeeder extends Seeder\n{\n    public function run()\n    {\n        // Disable foreign key checks before seeding\n        DB::statement('SET FOREIGN_KEY_CHECKS=0;');\n\n";

foreach ($tables as $table) {
    $rows = DB::table($table)->get()->map(function($item) {
        return (array) $item;
    })->toArray();
    
    if(count($rows) > 0) {
        $export = var_export($rows, true);
        // clean up objects representation (like stdClass if any)
        $export = preg_replace('/stdClass::__set_state\(/', '(', $export);
        
        $seedersContent .= "        DB::table('{$table}')->insert(" . $export . ");\n\n";
    }
}

$seedersContent .= "        // Re-enable foreign key checks\n        DB::statement('SET FOREIGN_KEY_CHECKS=1;');\n    }\n}\n";

File::put(database_path('seeders/BackupSeeder.php'), $seedersContent);
echo "BackupSeeder created successfully.\n";
