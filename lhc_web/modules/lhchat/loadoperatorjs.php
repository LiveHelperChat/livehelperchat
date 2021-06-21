<?php

header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
header('Content-type: text/javascript');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s',time()+60*60*8 ) . ' GMT' );
header('Cache-Control: no-store, no-cache, must-revalidate' );
header('Cache-Control: post-check=0, pre-check=0', false );
header('Pragma: no-cache' );

if (isset($Params['user_parameters_unordered']['type']) && $Params['user_parameters_unordered']['type'] == 'chat') {
    $chat = erLhcoreClassModelChat::fetch($Params['user_parameters_unordered']['id']);
    if ($chat instanceof erLhcoreClassModelChat) {
        echo $chat->operation_admin;
        $chat->operation_admin = '';
        $chat->updateThis(array('update' => array('operation_admin')));
    }
} else {
    $userData = $currentUser->getUserData();
    echo $userData->operation_admin;
    $userData->operation_admin = '';
    erLhcoreClassUser::getSession()->update($userData);
}

exit;

?>