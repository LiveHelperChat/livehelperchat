<?php

// For IE to support headers if chat is installed on different domain
header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');

$cache = CSCacheAPC::getMem();
$cacheKey = md5('chatbox_version_'.$cache->getCacheVersion('chatbox_'.(string)$Params['user_parameters_unordered']['identifier']).'_hash_'.(string)$Params['user_parameters_unordered']['hashchatbox'].erLhcoreClassChatbox::getVisitorName().'_height_'.(int)$Params['user_parameters_unordered']['chat_height'].'_sound_'.(int)$Params['user_parameters_unordered']['sound'].'_mode_'.(string)$Params['user_parameters_unordered']['mode'].'_siteaccess_'.erLhcoreClassSystem::instance()->SiteAccess);

header('Cache-Control: must-revalidate'); // must-revalidate
header('ETag: ' . $cacheKey);

$iftag = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] == $cacheKey : null;
if ($iftag === true)
{
	echo "asdad";exit;
	header ("HTTP/1.0 304 Not Modified");
	header ('Content-Length: 0');
	exit;
}

if (($Result = $cache->restore($cacheKey)) === false)
{
	$referer = '';
	$tpl = erLhcoreClassTemplate::getInstance( 'lhchatbox/chatwidget.tpl.php');
	$tpl->set('chatbox_chat_height',(!is_null($Params['user_parameters_unordered']['chat_height']) && (int)$Params['user_parameters_unordered']['chat_height'] > 0) ? (int)$Params['user_parameters_unordered']['chat_height'] : 220);
	
	if ($Params['user_parameters_unordered']['sound'] !== null && is_numeric($Params['user_parameters_unordered']['sound'])) {
		erLhcoreClassModelUserSetting::setSetting('chat_message',(int)$Params['user_parameters_unordered']['sound'] == 1 ? 1 : 0);
	}
	
	$chatbox = erLhcoreClassChatbox::getInstance((string)$Params['user_parameters_unordered']['identifier'],(string)$Params['user_parameters_unordered']['hashchatbox']);
	$tpl->set('chatbox',$chatbox);
	
	$tpl->set('referer',$referer);
	if (isset($_GET['URLReferer']))
	{
		$referer = $_GET['URLReferer'];
	    $tpl->set('referer',$referer);
	}
	
	if (isset($_POST['URLRefer']))
	{
		$referer = $_POST['URLRefer'];
	    $tpl->set('referer',$_POST['URLRefer']);
	}
	
	$embedMode = false;
	$modeAppend = '';
	if ((string)$Params['user_parameters_unordered']['mode'] == 'embed') {
		$embedMode = true;
		$modeAppend = '/(mode)/embed';
	}
	
	$tpl->set('append_mode',$modeAppend);
	
	$Result['content'] = $tpl->fetch();
	$Result['pagelayout'] = 'widget';
	$Result['pagelayout_css_append'] = 'widget-chat';
	$Result['dynamic_height'] = true;
	$Result['dynamic_height_message'] = 'lhc_sizing_chatbox';
	$Result['dynamic_height_append'] = 20;
	$Result['additional_post_message'] = 'lhc_chb:nick:'.htmlspecialchars(erLhcoreClassChatbox::getVisitorName(),ENT_QUOTES);
	$Result['is_sync_required'] = true;
	
	if ($embedMode == true) {
		$Result['dynamic_height_message'] = 'lhc_sizing_chatbox_page';
		$Result['pagelayout_css_append'] = 'embed-widget';
	}

	$cache->store($cacheKey,$Result);
}
?>