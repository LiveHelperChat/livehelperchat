<?php

header('Content-Type: application/json');

if (is_numeric($Params['user_parameters']['id']))
{
    $chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['id']);
    if ( erLhcoreClassChat::hasAccessToRead($chat) )
    {
        $vvcall = erLhcoreClassModelChatVoiceVideo::getInstance($chat->id);

        if ($Params['user_parameters_unordered']['action'] == 'end') {
            $vvcall->op_status = erLhcoreClassModelChatVoiceVideo::STATUS_OP_PENDING;
            $vvcall->vi_status = erLhcoreClassModelChatVoiceVideo::STATUS_VI_PENDING;
            $vvcall->status = erLhcoreClassModelChatVoiceVideo::STATUS_PENDING;
            $vvcall->updateThis(array('update' => array('op_status','status','vi_status')));
        } else if ($Params['user_parameters_unordered']['action'] == 'leave') {
            $vvcall->op_status = erLhcoreClassModelChatVoiceVideo::STATUS_OP_PENDING;
            $vvcall->updateThis(array('update' => array('op_status')));
        } else if ($Params['user_parameters_unordered']['action'] == 'join') {
            $vvcall->op_status = erLhcoreClassModelChatVoiceVideo::STATUS_OP_JOINED;
            $vvcall->token = '0063ec24f9a1e0649839ea488d685ef2f35IAAKEznoUGC3I0iwM2Dx2PGH/xu7EIvu3pAwEkpYYqJxLQEGzJ0AAAAAEACpE93IXIkBYAEAAQBeiQFg';
            // 10488039_a2b92671541d0175d98b91f39d5179cff8c91d6b
            $vvcall->updateThis(array('update' => array('op_status','token')));
        } else if ($Params['user_parameters_unordered']['action'] == 'letvisitorin') {
            $vvcall->vi_status = erLhcoreClassModelChatVoiceVideo::STATUS_VI_JOINED;
            $vvcall->status = erLhcoreClassModelChatVoiceVideo::STATUS_CONFIRMED;
            $vvcall->updateThis(array('update' => array('vi_status','status')));
        }

        echo json_encode($vvcall->getState());
    }
}

exit;


?>