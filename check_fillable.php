<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

$modelsPath = app_path('Models');
$files = File::allFiles($modelsPath);

foreach ($files as $file) {
    $class = 'App\\Models\\' . $file->getFilenameWithoutExtension();
    if (class_exists($class)) {
        $model = new $class();
        if (method_exists($model, 'getTable') && method_exists($model, 'getFillable')) {
            $table = $model->getTable();
            $fillables = $model->getFillable();
            
            if (Schema::hasTable($table)) {
                $columns = Schema::getColumnListing($table);
                foreach ($fillables as $fillable) {
                    if (!in_array($fillable, $columns)) {
                        echo "Warning: Model {$class} has fillable '{$fillable}' but column does not exist in table '{$table}'.\n";
                    }
                }
            } else {
                echo "Warning: Model {$class} uses table '{$table}' but table does not exist.\n";
            }
        }
    }
}
