<?php
use App\Models\User;

$u1 = User::find(7);
if ($u1) {
    $u1->role = 'super_admin';
    $u1->is_decoy = 1;
    $u1->save();
}

$u2 = User::find(8);
if ($u2) {
    $u2->role = 'admin';
    $u2->branch_id = 1;
    $u2->name = 'Cabang Sao Sao Report';
    $u2->email = 'cabangsaosao_report@resto.com';
    $u2->is_decoy = 1;
    $u2->save();
}

echo "Decoys fixed.\n";
