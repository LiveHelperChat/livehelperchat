<?php
/**
 * php cron.php -s site_admin -c cron/test
 *
 * For various testing purposes
 *
 * */

erLhcoreClassChatStatsResque::updateStats(erLhcoreClassModelDepartament::fetch(20));

?>