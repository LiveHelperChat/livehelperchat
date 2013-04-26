<?php

$tpl = erLhcoreClassTemplate::getInstance('lhabstract/edit.tpl.php');
$ObjectData = erLhcoreClassAbstract::getSession()->load( 'erLhAbstractModel'.$Params['user_parameters']['identifier'], (int)$Params['user_parameters']['object_id'] );

if (isset($_POST['CancelAction'])) {
    erLhcoreClassModule::redirect('abstract/list','/'.$Params['user_parameters']['identifier']);
    exit;
}

if (isset($_POST['SaveClient']) || isset($_POST['UpdateClient']))
{
    $Errors = erLhcoreClassAbstract::validateInput($ObjectData);
    if (count($Errors) == 0)
    {
        if ( method_exists($ObjectData,'updateThis') ) {
            $ObjectData->updateThis();
        } else {
            erLhcoreClassAbstract::getSession()->update($ObjectData);
        }

        $cache = CSCacheAPC::getMem();
        $cache->increaseCacheVersion('site_attributes_version');

		if (isset($_POST['SaveClient'])){
	        erLhcoreClassModule::redirect('abstract/list','/'.$Params['user_parameters']['identifier']);
	        exit;
		}

		$tpl->set('updated',true);

    }  else {
        $tpl->set('errors',$Errors);
    }
}


$tpl->set('object',$ObjectData);
$tpl->set('identifier',$Params['user_parameters']['identifier']);

$object_trans = $ObjectData->getModuleTranslations();
$tpl->set('object_trans',$object_trans);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')),
		array('url' => erLhcoreClassDesign::baseurl('abstract/list').'/EmailTemplate', 'title' => $object_trans['name']),
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Edit'))
);