<?php

header('content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

if ($Params['user_parameters_unordered']['hash'] != '' || $Params['user_parameters_unordered']['vid'] != '') {

    $checkHash = true;
    $vid = false;

    if ($Params['user_parameters_unordered']['hash'] != '') {
        list($chatID, $hash) = explode('_', $Params['user_parameters_unordered']['hash']);
    } else if ($Params['user_parameters_unordered']['hash_resume'] != '') {
        list($chatID, $hash) = explode('_', $Params['user_parameters_unordered']['hash_resume']);
    } elseif ($Params['user_parameters_unordered']['vid'] != '') {
        $vid = erLhcoreClassModelChatOnlineUser::fetchByVid($Params['user_parameters_unordered']['vid']);


        if ($vid !== false) {
            $chatID = $vid->chat_id;
            $checkHash = false;
        } else {
            echo json_encode(array('stored' => 'false'));
            exit;
        }
    };

    try {

        if ($chatID > 0) {
            $chat = erLhcoreClassChat::getSession()->load('erLhcoreClassModelChat', $chatID);
        } else {
            $chat = false;
        }

        if ((($checkHash == true && $chat !== false && $chat->hash == $hash) || $checkHash == false) && (is_object($vid) || ($chat !== false && $chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT))) {

            if (isset($_POST['data'])) {
                $errors = array();
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.storescreenshot.before_store', array('errors' => & $errors, 'chat' => & $chat, 'data' => $_POST['data']));

                if (!empty($errors) && $chat) {
                    $chat->support_informed = 1;
                    $chat->user_typing = time();
                    $chat->is_user_typing = 1;
                    $chat->user_typing_txt = htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Screenshot store error').': '.implode('; ', $errors),ENT_QUOTES);
                    $chat->saveThis();
                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.sync_back_office');

                    echo json_encode(array('stored' => 'false'));
                    exit;
                }

                $imgData = base64_decode(str_replace('data:image/png;base64,', '', $_POST['data']));

                $fileData = erLhcoreClassModelChatConfig::fetch('file_configuration');
                $data = (array)$fileData->data;

                if (strlen($imgData) < $data['fs_max'] * 1024 && $imgData != '') {

                    $storageID = false;
                    if ($chat !== false) {
                        $path = 'var/storage/' . date('Y') . 'y/' . date('m') . '/' . date('d') . '/' . $chat->id . '/';
                        $storageID = $chat->id;
                    } else {
                        $path = 'var/storage/' . date('Y') . 'y/' . date('m') . '/' . date('d') . '/' . $vid->id . '/';
                        $storageID = $vid->id;
                    }

                    $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.storescreenshot.screenshot_path', array('path' => & $path, 'storage_id' => $storageID));
                    $fileNameHash = sha1($imgData . time());

                    if ($response === false) {
                        erLhcoreClassFileUpload::mkdirRecursive($path);
                    }

                    file_put_contents($path . $fileNameHash, $imgData);
                    $imageSize = getimagesize($path . $fileNameHash);

                    if ($imageSize) {
                        try {
                            $db = ezcDbInstance::get();
                            $db->beginTransaction();

                            if ($chat !== false && $chat->screenshot !== false) {
                                $chat->screenshot->removeThis();
                                $chat->screenshot_id = 0;
                            }

                            if (is_object($vid) && $vid->screenshot !== false) {
                                $vid->screenshot->removeThis();
                                $vid->screenshot_id = 0;
                            }

                            $fileUpload = new erLhcoreClassModelChatFile();
                            $fileUpload->size = strlen($imgData);
                            $fileUpload->type = 'image/png';
                            $fileUpload->name = $fileNameHash;
                            $fileUpload->date = time();
                            $fileUpload->user_id = 0;
                            $fileUpload->upload_name = 'screenshot.png';
                            $fileUpload->file_path = $path;

                            if ($chat !== false) {
                                $fileUpload->chat_id = $chat->id;
                            } else {
                                $fileUpload->chat_id = 0;
                            }

                            $fileUpload->extension = 'png';
                            $fileUpload->saveThis();

                            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.storescreenshot.store', array('chat_file' => & $fileUpload));

                            if ($chat !== false) {

                                $msg = new erLhcoreClassModelmsg();
                                $msg->msg = '[file=' . $fileUpload->id . '_' . md5($fileUpload->name . '_' . $fileUpload->chat_id) . ']';
                                $msg->chat_id = $chat->id;
                                $msg->user_id = -1;

                                $chat->last_user_msg_time = $msg->time = time();

                                erLhcoreClassChat::getSession()->save($msg);

                                if ($chat->last_msg_id < $msg->id) {
                                    $chat->last_msg_id = $msg->id;
                                }

                                $chat->user_typing_txt = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot', 'Screenshot ready...');
                                $chat->user_typing = time();

                                $chat->screenshot_id = $fileUpload->id;
                                $chat->updateThis();

                                // Force operators to check for new messages
                                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.messages_added_passive', array(
                                    'chat' => & $chat,
                                    'msg' => & $msg,
                                ));

                                // Force operators to check for new messages
                                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.screenshot_ready', array(
                                    'chat' => & $chat,
                                    'msg' => & $msg,
                                    'file' => & $fileUpload
                                ));
                            }

                            if ($chat !== false && $chat->online_user !== false) {
                                $chat->online_user->screenshot_id = $fileUpload->id;
                                $chat->online_user->saveThis();
                            } elseif (is_object($vid)) {
                                $vid->screenshot_id = $fileUpload->id;
                                $vid->saveThis();
                            }

                            $db->commit();


                            echo json_encode(array('stored' => 'true'));
                            exit;

                        } catch (Exception $e) {
                            $db->rollback();
                        }
                    }
                }
            }
        }
    } catch (Exception $e) {
        // Do nothing
    }
}

echo json_encode(array('stored' => 'false'));
exit;
?>