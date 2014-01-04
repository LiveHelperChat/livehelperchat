<?php
/**
 * Override this template for custom is_online_help functionality
 *
 * */
$isOnlineHelp = erLhcoreClassChat::isOnline($department,array('online_timeout' => (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'])); ?>