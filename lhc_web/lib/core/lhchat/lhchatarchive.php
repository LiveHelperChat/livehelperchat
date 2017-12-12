<?php

class erLhcoreClassChatArcive
{
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

        $db = ezcDbInstance::get();

        $stmt = $db->prepare('SELECT id FROM lh_chat WHERE id IN ( ' . implode(',', array_keys($chatsId)) . ' )');
        $stmt->execute();
        $chatsLiveId = $stmt->fetchAll(PDO::FETCH_COLUMN);

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

        foreach ($archivedChats as $archiveChatId) {

            if (empty($archivesRanges) || ($archiveId = self::isInRange($archiveChatId, $archivesRanges)) === null) {
                $stmt = $db->prepare('SELECT id,first_id,last_id FROM lh_chat_archive_range WHERE :chat_id_1 <= last_id && :chat_id_2 >= first_id LIMIT 1');
                $stmt->bindValue(':chat_id_1', $archiveChatId);
                $stmt->bindValue(':chat_id_2', $archiveChatId);
                $stmt->execute();
                $dataArchive = $stmt->fetch(PDO::FETCH_ASSOC);

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