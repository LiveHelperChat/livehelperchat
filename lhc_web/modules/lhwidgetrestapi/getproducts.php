<?php

erLhcoreClassRestAPIHandler::setHeaders();

$departament = erLhcoreClassModelDepartament::fetch((int)$Params['user_parameters']['id']);

if (isset($departament->product_configuration_array['products_enabled']) && $departament->product_configuration_array['products_enabled'] == 1)
{
    $items = erLhAbstractModelProductDepartament::getList(['filter' => ['departament_id' => $departament->id]]);

    // Collect products id's
    $productsIds = [];
    foreach ($items as $item) {
        $productsIds[] = $item->product_id;
    }

    $products = [];

    if (!empty($productsIds)) {
        $products = erLhAbstractModelProduct::getList(['sort' => 'priority ASC, name ASC', 'filter' => ['disabled' => 0], 'filterin' => ['id' => $productsIds]]);
    }

    $returnProduct = [];

    foreach ($products as $product) {
        $returnProduct[] = [
            'value' => $product->id,
            'name' => $product->name
        ];
    }

    echo json_encode([
        'required' => (isset($departament->product_configuration_array['products_required']) && $departament->product_configuration_array['products_required'] == 1),
        'products' => $returnProduct
    ]);

} else {
    echo json_encode(['products' => []]);
}

exit;
