<?php

/**
 * Paid chats workflow
 * */
class erLhcoreClassChatPaid {

    /**
     * Executes paid chat workflow
     * */
    public static function paidChatWorkflow($params)
    {
        $paidchatData = erLhcoreClassModelChatConfig::fetch('paidchat_data');
        $data = (array)$paidchatData->data;
        
        $mode = isset($params['mode']) ? $params['mode'] : 'chatwidgetchat';
        
        if (isset($data['paidchat_enabled']) && $data['paidchat_enabled'] == 1)
        {
            $secretHash = $data['paidchat_secret_hash'];

            $hashVerify = sha1($secretHash . sha1($secretHash . $params['uparams']['phash']));

            if ($hashVerify == $params['uparams']['pvhash']) {
                $chatExisting = erLhcoreClassModelChatPaid::findOne(array('filter' => array('hash' => $params['uparams']['phash'])));

                if ($chatExisting instanceof erLhcoreClassModelChatPaid) {
                    if ($chatExisting->chat_id > 0 && $chatExisting->chat instanceof erLhcoreClassModelChat) {
                        if ($chatExisting->chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
                            if (isset($data['paidchat_read_denied']) && $data['paidchat_read_denied'] == 1) {
                                erLhcoreClassModule::redirect('paidchat/expiredchat','/' .$chatExisting->id . '/(mode)/widget' . $params['append_mode'] . '/(pchat)/' . $chatExisting->id );
                            } else {
                                erLhcoreClassModule::redirect('chat/'.$mode,'/' .$chatExisting->chat->id . '/' . $chatExisting->chat->hash . '/(mode)/widget' . $params['append_mode'] . '/(pchat)/' . $chatExisting->id );
                            }
                            exit;
                        } else {
                            erLhcoreClassModule::redirect('chat/'.$mode,'/' .$chatExisting->chat->id . '/' . $chatExisting->chat->hash . '/(mode)/widget' . $params['append_mode'] . '/(pchat)/' . $chatExisting->id );
                            exit;
                        }
                    } elseif ($chatExisting->chat_id > 0) {
                        erLhcoreClassModule::redirect('paidchat/removedpaidchat');
                        exit;
                    }
                } else {
                    return array('need_store' => true, 'hash' => $params['uparams']['phash']);
                }
            } else {                
                erLhcoreClassModule::redirect('paidchat/invalidhash','/' .$chatExisting->chat->id . '/' . $chatExisting->chat->hash );
                exit;
            }
        }
        
        return array('need_store' => false);
    }

    /**
     * Process paid chat workflow
     * */
    public static function processPaidChatWorkflow($params)
    {
        if ($params['paid_chat_params']['need_store'] == true) {
            $paidChat = new erLhcoreClassModelChatPaid();
            $paidChat->chat_id = $params['chat']->id;
            $paidChat->hash = $params['paid_chat_params']['hash'];
            $paidChat->saveThis();
        }
    }

    /**
     * Opening chat widget
     * */
    public static function openChatWidget($params)
    {
        try {
            $chatPaid = erLhcoreClassModelChatPaid::fetch($params['pchat']);

            $paidchatData = erLhcoreClassModelChatConfig::fetch('paidchat_data');
            $data = (array)$paidchatData->data;

            if ((!isset($data['paidchat_read_denied']) || $data['paidchat_read_denied'] == 0) && $chatPaid->chat_id == $params['chat']->id) {
                $params['tpl']->set('paid_chat_params', array(
                    'allow_read' => true
                ));
            }

        } catch (Exception $e) {

        }
    }
}

?>