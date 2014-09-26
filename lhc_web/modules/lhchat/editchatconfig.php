<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/editchatconfig.tpl.php');

// If already set during account update
$ConfigData = erLhcoreClassModelChatConfig::fetch($Params['user_parameters']['config_id']);

if (isset($_POST['UpdateConfig']))
{
	switch ($ConfigData->type) {
		case erLhcoreClassModelChatConfig::SITE_ACCESS_PARAM_ON:
			
				$data = array();
				foreach (erConfigClassLhConfig::getInstance()->getSetting('site','available_site_access') as $siteaccess)
				{
					$data[$siteaccess] = $_POST['Value'.$siteaccess];
				}					
				$ConfigData->value = serialize($data);
			break;
			
		case erLhcoreClassModelChatConfig::SITE_ACCESS_PARAM_OFF:
				$ConfigData->value = $_POST['ValueParam'];
			break;
	
		default:
			break;
	}

    $ConfigData->saveThis();
	$tpl->set('data_updated',true);
	
	// Cleanup cache to recompile templates etc.
	$CacheManager = erConfigClassLhCacheConfig::getInstance();
    $CacheManager->expireCache();

}

$tpl->set('systemconfig',$ConfigData);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('chat/listchatconfig'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','List chat configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/editchatconfig','Edit')))

?>