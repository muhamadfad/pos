<?php
use Illuminate\Http\Request;
use App\Http\Controllers\RecipeController;

$req = new Request(['product_id' => 1, 'items' => [['ingredient_id' => 1, 'quantity' => 1]]]);
$controller = new RecipeController();
try {
    $resp = $controller->store($req);
    dump($resp->getSession()->get('error'));
    dump($resp->getSession()->get('errors'));
    echo "Success!";
} catch (\Exception $e) {
    dump($e->getMessage());
}
