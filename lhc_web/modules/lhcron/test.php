<?php
/**
 * php cron.php -s site_admin -c cron/test
 *
 * For various testing purposes
 *
 * */

var_dump(\LiveHelperChat\Helpers\ChatDuration::getChatDurationToUpdateChatID(erLhcoreClassModelChat::fetch(1647600173), true));



?>