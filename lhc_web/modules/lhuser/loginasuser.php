<?php

$user = erLhcoreClassModelUser::fetch((int)$Params['user_parameters']['id']);

$hash = $Params['user_parameters_unordered']['hash'];
$ts = $Params['user_parameters_unordered']['ts'];

if ($user instanceof erLhcoreClassModelUser && $hash == sha1($user->id . '_' . $user->password . '_' . erConfigClassLhConfig::getInstance()->getSetting('site','secrethash') . '_' . $ts) && $ts > time() - 120) {
    erLhcoreClassUser::instance()->setLoggedUser($user->id);

    // change status instnatly of offline. To avoid any conflicts
    $db = ezcDbInstance::get();

    try {
        $db->beginTransaction();

        $user->hide_online = 1;

        erLhcoreClassUser::getSession()->update($user);

        erLhcoreClassUserDep::setHideOnlineStatus($user);

        erLhcoreClassChat::updateActiveChats($user->id);

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.operator_status_changed', array('user' => & $user, 'reason' => 'user_action'));

        $db->commit();
    }  catch (Exception $e) {
        $db->rollback();
    }

    erLhcoreClassModule::redirect('/');
    exit;
} else {
    $tpl = erLhcoreClassTemplate::getInstance( 'lhkernel/validation_error_autologin_user.tpl.php');
    $tpl->set('errors',array(erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Invalid hash or user')));
    $Result['content'] = $tpl->fetch();
    $Result['pagelayout'] = 'login';
}

?>