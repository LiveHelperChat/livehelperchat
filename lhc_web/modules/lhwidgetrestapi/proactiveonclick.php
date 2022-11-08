<?php

header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
header('Content-type: text/javascript');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s',time()+60*60*8 ) . ' GMT' );
header('Cache-Control: no-store, no-cache, must-revalidate' );
header('Cache-Control: post-check=0, pre-check=0', false );
header('Pragma: no-cache' );

$htmlCode = '';

$invitation = erLhAbstractModelProactiveChatInvitation::fetch($Params['user_parameters']['id']);

if ($invitation instanceof erLhAbstractModelProactiveChatInvitation && isset($invitation->design_data_array['custom_on_click']) && $invitation->design_data_array['custom_on_click'] != '') {
    $htmlCode = $invitation->design_data_array['custom_on_click'];
}

if (!empty($htmlCode)) : ?>
function callback_<?php echo md5($htmlCode)?>() {
    <?php echo $htmlCode?>
}
<?php endif; exit; ?>