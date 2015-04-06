<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/listchatconfig.tpl.php');

if ($currentUser->hasAccessTo('lhchat','administrateconfig')) {
	if (isset($_POST['UpdateConfig']))
	{
		foreach (erLhcoreClassModelChatConfig::getItems() as $item) {
				$ConfigData = erLhcoreClassModelChatConfig::fetch($item->identifier);
				
				switch ($ConfigData->type) {
					case erLhcoreClassModelChatConfig::SITE_ACCESS_PARAM_ON:
							
						$data = array();
						foreach (erConfigClassLhConfig::getInstance()->getSetting('site','available_site_access') as $siteaccess)
						{
							$data[$siteaccess] = $_POST[$item->identifier.'Value'.$siteaccess];
						}
						$ConfigData->value = serialize($data);
						break;
							
					case erLhcoreClassModelChatConfig::SITE_ACCESS_PARAM_OFF:
						$ConfigData->value = isset($_POST[$item->identifier.'ValueParam']) ? $_POST[$item->identifier.'ValueParam'] : 0;
						break;
			
					default:
						break;
				}
	
				$ConfigData->saveThis();
		}
		
		// Cleanup cache to recompile templates etc.
		$CacheManager = erConfigClassLhCacheConfig::getInstance();
		$CacheManager->expireCache();
		
		$tpl->set('updated','done');
	}
}

$tpl->set('currentUser',$currentUser);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Chat configuration')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.listchatconfig_path',array('result' => & $Result));

?>