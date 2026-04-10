<?php 

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
       
    $tpl = erLhcoreClassTemplate::getInstance('lhproduct/getproducts.tpl.php');
    $tpl->set('items', $products);
    $tpl->set('product_id', (int)$Params['user_parameters']['product_id']);
    $tpl->set('required',isset($departament->product_configuration_array['products_required']) && $departament->product_configuration_array['products_required'] == 1);
    
    echo json_encode(['result' => $tpl->fetch()]);
    
} else {
    echo json_encode(['result' => '']);
}

exit;
