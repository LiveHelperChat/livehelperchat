<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/getonlineuserinfo.tpl.php');

$onlineUser = erLhcoreClassModelChatOnlineUser::fetch($Params['user_parameters']['id']);

if (!is_object($onlineUser)) {
    $onlineUser = new erLhcoreClassModelChatOnlineUser();
} else {
    $userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID(), $currentUser->cache_version);
    if ($userDepartments !== true && !$currentUser->hasAccessTo('lhchat','sees_all_online_visitors') && !in_array($onlineUser->dep_id, $userDepartments)) {
        $tpl->setFile('lhchat/errors/adminchatnopermission.tpl.php');
        if (isset($_GET['popup'])) {
            $Result['content'] = $tpl->fetch();
            $Result['pagelayout'] = 'chattabs';
        } else {
            $Result['content'] = $tpl->fetch();
            $Result['modal_header_title'] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat', 'No permission');
            $Result['pagelayout'] = 'modal';
        }
        return;
    }
}

$tpl->set('online_user',$onlineUser);
$tpl->set('tab',$Params['user_parameters_unordered']['tab']);
$tpl->set('chat_id_present',$Params['user_parameters_unordered']['chat_id']);

if (isset($_GET['popup'])) {
    $tpl->set('popup',true);
    $Result['content'] = $tpl->fetch();
    $Result['pagelayout'] = 'chattabs';
} else {
    echo $tpl->fetch();
    exit;
}


?>