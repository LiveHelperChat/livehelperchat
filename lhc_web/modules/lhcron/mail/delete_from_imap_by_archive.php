<?php

/*
 * php cron.php -s site_admin -c cron/mail/delete_from_imap_by_archive -p <archive_id>_<message_id>
 * // Calls purge erLhcoreClassMailconvParser::purgeMessage() on archive messages;
 * */

// Lock filter object
$db = ezcDbInstance::get();

list($archiveId,$lastId) = explode('_',$cronjobPathOption->value);

$archive = \LiveHelperChat\Models\mailConv\Archive\Range::fetch($archiveId);
$archive->setTables();

echo "Calling purgeMessage message on archive\n";

$pageLimit = 50;

for ($i = 0; $i < 100000; $i++) {

    echo "Processing msg - ",($i + 1),"\n";

    $messages = \LiveHelperChat\Models\mailConv\Archive\Message::getList(array('offset' => 0, 'filtergt' => array('id' => $lastId), 'limit' => $pageLimit, 'sort' => 'id ASC'));
    end($messages);
    $lastMessage = current($messages);

    if (!is_object($lastMessage)) {
        exit;
    }

    $lastId = $lastMessage->id;

    echo $lastId , '-' , count($messages),"\n";

    if (empty($messages)) {
        exit;
    }

    foreach ($messages as $message) {
        erLhcoreClassMailconvParser::purgeMessage($message, true);
    }
}



?>
