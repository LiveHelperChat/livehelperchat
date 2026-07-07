<?php

class erLhcoreClassChatArcive
{

    /**
     * @desc returns archive chat object if id is found
     * @param $chatId
     * @return array|null
     * @throws ezcDbHandlerNotFoundException
     */
    public static function fetchChatById($chatId, $useCache = true, $trowException = true) {
        $chatsId[$chatId] = array();
        self::setArchiveAttribute($chatsId);

        if ($chatsId[$chatId]['archive'] == 1 && is_numeric($chatsId[$chatId]['archive_id'])) {
            $archive = erLhcoreClassModelChatArchiveRange::fetch($chatsId[$chatId]['archive_id']);

            if ($archive instanceof erLhcoreClassModelChatArchiveRange) {
                $archive->setTables();
                $chat = erLhcoreClassModelChatArchive::fetch($chatId, $useCache, $trowException);
                return array('archive' => $archive, 'chat' => $chat);
            }
        }

        return null;
    }

    /**
     * @desc Returns pending messages for archive chat
     * @param $chat_id
     * @param $message_id
     * @return array
     * @throws ezcDbHandlerNotFoundException
     */
    public static function getPendingMessages($chat_id, $message_id)
    {
        $db = ezcDbInstance::get();
        $stmt = $db->prepare("SELECT " . erLhcoreClassModelChatArchiveRange::$archiveMsgTable .".* FROM " . erLhcoreClassModelChatArchiveRange::$archiveMsgTable ." INNER JOIN ( SELECT id FROM " . erLhcoreClassModelChatArchiveRange::$archiveMsgTable ." WHERE chat_id = :chat_id AND id > :message_id ORDER BY id ASC) AS items ON " . erLhcoreClassModelChatArchiveRange::$archiveMsgTable .".id = items.id");
        $stmt->bindValue( ':chat_id',$chat_id,PDO::PARAM_INT);
        $stmt->bindValue( ':message_id',$message_id,PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        return $rows;
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

            $archiveId = null;

            // Collect all matching ranges from cache first
            $matchingRanges = self::getMatchingRanges($archiveChatId, $archivesRanges);

            // If no cached ranges match, query the database
            if (empty($matchingRanges)) {
                $stmt = $db->prepare('SELECT id, first_id, last_id FROM lh_chat_archive_range WHERE :chat_id <= last_id AND :chat_id >= first_id');
                $stmt->bindValue(':chat_id', $archiveChatId);
                $stmt->execute();
                $matchingRanges = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            // If multiple ranges match (overlap), verify by checking the archive table
            if (count($matchingRanges) > 1) {
                foreach ($matchingRanges as $dataArchive) {
                    if (self::chatExistsInArchive($db, $dataArchive['id'], $archiveChatId)) {
                        $archiveId = $dataArchive['id'];

                        if (! self::isRangeCached($dataArchive['id'], $archivesRanges)) {
                            $archivesRanges[] = $dataArchive;
                        }
                        break;
                    }
                }
            } elseif (count($matchingRanges) === 1) {
                $dataArchive = reset($matchingRanges);
                $archiveId = $dataArchive['id'];

                if (! self::isRangeCached($dataArchive['id'], $archivesRanges)) {
                    $archivesRanges[] = $dataArchive;
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

    /**
     * Returns all cached ranges that could contain the given chat ID.
     * When ranges overlap, multiple ranges may match.
     */
    private static function getMatchingRanges($chatId, $ranges)
    {
        $matched = array();
        foreach ($ranges as $range) {
            if ($chatId >= $range['first_id'] && $chatId <= $range['last_id']) {
                $matched[] = $range;
            }
        }
        return $matched;
    }

    private static function isRangeCached($rangeId, $ranges)
    {
        foreach ($ranges as $r) {
            if ($r['id'] == $rangeId) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifies whether a chat actually exists in a specific archive table.
     * This resolves ambiguity when archive ranges overlap.
     */
    private static function chatExistsInArchive($db, $archiveRangeId, $chatId)
    {
        $tableName = 'lh_chat_archive_' . (int)$archiveRangeId;
        $stmt = $db->prepare("SELECT 1 FROM `{$tableName}` WHERE id = :chat_id LIMIT 1");
        $stmt->bindValue(':chat_id', $chatId, PDO::PARAM_INT);
        $stmt->execute();
        return (bool)$stmt->fetchColumn();
    }
}