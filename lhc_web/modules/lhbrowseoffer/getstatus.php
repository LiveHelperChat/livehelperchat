<?php

header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');
header('Content-type: text/javascript');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s',time()+60*60*8 ) . ' GMT' );
header('Cache-Control: no-store, no-cache, must-revalidate' );
header('Cache-Control: post-check=0, pre-check=0', false );
header('Pragma: no-cache' );

$validUnits = array('pixels' => 'px','percents' => '%');

$referer = isset($_GET['r']) ? rawurldecode($_GET['r']) : '';
$location = isset($_GET['l']) ? rawurldecode($_GET['l']) : '';

$ignorable_ip = erLhcoreClassModelChatConfig::fetch('ignorable_ip')->current_value;

if ( $ignorable_ip == '' || !erLhcoreClassIPDetect::isIgnored(erLhcoreClassIPDetect::getIP(),explode(',',$ignorable_ip))) {
	$matched = erLhAbstractModelBrowseOfferInvitation::processInvitation(array('l' => $location,'r' => $referer, 'identifier' => (string)$Params['user_parameters_unordered']['identifier']));
	
	if ($matched !== false) {		
		$tpl = erLhcoreClassTemplate::getInstance('lhbrowseoffer/getstatus.tpl.php');
		$tpl->set('size',$matched->width > 0 ? $matched->width : ((!is_null($Params['user_parameters_unordered']['size']) && (int)$Params['user_parameters_unordered']['size'] >= 0) ? (int)$Params['user_parameters_unordered']['size'] : 450));
		$tpl->set('size_height',$matched->height > 0 ? $matched->height : (!is_null($Params['user_parameters_unordered']['height']) && (int)$Params['user_parameters_unordered']['height'] >= 0) ? (int)$Params['user_parameters_unordered']['height'] : 450);
		$tpl->set('units',key_exists((string)$matched->unit, $validUnits) ? $validUnits[$matched->unit] : (key_exists((string)$Params['user_parameters_unordered']['units'], $validUnits) ? $validUnits[(string)$Params['user_parameters_unordered']['units']] : 'px'));
		$tpl->set('invite',$matched);
		$tpl->set('showoverlay',(string)$Params['user_parameters_unordered']['showoverlay'] == 'true' ? true : false);	
		$tpl->set('canreopen',(string)$Params['user_parameters_unordered']['canreopen'] == 'true' ? true : false);	
		$tpl->set('timeout',(!is_null($Params['user_parameters_unordered']['timeout']) && (int)$Params['user_parameters_unordered']['timeout'] > 0) ? (int)$Params['user_parameters_unordered']['timeout'] : false);
		echo $tpl->fetch();
	}
}
exit;