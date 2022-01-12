<?php

// php cron.php -s site_admin -c cron/mail/auto_close

foreach (erLhcoreClassModelMailconvMailbox::getList(['limit' => false, 'filter' => ['active' => 1]]) as $mailbox) {

    $workflowOptions = $mailbox->workflow_options_array;

    if (
        isset($workflowOptions['auto_close']) &&
        $workflowOptions['auto_close'] > 0 &&
        isset($workflowOptions['close_status']) &&
        is_array($workflowOptions['close_status']) &&
        !empty($workflowOptions['close_status'])
    ) {
        foreach (erLhcoreClassModelMailconvConversation::getList([
            'limit' => 500,
            'filterlt' => ['udate' => (time() - ($workflowOptions['auto_close'] * 24 * 3600))],
            'filterin' => ['status' => $workflowOptions['close_status']]
        ]) as $conversation) {
            echo $conversation->id,"\n";
            erLhcoreClassMailconvWorkflow::closeConversation(['conv' => & $conversation]);
        }
    }
}

?>