<?php

namespace LiveHelperChat\Helpers;

class Reaction
{
    public static function operatorReaction($chat, $message, $paramsPayload) {

        $db = \ezcDbInstance::get();
        $db->reconnect();

        $db->beginTransaction();

        $message->syncAndLock();

        $metaMessage = $message->meta_msg_array;

        $currentPart = isset($metaMessage['content']['reactions']['current'][$paramsPayload['payload-id']]) ? $metaMessage['content']['reactions']['current'][$paramsPayload['payload-id']] : null;

        $action = 'remove';
        $identifier = '';
        $valueAction = '';

        // Same reaction icon was clicked unselect if it was selected
        if ($currentPart === $paramsPayload['payload']) {
            unset($metaMessage['content']['reactions']['current'][$paramsPayload['payload-id']]);
            if (empty($metaMessage['content']['reactions']['current'])) {
                unset($metaMessage['content']);
            }
            $identifier = $paramsPayload['payload-id'];
        } else {
            // Operator can react only thumb only, extensions might add more support if required
            $validIdentifiers['thumb'] = [0,1];
            if (key_exists($paramsPayload['payload-id'],$validIdentifiers) && in_array($paramsPayload['payload'],$validIdentifiers[$paramsPayload['payload-id']])) {
                $metaMessage['content']['reactions']['current'][$paramsPayload['payload-id']] = (string)$paramsPayload['payload'];
                $action = 'add';
                $valueAction = (string)$paramsPayload['payload'];
            }
        }

        $message->meta_msg_array = $metaMessage;
        $message->meta_msg = json_encode($message->meta_msg_array);
        $message->updateThis(['update' => ['meta_msg']]);

        $db->commit();

        // This we need for frontend visitor to update UI
        $chat->operation = "lhinst.updateMessageRow({$message->id});\n";
        $chat->updateThis(['update' => ['operation']]);

        // Dispatch reaction action for extensions
        \erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.reaction_operator', array(
            'reaction_identifier' => $identifier,
            'reaction_value' => $valueAction,
            'action' => $action,
            'msg' => & $message,
            'chat' => & $chat
        ));

    }
}

?>