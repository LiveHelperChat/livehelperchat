<?php

if (is_numeric($Params['user_parameters']['chat_id'])) {
    /*
     * If online user mode we have to make different check
     * */
    $browse = false;
    if ($Params['user_parameters_unordered']['cobrowsemode'] == 'onlineuser') {
        $onlineUser = erLhcoreClassModelChatOnlineUser::fetch($Params['user_parameters']['chat_id']);
        $browse = erLhcoreClassCoBrowse::getBrowseInstanceByOnlineUser($onlineUser);
    } else {
        $chat = erLhcoreClassChat::getSession()->load('erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
        if (erLhcoreClassChat::hasAccessToRead($chat)) {
            $browse = erLhcoreClassCoBrowse::getBrowseInstance($chat);
        }
    }

    if ($browse instanceof erLhcoreClassModelCoBrowse) {
        if ($browse->modifications != '') {
            $changes = json_decode($browse->modifications);
            $changes[] = array('url' => $browse->url);
            $changes[] = array('lmsg' => $browse->mtime > 0 ? $browse->mtime_front : '');
            $changes[] = array('finished' => array('status' => !$browse->is_sharing, 'text' => $browse->is_sharing == 0 ? erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse', 'Screen sharing session has finished') : erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse', 'Screen is shared')));
            if ($browse->finished == 1) {
                $changes[] = array('clear' => true);
            }
            array_unshift($changes, array('base' => $browse->url));
            echo json_encode($changes);
            $browse->modifications = '';
            $browse->saveThis();
        } else {
            $changes = array();
            $changes[] = array('lmsg' => $browse->mtime > 0 ? $browse->mtime_front : '');
            $changes[] = array('finished' => array('status' => !$browse->is_sharing, 'text' => $browse->is_sharing == 0 ? erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse', 'Screen sharing session has finished') : erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse', 'Screen is shared')));
            if ($browse->finished == 1) {
                $changes[] = array('clear' => true);
            }
            array_unshift($changes, array('base' => $browse->url));
            echo json_encode($changes);
        }
    }
}

exit;
?>