<?php
$p = new App\Models\Product();
$p->name = 'Test Product';
$p->category_id = 1;
$p->selling_price = 10000;
$p->save();
dump($p->is_active);
