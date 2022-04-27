<?php
/**
 * php cron.php -s site_admin -c cron/test
 *
 * For various testing purposes
 *
 * */

$chat = erLhcoreClassModelChat::fetch(	1647599587);
erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.close',array('chat' => & $chat));

?>