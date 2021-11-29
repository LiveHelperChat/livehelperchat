<?php

class erLhcoreClassMailconv {

    public static function getSession() {
        if (! isset ( self::$persistentSession )) {
            self::$persistentSession = new ezcPersistentSession ( ezcDbInstance::get (), new ezcPersistentCodeManager ( './pos/lhmailconv' ) );
        }
        return self::$persistentSession;
    }

    public static $messagesAttributes = [
        'udate_front',
        'udate_ago',
        'body_front',
        'plain_user_name',
        'accept_time_front',
        'lr_time_front',
        'wait_time_pending',
        'wait_time_response',
        'interaction_time_duration',
        'cls_time_front',
        'delivery_status_keyed',
        'to_data_front',
        'reply_to_data_front',
        'cc_data_front',
        'attachments',
        'bcc_data_front',
        'conv_duration_front',
        'subjects'
    ];

    public static $messagesAttributesRemove = [
        'user',
        'conversation',
        'files',
        'delivery_status'
    ];

    public static $conversationAttributesRemove = [
        'department',
        'user',
        'mailbox'
    ];

    public static $conversationAttributes = [
        'plain_user_name',
        'can_delete',
        'udate_front',
        'department_name',
        'accept_time_front',
        'cls_time_front',
        'wait_time_pending',
        'wait_time_response',
        'lr_time_front',
        'conv_duration_front',
        'interaction_time_duration',
        'mailbox_front',
    ];

    private static $persistentSession;
}

?>