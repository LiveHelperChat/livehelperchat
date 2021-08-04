<?php

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.blockedusers', array());

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/blockedusers.tpl.php');

if (is_numeric($Params['user_parameters_unordered']['remove_block'])) {
    try {

    	if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    		die('Invalid CSRF Token');
    		exit;
    	}

        $block = erLhcoreClassModelChatBlockedUser::fetch($Params['user_parameters_unordered']['remove_block']);

    	if ($block instanceof erLhcoreClassModelChatBlockedUser){
            $block->removeThis();
        }

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

if (isset($_POST['AddBlockEmail']))
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
		$ipBlock->ip = '127.0.0.1';
		$ipBlock->nick = $form->IPToBlock;
		$ipBlock->user_id = erLhcoreClassUser::instance()->getUserID();
		$ipBlock->datets = time();
		$ipBlock->btype = erLhcoreClassModelChatBlockedUser::BLOCK_EMAIL;
		$ipBlock->saveThis();
		$tpl->set('block_saved',true);
	} else {
		$tpl->set('errors',array(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Please enter an IP to block')));
	}
}

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'block_search','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'block_search','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('chat/blockedusers').$append;
$pages->items_total = erLhcoreClassModelChatBlockedUser::getCount($filterParams['filter']);
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();

if ($pages->items_total > 0) {
    $items = erLhcoreClassModelChatBlockedUser::getList(array_merge_recursive($filterParams['filter'],array('offset' => $pages->low, 'limit' => $pages->items_per_page)));
}

$tpl->set('items',$items);
$tpl->set('pages',$pages);

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('chat/blockedusers');

$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Blocked users')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.blockedusres_path',array('result' => & $Result));

?>