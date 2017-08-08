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

            if ( isset($_POST['type']) && $_POST['type'] == 'dep' ) {
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

                $msg->msg = (string) $currentUser->getUserData() . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferuser', 'has transferred chat to').' ' . (string)$dep . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferuser', 'department');

            } else {
                $Transfer->transfer_to_user_id = $Params['user_parameters']['item_id']; // Transfer was made to user

                $userTo = erLhcoreClassModelUser::fetch($Transfer->transfer_to_user_id);
                $msg->msg = (string) $currentUser->getUserData() . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferuser', 'has transferred chat to').' ' . (string)$userTo;
            }

            $Chat->last_user_msg_time = $msg->time = time();

            // Original department id
            $Transfer->from_dep_id = $Chat->dep_id;

            // User which is transferring
            $Transfer->transfer_user_id = $currentUser->getUserID();

            erLhcoreClassTransfer::getSession()->save($Transfer);

            $tpl = erLhcoreClassTemplate::getInstance('lhkernel/alert_success.tpl.php');
            if ( isset($_POST['type']) && $_POST['type'] == 'dep' ) {
                $tpl->set('msg',erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferuser','Chat was assigned to selected department'));
            } else {
                $tpl->set('msg',erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferuser','Chat was assigned to selected user'));
            }

            // Save message
            erLhcoreClassChat::getSession()->save($msg);

            // User who transferred chat
            $Chat->last_msg_id = $msg->id;
            $Chat->transfer_uid = $currentUser->getUserID();
            $Chat->saveThis();

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_transfered',array('chat' => & $Chat));

            echo json_encode(['error' => 'false', 'result' => $tpl->fetch(), 'chat_id' => $Params['user_parameters']['chat_id']]);

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
