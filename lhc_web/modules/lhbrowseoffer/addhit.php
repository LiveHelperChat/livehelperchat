<?php 

$invitationHash = (string)$Params['user_parameters']['hash'];
$invitation = erLhAbstractModelBrowseOfferInvitation::getList(array('filter' => array('hash' => $invitationHash)));

if (!empty($invitation)){	
	$invite = array_shift($invitation);
	$invite->executed_times++;
	$invite->saveThis();
}

exit;
?>