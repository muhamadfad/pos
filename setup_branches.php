<?php

use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

$branchesData = [
    1 => 'Cabang Sao Sao',
    2 => 'Cabang 2',
    3 => 'Cabang 3',
    4 => 'Cabang 4',
    5 => 'Cabang 5',
    6 => 'Cabang 6',
    7 => 'Cabang 7',
];

foreach ($branchesData as $id => $name) {
    $branch = Branch::find($id);
    if (!$branch) {
        $branch = new Branch();
        $branch->id = $id;
        $branch->code = 'CBG00' . $id;
    }
    $branch->name = $name;
    $branch->is_active = 1;
    $branch->save();
    
    // Ensure admin and kasir exist for this branch
    // Admin
    $admin = User::where('role', 'admin')->where('branch_id', $branch->id)->first();
    if (!$admin) {
        $admin = new User();
        $admin->role = 'admin';
        $admin->branch_id = $branch->id;
        $admin->password = Hash::make('password');
    }
    $admin->name = "Admin " . $name;
    $admin->email = "admin" . str_replace(' ', '', strtolower($name)) . "@resto.com";
    $admin->save();

    // Kasir
    $kasir = User::where('role', 'kasir')->where('branch_id', $branch->id)->where('is_decoy', false)->first();
    if (!$kasir) {
        $kasir = new User();
        $kasir->role = 'kasir';
        $kasir->branch_id = $branch->id;
        $kasir->password = Hash::make('password');
    }
    $kasir->name = "Kasir " . $name;
    $kasir->email = "kasir" . str_replace(' ', '', strtolower($name)) . "@resto.com";
    $kasir->save();
}

echo "Branches and users updated successfully.\n";
