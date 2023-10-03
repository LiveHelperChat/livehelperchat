<?php
/**
 * php cron.php -s site_admin -c cron/test
 *
 * For various testing purposes
 *
 * */

$chat = erLhcoreClassModelChat::fetch(1647601140);

\LiveHelperChat\Helpers\ChatDuration::setChatTimes($chat);
$chat->updateThis();

?>