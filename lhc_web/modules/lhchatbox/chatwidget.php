<?php

// For IE to support headers if chat is installed on different domain
header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');

$referer = '';
$tpl = erLhcoreClassTemplate::getInstance( 'lhchatbox/chatwidget.tpl.php');
$tpl->set('chatbox_chat_height',(!is_null($Params['user_parameters_unordered']['chat_height']) && (int)$Params['user_parameters_unordered']['chat_height'] > 0) ? (int)$Params['user_parameters_unordered']['chat_height'] : 220);

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

if ($embedMode == false) {
	// Store status, if user reloads page etc, we show widget
	CSCacheAPC::getMem()->setSession('lhc_chatbox_is_opened',1);
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'widget';
$Result['pagelayout_css_append'] = 'widget-chat';
$Result['dynamic_height'] = true;
$Result['dynamic_height_message'] = 'lhc_sizing_chatbox';
$Result['dynamic_height_append'] = 20;

if ($embedMode == true) {
	$Result['dynamic_height_message'] = 'lhc_sizing_chatbox_page';
	$Result['pagelayout_css_append'] = 'embed-widget';
}

?>