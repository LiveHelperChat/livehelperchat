<?php 

$departament = erLhcoreClassModelDepartament::fetch((int)$Params['user_parameters']['id']);

if (isset($departament->product_configuration_array['products_enabled']) && $departament->product_configuration_array['products_enabled'] == 1)
{
    $items = erLhAbstractModelProductDepartament::getList(array('filter' => array('departament_id' => $departament->id)));

    erLhcoreClassChat::prefillGetAttributes($items, array('product_name'), array('departament_id','product'));

    $tpl = erLhcoreClassTemplate::getInstance('lhproduct/getproducts.tpl.php');
    $tpl->set('items', $items);
    $tpl->set('product_id', (int)$Params['user_parameters']['product_id']);
    $tpl->set('required',isset($departament->product_configuration_array['products_required']) && $departament->product_configuration_array['products_required'] == 1);
    
    echo json_encode(array('result' => $tpl->fetch()));
    
} else {
    echo json_encode(array('result' => ''));
}



exit;

?>