<?php
/**
 * Override this template for custom is_online_help functionality
 *
 * */
$isOnlineHelp = erLhcoreClassChat::isOnline($department_array,false,array('ignore_user_status'=> (int)erLhcoreClassModelChatConfig::fetch('ignore_user_status')->current_value, 'online_timeout' => (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'])); ?>