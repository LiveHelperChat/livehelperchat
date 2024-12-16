<?php

$item =  erLhcoreClassModelMailconvMailbox::fetch($Params['user_parameters']['id']);

$cfg = erConfigClassLhConfig::getInstance();
$worker = $cfg->getSetting( 'webhooks', 'worker' );

if ($worker == 'resque' && class_exists('erLhcoreClassExtensionLhcphpresque')) {
    $inst_id = class_exists('erLhcoreClassInstance') ? erLhcoreClassInstance::$instanceChat->id : 0;
    erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_mailconv', 'erLhcoreClassMailConvWorker', array('inst_id' => $inst_id, 'mailbox_id' => $item->id));
} else {
    erLhcoreClassMailconvParser::syncMailbox($item);
}

?>