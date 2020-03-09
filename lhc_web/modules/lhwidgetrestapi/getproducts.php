<?php

erLhcoreClassRestAPIHandler::setHeaders();

$departament = erLhcoreClassModelDepartament::fetch((int)$Params['user_parameters']['id']);

if (isset($departament->product_configuration_array['products_enabled']) && $departament->product_configuration_array['products_enabled'] == 1)
{
    $items = erLhAbstractModelProductDepartament::getList(array('filter' => array('departament_id' => $departament->id)));

    // Collect products id's
    $productsIds = array();
    foreach ($items as $item) {
        $productsIds[] = $item->product_id;
    }

    $products = array();

    if (!empty($productsIds)) {
        $products = erLhAbstractModelProduct::getList(array('sort' => 'priority ASC, name ASC', 'filter' => array('disabled' => 0), 'filterin' => array('id' => $productsIds)));
    }

    $returnProduct = array();

    foreach ($products as $product) {
        $returnProduct[] = array(
            'value' => $product->id,
            'name' => $product->name
        );
    }

    echo json_encode(array(
        'required' => (isset($departament->product_configuration_array['products_required']) && $departament->product_configuration_array['products_required'] == 1),
        'products' => $returnProduct
    ));

} else {
    echo json_encode(array('products' => []));
}



exit;

?>