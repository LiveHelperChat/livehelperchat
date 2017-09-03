<?php

$tpl = erLhcoreClassTemplate::getInstance('lhabstract/new.tpl.php');

$objectClass = 'erLhAbstractModel'.$Params['user_parameters']['identifier'];
$objectData = new $objectClass;

$object_trans = $objectData->getModuleTranslations();

if (isset($object_trans['permission']) && !$currentUser->hasAccessTo($object_trans['permission']['module'],$object_trans['permission']['function'])) {
	erLhcoreClassModule::redirect();
	exit;
}

if (isset($_POST['CancelAction'])) {
    erLhcoreClassModule::redirect('abstract/list','/'.$Params['user_parameters']['identifier']);
    exit;
}

if ( isset($_POST['SaveClient']) || isset($_POST['UpdateClient']) ) {

	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect();
		exit;
	}

	$Errors = erLhcoreClassAbstract::validateInput($objectData);

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('abstract.before_created.'.strtolower($objectClass),array('object' => & $objectData, 'errors' => & $Errors));

    if (count($Errors) == 0)
    {
        if ( method_exists($objectData,'saveThis') ) {
            $objectData->saveThis();
        } else {
            erLhcoreClassAbstract::getSession()->save($objectData);
        }

        if (method_exists($objectData,'synchronizeAttribute')) {
            $objectData->synchronizeAttribute();
            erLhcoreClassAbstract::getSession()->update($objectData);
        }

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('abstract.created.'.strtolower($objectClass),array('object' => & $objectData));

        if ( isset($_POST['SaveClient']) ) {
        	erLhcoreClassModule::redirect('abstract/list','/'.$Params['user_parameters']['identifier']);
        	exit;
        }

        if ( isset($_POST['UpdateClient']) ) {
        	erLhcoreClassModule::redirect('abstract/edit','/'.$Params['user_parameters']['identifier'].'/'.$objectData->id);
        	exit;
        }

    }  else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('object',$objectData);
$tpl->set('object_trans',$object_trans);

if (method_exists($objectData,'customForm')) {
	$tpl->set('custom_form',$objectData->customForm());
}


$tpl->set('identifier',$Params['user_parameters']['identifier']);
$Result['content'] = $tpl->fetch();

if (method_exists($objectData,'dependCss')) {
	$Result['additional_header_css'] = $objectData->dependCss();
}

if (method_exists($objectData,'dependJs')) {
	$Result['additional_header_js'] = $objectData->dependJs();
}

if (method_exists($objectData,'dependFooterJs')) {
    $Result['additional_footer_js'] = $objectData->dependFooterJs();
}

if (isset($object_trans['path'])){
	$Result['path'][] = $object_trans['path'];
	$Result['path'][] = array('url' => erLhcoreClassDesign::baseurl('abstract/list').'/'.$Params['user_parameters']['identifier'], 'title' => $object_trans['name']);	
	$Result['path'][] = array('title' =>erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New'));
} else {
	$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')),
			array('url' => erLhcoreClassDesign::baseurl('abstract/list').'/'.$Params['user_parameters']['identifier'], 'title' => $object_trans['name']),
			array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New'))
	);
}

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('abstract.new_'.strtolower($Params['user_parameters']['identifier']).'_path', array('result' => & $Result));