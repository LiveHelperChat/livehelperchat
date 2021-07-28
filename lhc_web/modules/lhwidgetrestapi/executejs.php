<?php

header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
header('Content-type: text/javascript');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time() + 60 * 60 * 8) . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

$tpl = erLhcoreClassTemplate::getInstance('lhwidgetrestapi/executejs.tpl.php');

if (is_numeric($Params['user_parameters_unordered']['id'])) {
    $chat = erLhcoreClassModelChat::fetch($Params['user_parameters_unordered']['id']);
    if (!($chat instanceof erLhcoreClassModelChat) || $chat->hash != $Params['user_parameters_unordered']['hash']) {
        // Invalid hash
        exit;
    } else {
        $tpl->set('chat',$chat);
    }
}

if (is_array($Params['user_parameters_unordered']['dep']) && !empty($Params['user_parameters_unordered']['dep'])) {
    erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['dep']);
    $tpl->set('dep',$Params['user_parameters_unordered']['dep']);
}

$tpl->set('ext',$Params['user_parameters_unordered']['ext']);

echo $tpl->fetch();

exit;
?>