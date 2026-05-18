<?php

$Module = ['name' => 'Product', 'variable_params' => true];

$ViewList = [
    'index' => [
        'params' => [],
        'functions' => ['manage_product'],
    ],
    'getproducts' => [
        'params' => ['id', 'product_id'],
    ],
];

$FunctionList = [
    'manage_product' => ['explain' => 'Allow users to maintain themes'],
];
