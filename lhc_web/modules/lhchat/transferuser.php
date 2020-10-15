<?php

if (is_numeric( $Params['user_parameters']['chat_id']) && is_numeric($Params['user_parameters']['item_id']))
{
    $db = ezcDbInstance::get();
    $db->beginTransaction();
    try {

        $Chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
        $errors = array();

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_chat_transfered', array('chat' => & $Chat, 'errors' => & $errors));

        if ( erLhcoreClassChat::hasAccessToRead($Chat) && empty($errors) )
        {
            $currentUser = erLhcoreClassUser::instance();

            if ( isset($_POST['type']) && $_POST['type'] == 'change_owner' ) {

                if ($currentUser->hasAccessTo('lhchat','changeowner')) {

                    $user = erLhcoreClassModelUser::fetch($Params['user_parameters']['item_id']);

                    if ($user instanceof erLhcoreClassModelUser)
                    {
                        $msg = new erLhcoreClassModelmsg();
                        $msg->chat_id = $Chat->id;
                        $msg->user_id = -1;
                        $msg->time = time();

                        $msg->name_support = (string)$user->name_support;
                        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_admin_saved', array('msg' => & $msg, 'chat' => & $chat, 'user_id' => $user->id));
                        $nickTo = $msg->name_support;

                        $msg->name_support = (string)$currentUser->getUserData()->name_support;
                        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_admin_saved', array('msg' => & $msg, 'chat' => & $chat, 'user_id' => $currentUser->getUserID()));
                        $msg->msg = (string)$msg->name_support . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferuser', 'has changed owner to') . ' ' . $nickTo;

                        $msg->saveThis();

                        $oldUserId = 0;

                        if ($Chat->user_id > 0) {
                            $oldUserId = $Chat->user_id;
                        }

                        $Chat->last_msg_id = $msg->id;
                        $Chat->last_user_msg_time = time();
                        $Chat->user_id = $user->id;
                        $Chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_OWNER_CHANGED;
                        $Chat->saveThis();

                        erLhcoreClassChat::updateActiveChats($Chat->user_id);

                        if ($oldUserId > 0) {
                            erLhcoreClassChat::updateActiveChats($oldUserId);
                        }

                        $tpl = erLhcoreClassTemplate::getInstance('lhkernel/alert_success.tpl.php');
                        $tpl->set('msg', erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferuser', 'Chat owner was changed to') . ' ' . $user->name_support);

                        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_owner_changed', array('chat' => & $Chat, 'user' => $user));

                        echo json_encode(['error' => 'false', 'result' => $tpl->fetch(), 'chat_id' => $Params['user_parameters']['chat_id']]);
                    } else {
                        throw new Exception('User could not be found!');
                    }

                } else {
                    throw new Exception('You do not have permission to change owner!');
                }

            } else {

                // Delete any existing transfer for this chat already underway
                $transferLegacy = erLhcoreClassTransfer::getTransferByChat($Params['user_parameters']['chat_id']);

                if (is_array($transferLegacy)) {
                    $chatTransfer = erLhcoreClassTransfer::getSession()->load('erLhcoreClassModelTransfer', $transferLegacy['id']);
                    erLhcoreClassTransfer::getSession()->delete($chatTransfer);
                }

                $Transfer = new erLhcoreClassModelTransfer();
                $Transfer->chat_id = $Chat->id;
                $Transfer->ctime = time();

                $msg = new erLhcoreClassModelmsg();
                $msg->chat_id = $Chat->id;
                $msg->user_id = -1;

                if (isset($_POST['type']) && $_POST['type'] == 'dep') {
                    $transferConfiguration = erLhcoreClassModelChatConfig::fetch('transfer_configuration')->data;
                    $dep = erLhcoreClassModelDepartament::fetch($Params['user_parameters']['item_id']);

                    $Transfer->dep_id = $dep->id; // Transfer was made to department

                    if (isset($transferConfiguration['change_department']) && $transferConfiguration['change_department'] == true) {
                        $Chat->dep_id = $Transfer->dep_id;
                    }

                    if (isset($transferConfiguration['make_pending']) && $transferConfiguration['make_pending'] == true) {
                        $Chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
                    }

                    if (isset($transferConfiguration['make_unassigned']) && $transferConfiguration['make_unassigned'] == true) {
                        $Chat->user_id = 0;
                    }

                    $msg->name_support = (string)$currentUser->getUserData()->name_support;
                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_admin_saved', array('msg' => & $msg, 'chat' => & $chat, 'user_id' => $currentUser->getUserID()));

                    $msg->msg = (string)$msg->name_support . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferuser', 'has transferred chat to') . ' ' . (string)$dep . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferuser', 'department');

                } else {
                    $Transfer->transfer_to_user_id = $Params['user_parameters']['item_id']; // Transfer was made to user

                    $userTo = erLhcoreClassModelUser::fetch($Transfer->transfer_to_user_id);
                    $msg->name_support = $userTo->name_support;
                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_admin_saved', array('msg' => & $msg, 'chat' => & $chat, 'user_id' => $userTo->id));
                    $userToNick = $msg->name_support;

                    $msg->name_support = (string)$currentUser->getUserData()->name_support;
                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_admin_saved', array('msg' => & $msg, 'chat' => & $chat, 'user_id' => $currentUser->getUserID()));
                    $msg->msg = (string)$msg->name_support . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferuser', 'has transferred chat to') . ' ' . (string)$userToNick;
                }

                $Chat->last_user_msg_time = $msg->time = time();

                // Original department id
                $Transfer->from_dep_id = $Chat->dep_id;

                // User which is transferring
                $Transfer->transfer_user_id = $currentUser->getUserID();

                erLhcoreClassTransfer::getSession()->save($Transfer);

                $tpl = erLhcoreClassTemplate::getInstance('lhkernel/alert_success.tpl.php');
                if (isset($_POST['type']) && $_POST['type'] == 'dep') {
                    $tpl->set('msg', erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferuser', 'Chat was assigned to selected department'));
                } else {
                    $tpl->set('msg', erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferuser', 'Chat was assigned to selected user'));
                }

                // Save message
                erLhcoreClassChat::getSession()->save($msg);

                // User who transferred chat
                $Chat->last_msg_id = $msg->id;
                $Chat->transfer_uid = $currentUser->getUserID();
                $Chat->saveThis();

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_transfered', array('chat' => & $Chat, 'transfer' => $Transfer));

                echo json_encode(['error' => 'false', 'result' => $tpl->fetch(), 'chat_id' => $Params['user_parameters']['chat_id']]);
            }

        } elseif (!empty($errors)) {
            $tpl = erLhcoreClassTemplate::getInstance('lhkernel/validation_error.tpl.php');
            $tpl->set('errors', $errors);
            echo json_encode(['error' => 'false', 'result' => $tpl->fetch(), 'chat_id' => $Params['user_parameters']['chat_id']]);
        }
        $db->commit();
    } catch (Exception $e) {
        $db->rollback();

        $tpl = erLhcoreClassTemplate::getInstance('lhkernel/validation_error.tpl.php');
        $tpl->set('errors', array($e->getMessage()));
        echo json_encode(['error' => 'false', 'result' => $tpl->fetch(), 'chat_id' => $Params['user_parameters']['chat_id']]);
    }
}
exit;
?>
