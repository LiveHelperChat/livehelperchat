<?php

header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');
header('Content-type: text/javascript');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s',time()+60*60*8 ) . ' GMT' );
header('Cache-Control: no-store, no-cache, must-revalidate' );
header('Cache-Control: post-check=0, pre-check=0', false );
header('Pragma: no-cache' );

$theme = false;

if (isset($Params['user_parameters_unordered']['theme']) && (int)$Params['user_parameters_unordered']['theme'] > 0){
	$theme = (int)$Params['user_parameters_unordered']['theme'];
} else {
	$defaultTheme = erLhcoreClassModelChatConfig::fetch('default_theme_id')->current_value;
	if ($defaultTheme > 0) {
		$theme = (int)$defaultTheme;
	}
}


$tpl = erLhcoreClassTemplate::getInstance('lhchatbox/embed.tpl.php');
$tpl->set('heightchatcontent',(!is_null($Params['user_parameters_unordered']['chat_height']) && (int)$Params['user_parameters_unordered']['chat_height'] > 0) ? (int)$Params['user_parameters_unordered']['chat_height'] : 220);
$tpl->set('nick',erLhcoreClassChatbox::getVisitorName());
$tpl->set('theme',$theme);

echo $tpl->fetch();
exit;