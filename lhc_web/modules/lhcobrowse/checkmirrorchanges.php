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
        $errors = [];
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('cobrowse.before_check_mirror_changes', array('browse' => & $browse, 'errors' => & $errors));

        if (empty($errors)) {
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
        } else {
            $browse->initialize = '';
            $browse->modifications = '';
            $browse->finished = 1;
            $browse->saveThis();

            array_unshift($errors, erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse', 'Co-browse is stopped!'));
            $changes = [];
            $changes[] = ['clear' => true];
            $changes[] = ['error_msg' => implode(PHP_EOL, $errors)];
            $changes[] = array('lmsg' => $browse->mtime > 0 ? $browse->mtime_front : '');
            $changes[] = array('finished' => array('status' => !$browse->is_sharing, 'text' => $browse->is_sharing == 0 ? erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse', 'Screen sharing session has finished') : erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse', 'Screen is shared')));
            array_unshift($changes, ['base' => $browse->url]);
            echo json_encode($changes);
        }
    }
}

exit;
?>