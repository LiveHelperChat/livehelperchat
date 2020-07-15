<?php
/**
 * php cron.php -s site_admin -c cron/syncmail
 *
 * Run every 10 minits or so. On this cron depends automatic chat transfer and unaswered chats callback.
 *
 * */

$mailbox = erLhcoreClassModelMailconvMailbox::getList(['filter' => ['active' => 1]]);

foreach ($mailbox as $mail) {
    erLhcoreClassMailconvParser::syncMailbox($mail);
}

?>