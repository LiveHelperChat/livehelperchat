<?php

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.blockedusers', array());

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/blockedusers.tpl.php');

if (is_numeric($Params['user_parameters_unordered']['remove_block'])){
    try {

    	if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    		die('Invalid CSRF Token');
    		exit;
    	}

        $block = erLhcoreClassModelChatBlockedUser::fetch($Params['user_parameters_unordered']['remove_block']);
        $block->removeThis();
    } catch (Exception $e) {
        // Do nothing
    }
}

if (isset($_POST['AddBlock']))
{
	$definition = array(
			'IPToBlock' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'string'
			)			
	);

	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('chat/blockedusers');
		exit;
	}

	$form = new ezcInputForm( INPUT_POST, $definition );
	$Errors = array();

	if ( $form->hasValidData( 'IPToBlock' ) && $form->IPToBlock != '' ) {
		$ipBlock = new erLhcoreClassModelChatBlockedUser();
		$ipBlock->ip = $form->IPToBlock;
		$ipBlock->user_id = erLhcoreClassUser::instance()->getUserID();
		$ipBlock->datets = time();
		$ipBlock->saveThis();
		$tpl->set('block_saved',true);
	} else {
		$tpl->set('errors',array(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Please enter an IP to block')));
	}
}



$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('chat/blockedusers');
$pages->items_total = erLhcoreClassModelChatBlockedUser::getCount();
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();

if ($pages->items_total > 0) {
    $items = erLhcoreClassModelChatBlockedUser::getList(array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id ASC'));
}

$tpl->set('items',$items);
$tpl->set('pages',$pages);



$Result['content'] = $tpl->fetch();
$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Blocked users')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.blockedusres_path',array('result' => & $Result));

?>