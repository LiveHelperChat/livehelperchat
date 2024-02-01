<?php

namespace LiveHelperChat\Helpers;

class ChatDuration
{
    public static function getChatDurationToUpdateChatID($chat, $updateParticipants = false, & $logDuration = [], & $mainStats = []) {

        //@todo Include meta messages
        $sql = 'SELECT `lh_msg`.`time`, `lh_msg`.`user_id`, `lh_msg`.`meta_msg` FROM `lh_msg` WHERE `lh_msg`.`chat_id` = :chat_id /*AND lh_msg.user_id != -1*/  ORDER BY `id` ASC';// AND lh_msg.id >= 2878699
        $db = \ezcDbInstance::get();
        $stmt = $db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL));
        $stmt->bindValue(':chat_id',$chat->id);
        $stmt->execute();

        $timeout_user = \erLhcoreClassModelChatConfig::fetch('cduration_timeout_user')->current_value;
        $timeout_operator = \erLhcoreClassModelChatConfig::fetch('cduration_timeout_operator')->current_value;

        $params = array(
            'timeout_user' => ($timeout_user > 0 ? $timeout_user : 4)*60,// How long operator can wait for message from visitor before delay between messages are ignored
            'timeout_operator' => ($timeout_operator > 0 ? $timeout_operator : 10)*60
        );

        $lastVisitorMessage = null;
        $previousMessage = null;
        $timeToAdd = 0;
        $timeToAddParticipant = 0;

        $previousOwner = null;
        $statusOperators = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_NEXT)) {

            $metaAction = false;
            $userLastMessage = false;

            if ($row['user_id'] == -1 && $row['meta_msg'] != '') {

                $metaData = json_decode($row['meta_msg'],true);

                if (isset($metaData['content']['accept_action']['user_id'])) {
                    $row['user_id'] = $metaData['content']['accept_action']['user_id'];
                    $userLastMessage = true;
                    $metaAction = true;
                } elseif (isset($metaData['content']['transfer_action_dep']['user_id'])) {
                    $row['user_id'] = $metaData['content']['transfer_action_dep']['user_id'];
                    $metaAction = true;
                } elseif (isset($metaData['content']['change_owner_action']['destination_user_id'])) {
                    $row['user_id'] = $metaData['content']['change_owner_action']['destination_user_id'];
                    $metaAction = true;
                } elseif (isset($metaData['content']['change_dep_action']['user_id'])) {
                    $row['user_id'] = $metaData['content']['change_dep_action']['user_id'];
                    $metaAction = true;
                } elseif (isset($metaData['content']['transfer_action_user']['user_id'])) {
                    $row['user_id'] = $metaData['content']['transfer_action_user']['user_id'];
                    $metaAction = true;
                } else {
                    continue; // Not supported
                }

            } elseif ($row['user_id'] == -1) { // Some other system message
                continue;
            }

            if (($lastVisitorMessage === null && $row['user_id'] == 0) || ($userLastMessage === true)) {
                $lastVisitorMessage = $row;
            }

            if ($previousMessage === null) {
                $previousMessage = $row;
                continue;
            }

            $ownerChanged = false;

            if ($previousOwner == null || ($previousOwner != $row['user_id'] && $row['user_id'] != 0)) {
                $previousOwner = $row['user_id'];
                $timeToAddParticipant = 0;

                if ($row['user_id'] != -2) {
                    $ownerChanged = true;

                    if (!isset($statusOperators[$previousOwner])) {
                        $statusOperators[$previousOwner] = 0;
                    }

                    $timeToAddParticipant = $statusOperators[$previousOwner];
                }
            }

            if ($row['user_id'] == 0) {
                $timeout = $params['timeout_user'];
            } else {
                $timeout = $params['timeout_operator'];
            }

            // calculations below this point
            if ($lastVisitorMessage !== null && $row['user_id'] > 0 && $metaAction === false &&/*$ownerChanged === false &&*/ $row['time'] > ($chat->pnd_time + $chat->wait_time) ) {
                $responseTime = $row['time'] - ($lastVisitorMessage['time'] < ($chat->pnd_time + $chat->wait_time) ? ($chat->pnd_time + $chat->wait_time) : $lastVisitorMessage['time']);
                $mainStats['response_times'][$row['user_id']][] = $responseTime;
                $mainStats['response_times_total'][] = $responseTime;
                $lastVisitorMessage = null;
            }

            $diff = $row['time'] - $previousMessage['time'];

            if ($diff < $timeout && $diff > 0) {
                $timeToAdd += $diff;
                if ($ownerChanged === false) {
                    $timeToAddParticipant += $diff;
                }
            }

            // We can include message if
            if ($previousOwner == $row['user_id'] || $row['user_id'] == 0) {
                $logDuration[] = $previousOwner . " P_USER_ID -- " . $timeToAddParticipant . " TTA -- " . date('H:i:s',$row['time']) . ' MSG_TIME'; // @debug

                // Valid message
                $statusOperators[$previousOwner] = $timeToAddParticipant;
            } else { // Message author changed, reset spend time
                $timeToAddParticipant = 0;
            }

            $previousMessage = $row;
        }

        if ($updateParticipants === true) {
            for ( $i = 0; $i < 3; $i++) {
                try {
                    $db->beginTransaction();
                    $db->query('DELETE FROM `lh_chat_participant` WHERE `chat_id` = ' . (int)$chat->id);

                    $insertParts = [];
                    foreach ($statusOperators as $participantId => $duration) {
                        $frt = $aart = $mart = 0;
                        if (isset($mainStats['response_times'][$participantId][0])) {
                            $frt = $mainStats['response_times'][$participantId][0];
                            $aart = round(array_sum($mainStats['response_times'][$participantId]) / count($mainStats['response_times'][$participantId]));
                            $mart = max($mainStats['response_times'][$participantId]);
                        }
                        $insertParts[] = '(' . (int)$chat->id . ',' . $participantId . ',' . $duration . ',' . $chat->time . ',' . $chat->dep_id . ',' . $frt . ',' . $aart . ',' . $mart . ')';
                    }

                    if (!empty($insertParts)) {
                        $db->query('INSERT INTO `lh_chat_participant` (`chat_id`,`user_id`,`duration`,`time`,`dep_id`,`frt`,`aart`,`mart`) VALUES ' . implode(',',$insertParts));
                    }

                    $db->commit();
                    break;
                } catch (\Exception $e) {
                    $db->rollback();
                    sleep(1);
                }
            }
        }

        return $timeToAdd;
    }

    public static function setChatTimes(& $chat) {

        $logStats = [];
        $mainStats = [];

        $chat->chat_duration = \LiveHelperChat\Helpers\ChatDuration::getChatDurationToUpdateChatID($chat, true, $logStats, $mainStats);

        $chat->frt = 0;

        if (isset($mainStats['response_times_total'][0])) {
            $chat->frt = $mainStats['response_times_total'][0];
        }

        if (isset($mainStats['response_times_total'])) {
            $chat->aart = round(array_sum($mainStats['response_times_total']) / count($mainStats['response_times_total']));
            $chat->mart = max($mainStats['response_times_total']);
        }
    }

}