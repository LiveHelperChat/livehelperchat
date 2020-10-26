<?php

class erLhcoreClassMailconvWorkflow {

    public static function closeConversation($params) {

        $conv = $params['conv'];

        if ($conv->status == erLhcoreClassModelMailconvConversation::STATUS_CLOSED) {
            return;
        }

        if (isset($params['user_id']) && is_numeric($params['user_id'])) {
            if ($conv->user_id == 0) {
                $conv->user_id = $params['user_id'];
            }
        }

        $conv->cls_time = time();

        if ($conv->accept_time == 0) {
            $conv->accept_time = time();
        }

        $conv->wait_time = $conv->accept_time - $conv->pnd_time;

        if ($conv->lr_time == 0) {
            $conv->lr_time = time();
        }

        $conv->response_time = $conv->lr_time - $conv->accept_time;
        $conv->interaction_time = $conv->cls_time - $conv->accept_time;
        $conv->status = erLhcoreClassModelMailconvConversation::STATUS_CLOSED;
        $conv->saveThis();

        $messages = erLhcoreClassModelMailconvMessage::getList(['filter' => ['conversation_id' => $conv->id]]);

        foreach ($messages as $message) {

            if ($message->status != erLhcoreClassModelMailconvMessage::STATUS_RESPONDED)
            {
                $message->status = erLhcoreClassModelMailconvMessage::STATUS_RESPONDED;
                $message->response_type = erLhcoreClassModelMailconvMessage::RESPONSE_NOT_REQUIRED;
            }

            if ($message->lr_time == 0) {
                $message->lr_time = time();
            }

            if ($message->cls_time == 0) {
                $message->cls_time = time();
            }

            if ($message->accept_time == 0) {
                $message->accept_time = time();
            }

            if ($message->response_time == 0) {
                $message->response_time = $message->lr_time - $message->accept_time;
            }

            if ($message->interaction_time == 0) {
                $message->interaction_time = $message->cls_time - $message->accept_time;
            }

            if ($message->wait_time == 0) {
                $message->wait_time = $message->accept_time - $message->ctime;
            }

            if ($message->user_id == 0) {
                $message->user_id = $conv->user_id;
            }

            $message->updateThis();
        }
    }
}

?>