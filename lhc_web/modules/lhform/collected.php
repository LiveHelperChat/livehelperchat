<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhform/collected.tpl.php');

$form = erLhAbstractModelForm::fetch((int)$Params['user_parameters']['form_id']);

if (is_numeric($Params['user_parameters_unordered']['id']) && $Params['user_parameters_unordered']['action'] == 'delete'){

	// Delete selected canned message
	try {
		if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
			die('Invalid CSRF Token');
			exit;
		}
		$collected = erLhAbstractModelFormCollected::fetch((int)$Params['user_parameters_unordered']['id']);
		$collected->removeThis();
	} catch (Exception $e) {
		// Do nothing
	}

	erLhcoreClassModule::redirect('form/collected','/'.$form->id);
	exit;
}

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('form/collected').'/'.$form->id;
$pages->items_total = erLhAbstractModelFormCollected::getCount(array('filter' => array('form_id' => $form->id)));
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
	$items = erLhAbstractModelFormCollected::getList(array('filter' => array('form_id' => $form->id),'offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id DESC'));
}

$tpl->set('items',$items);
$tpl->set('pages',$pages);

$tpl->set('form',$form);
$Result['content'] = $tpl->fetch();

$object_trans = $form->getModuleTranslations();
$Result['path'][] =  $object_trans['path'];
$Result['path'][] = array('url' => erLhcoreClassDesign::baseurl('abstract/list').'/Form','title' => $object_trans['name']);
$Result['path'][] = array('title' => (string)$form);

?>