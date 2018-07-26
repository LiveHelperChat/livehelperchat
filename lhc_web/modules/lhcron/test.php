<?php
/**
 * php cron.php -s site_admin -c cron/test
 *
 * For various testing purposes
 *
 * */
$chat = erLhcoreClassModelChat::fetch(7906);
$chat->invitation_id = 4;
$chat->saveThis();

?>