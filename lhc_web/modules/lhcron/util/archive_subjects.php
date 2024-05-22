<?php

/**
 * php cron.php -s site_admin -c cron/util/archive_subjects -p remove
 *
 * */
echo "Starting subject archive process\n";

foreach (erLhcoreClassModelChatArchiveRange::getList() as $archiveRange) {
    $archiveRange->setTables();

    $db = ezcDbInstance::get();
    $stmt = $db->prepare( "SELECT min(id) as `min_id`, max(id) as `max_id` FROM `"  . erLhcoreClassModelChatArchive::$dbTable . "`");
    $stmt->execute();

    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "=========\nMoving to archive chat subjects - {$archiveRange->id}\n";
    // Insert subjects to archive tables
    $stmt = $db->prepare( "INSERT IGNORE INTO `" . erLhAbstractModelChatArchiveSubject::$dbTable . "` (id, subject_id, chat_id) SELECT id,subject_id,chat_id FROM lh_abstract_subject_chat WHERE chat_id >= :min_id AND chat_id <= :max_id");
    $stmt->bindValue(':min_id',$data['min_id'],PDO::PARAM_INT);
    $stmt->bindValue(':max_id',$data['max_id'],PDO::PARAM_INT);
    $stmt->execute();
    echo "Moved - " , $stmt->rowCount(),"\n";

    if ($cronjobPathOption->value == 'remove') {
        echo "Now removing from live table\n";
        // Remove moved subjects
        $stmt = $db->prepare( "DELETE FROM lh_abstract_subject_chat WHERE chat_id >= :min_id AND chat_id <= :max_id");
        $stmt->bindValue(':min_id',$data['min_id'],PDO::PARAM_INT);
        $stmt->bindValue(':max_id',$data['max_id'],PDO::PARAM_INT);
        $stmt->execute();
        echo "Removed - " , $stmt->rowCount(),"\n";
    }
}

?>