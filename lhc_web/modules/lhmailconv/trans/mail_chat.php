<?php

echo json_encode(array(
    "mail" => [
        "sender" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Sender'),
        "status" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Status'),
        "close" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Close'),
        "interactions_history" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Interactions history'),
        "print" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Print'),
        "transfer_chat" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Transfer chat'),
        "closed_at" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Closed at'),
        "responded_at" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Responded at'),
        "interaction_time" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Interaction time'),
        "priroity" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Priority'),
        "chat_owner" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Chat owner'),
        "received" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Received at'),
        "accepted_at" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Accepted at'),
        "department" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Department'),
        "information" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Information'),
        "remarks" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Remarks'),
        "send_fetching" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Send. Your send message will appear here... You can close this conversation in any case.'),
        "accepted_by" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Accepted by'),
        "accept_wait_time" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Accept wait time'),
        "response_wait_time" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Response wait time'),
    ],
    "status" => [
        "pending" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Pending'),
        "active" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Active'),
        "closed" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Closed')
    ],
    "msg" => [
        "reply" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Reply'),
        "forward" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Forward'),
        "download" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Download'),
        "no_reply" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','No reply required'),
        "info" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Message information'),
        "from" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','from'),
        "to" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','to'),
        "reply_to" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','reply-to'),
        "mailed_by" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','mailed-by'),
        "nrr" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','No reply required'),
        "orm" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','This is our response message'),
        "rbe" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Responeded by e-mail'),
        "ago" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','ago'),
        "ar_label" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Add/Remove label'),
        "send" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Send'),
        "sending" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Sending...'),
        "click_to_remove" => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Click to remove'),
    ],
    "r" => [
        'recipients' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Recipients'),
        'to' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','To'),
        'email' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','E-mail'),
        'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Recipient name'),
    ],
    "file" => [
        'incorrect_type' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Incorrect file type'),
        'to_big_file' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','File to big'),
        'uploading' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Uploading'),
        'choose_uploaded' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Choose file from uploaded files'),
        'drop_here' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Drop your files here or choose a new file')
    ]
));

?>