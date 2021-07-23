<?php

$item =  erLhcoreClassModelMailconvMailbox::fetch($Params['user_parameters']['id']);

$cfg = erConfigClassLhConfig::getInstance();
$worker = $cfg->getSetting( 'webhooks', 'worker' );

if ($worker == 'resque' && class_exists('erLhcoreClassExtensionLhcphpresque')) {
    erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_mailconv', 'erLhcoreClassMailConvWorker', array('mailbox_id' => $item->id));
} else {
    erLhcoreClassMailconvParser::syncMailbox($item);
}

?>