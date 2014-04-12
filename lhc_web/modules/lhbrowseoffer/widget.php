<?php

// For IE to support headers if chat is installed on different domain
header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');

$referer = '';
$dynamic_url = '';
$identifier = '';

$tpl = erLhcoreClassTemplate::getInstance( 'lhbrowseoffer/widget.tpl.php');

$invitationHash = (string)$Params['user_parameters']['hash'];
$invitation = erLhAbstractModelBrowseOfferInvitation::getList(array('filter' => array('hash' => $invitationHash)));

if (!empty($invitation)){	
	$invite = array_shift($invitation);
	$tpl->set('invite',$invite);
	
	$Result['content'] = $tpl->fetch();
	$Result['pagelayout'] = 'widget';
	$Result['dynamic_height'] = true;
	$Result['dynamic_height_message'] = 'lhc_sizing_browseoffer';
	$Result['dynamic_height_append'] = 10;
	$Result['pagelayout_css_append'] = 'embed-widget';
} else {
	exit;
}