<?php

namespace LiveHelperChat\mailConv\Archive;

use LiveHelperChat\Models\mailConv\Archive as ArchiveModels;

class Archive
{

    /**
     * @desc returns archive chat object if id is found
     * @param $chatId
     * @return array|null
     * @throws ezcDbHandlerNotFoundException
     */
    public static function fetchMailById($chatId) {

        if (!is_numeric($chatId)){
            return null;
        }

        $chatsId[$chatId] = array();
        self::setArchiveAttribute($chatsId);

        if ($chatsId[$chatId]['archive'] == 1 && is_numeric($chatsId[$chatId]['archive_id'])) {
            $archive = ArchiveModels\Range::fetch($chatsId[$chatId]['archive_id']);

            if ($archive instanceof ArchiveModels\Range) {
                $archive->setTables();
                $chat = ArchiveModels\Conversation::fetch($chatId, true, true);
                return array('archive' => $archive, 'mail' => $chat);
            }
        }

        return null;
    }

    /**
     * @param array $chatsId
     * @return array
     * @throws ezcDbHandlerNotFoundException
     */
    public static function setArchiveAttribute(array & $chatsId)
    {
        if (empty($chatsId)) {
            return;
        }

        $db = \ezcDbInstance::get();

        $stmt = $db->prepare('SELECT id FROM lhc_mailconv_conversation WHERE id IN ( ' . implode(',', array_keys($chatsId)) . ' )');
        $stmt->execute();
        $chatsLiveId = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        $archivedChats = array();

        foreach ($chatsId as $chatId => & $data) {
            if (in_array($chatId, $chatsLiveId)) {
                $data['archive'] = false;
            } else {
                $data['archive'] = true;
                $archivedChats[] = $chatId;
            }
        }

        $archivesRanges = array();

        $backupArchives = \LiveHelperChat\Models\mailConv\Archive\Range::getList(['limit' => false, 'filter' => ['type' => 1]]);

        foreach ($archivedChats as $archiveChatId) {

            $archiveId = null;

            foreach ($backupArchives as $backupArchive) {
                if ($backupArchive->inArchive($archiveChatId)) {
                    $archiveId = $backupArchive->id;
                    break;
                }
            }

            if ($archiveId == null && (empty($archivesRanges) || ($archiveId = self::isInRange($archiveChatId, $archivesRanges)) === null)) {
                $stmt = $db->prepare('SELECT `id`,`first_id`,`last_id` FROM `lh_mail_archive_range` WHERE :chat_id_1 <= `last_id` && :chat_id_2 >= `first_id` AND `type` = 0 LIMIT 1');
                $stmt->bindValue(':chat_id_1', $archiveChatId);
                $stmt->bindValue(':chat_id_2', $archiveChatId);
                $stmt->execute();
                $dataArchive = $stmt->fetch(\PDO::FETCH_ASSOC);



                if (is_array($dataArchive)) {
                    $archivesRanges[] = $dataArchive;
                    $archiveId = $dataArchive['id'];
                } else {
                    $archiveId = null;
                }
            }

            if (is_numeric($archiveId)) {
                $chatsId[$archiveChatId]['archive_id'] = $archiveId;
            } else {
                $chatsId[$archiveChatId]['archive_id'] = null;
            }
        }

        return $chatsId;
    }

    public static function isInRange($chatId, $ranges)
    {
        foreach ($ranges as $range) {
            if ($chatId >= $range['first_id'] && $chatId <= $range['last_id']) {
                return $range['id'];
            }
        }

        return null;
    }
}