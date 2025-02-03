<?php
/**
 * php cron.php -s site_admin -c cron/syncmail
 *
 * Run every 10 minits or so. On this cron depends automatic chat transfer and unaswered chats callback.
 *
 * */

$mailbox = erLhcoreClassModelMailconvMailbox::getList(['customfilter' => [
    '(`last_sync_time` < '. time() . ' - `sync_interval`)',
    '(NOT (`sync_status` = ' . erLhcoreClassModelMailconvMailbox::SYNC_PROGRESS . ' AND `sync_started` > 0 AND (' . time() . ' - `sync_started`) < 4800))'], // 80 minutes
    'filter' => ['active' => 1]]);

$cfg = erConfigClassLhConfig::getInstance();
$worker = $cfg->getSetting( 'webhooks', 'worker' );

foreach ($mailbox as $mail) {
    if ($worker == 'resque' && class_exists('erLhcoreClassExtensionLhcphpresque')) {
        $inst_id = class_exists('erLhcoreClassInstance') ? erLhcoreClassInstance::$instanceChat->id : 0;
        erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_mailconv', 'erLhcoreClassMailConvWorker', array('inst_id' => $inst_id, 'mailbox_id' => $mail->id));
    } else {
        erLhcoreClassMailconvParser::syncMailbox($mail);
    }
}

if (class_exists('erLhcoreClassExtensionLhcphpresque')) {
    $inst_id = class_exists('erLhcoreClassInstance') ? erLhcoreClassInstance::$instanceChat->id : 0;
    \erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_imap_copy', '\LiveHelperChat\mailConv\workers\SentCopyWorker', array('inst_id' => $inst_id));
}

?>