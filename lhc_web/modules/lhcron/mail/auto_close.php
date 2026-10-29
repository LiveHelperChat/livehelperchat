<?php

// php cron.php -s site_admin -c cron/mail/auto_close

$id = '';
if (class_exists('erLhcoreClassInstance')) {
    $id = \erLhcoreClassInstance::$instanceChat->id;
}

$fp = fopen("cache/cron_mail_auto_close{$id}.lock", "w+");

// Gain the lock
if (!flock($fp, LOCK_EX | LOCK_NB)) {
    echo "Couldn't get the lock! Another process is already running";
    fclose($fp);
    return;
} else {
    chmod("cache/cron_mail_auto_close{$id}.lock", 0666);
    echo "Lock acquired. Starting process!";
}

foreach (erLhcoreClassModelMailconvMailbox::getList(['limit' => false, 'filter' => ['active' => 1]]) as $mailbox) {

    $workflowOptions = $mailbox->workflow_options_array;

    if (
        isset($workflowOptions['auto_close']) &&
        $workflowOptions['auto_close'] > 0 &&
        isset($workflowOptions['close_status']) &&
        is_array($workflowOptions['close_status']) &&
        !empty($workflowOptions['close_status'])
    ) {
        $filter = [
            'limit' => 100,
            'filter' => ['mailbox_id' => $mailbox->id],
            'filterlt' => ['udate' => (time() - ($workflowOptions['auto_close'] * 24 * 3600))],
            'filterin' => ['status' => $workflowOptions['close_status']]
        ];

        // Do not close conversations which still have unanswered messages
        if (isset($workflowOptions['close_answered_only']) && $workflowOptions['close_answered_only'] == 1) {
            $filter['customfilter'] = ['NOT EXISTS (SELECT 1 FROM `lhc_mailconv_msg` WHERE `lhc_mailconv_msg`.`conversation_id` = `lhc_mailconv_conversation`.`id` AND `lhc_mailconv_msg`.`status` != ' . erLhcoreClassModelMailconvMessage::STATUS_RESPONDED . ')'];
        }

        foreach (erLhcoreClassModelMailconvConversation::getList($filter) as $conversation) {
            echo $conversation->id,"\n";
            erLhcoreClassMailconvWorkflow::closeConversation(['conv' => & $conversation, 'close_source' => 'auto_close']);
        }
    }
}

flock($fp, LOCK_UN); // release the lock
fclose($fp);

?>