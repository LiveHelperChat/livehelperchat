<?php 

header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
header('Content-type: text/javascript');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s',time()+60*60*8 ) . ' GMT' );
header('Cache-Control: no-store, no-cache, must-revalidate' );
header('Cache-Control: post-check=0, pre-check=0', false );
header('Pragma: no-cache' );

$invitationHash = (string)$Params['user_parameters']['hash'];
$invitation = erLhAbstractModelBrowseOfferInvitation::getList(array('filter' => array('hash' => $invitationHash)));

if (!empty($invitation)){	
	$invite = array_shift($invitation);
	$invite->executed_times++;
	$invite->saveThis();
}

exit;
?>