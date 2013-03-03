<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/cannedmsg.tpl.php');

if (is_numeric($Params['user_parameters_unordered']['id']) && $Params['user_parameters_unordered']['action'] == 'delete'){
    
    // Delete selected canned message
    try {
        $Msg = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelCannedMsg', (int)$Params['user_parameters_unordered']['id'] );
        $Msg->removeThis();
    } catch (Exception $e) {
        // Do nothing
    }
    
    erLhcoreClassModule::redirect('chat/cannedmsg');
    exit;
}



$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('chat/cannedmsg');
$pages->items_total = erLhcoreClassModelCannedMsg::getCount();
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
    $items = erLhcoreClassModelCannedMsg::getList(array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id ASC'));
}

$tpl->set('items',$items);
$tpl->set('pages',$pages);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('chat/cannedmsg'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Canned messages')))

?>