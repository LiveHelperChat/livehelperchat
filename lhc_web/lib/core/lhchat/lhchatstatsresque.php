<?php

class erLhcoreClassChatStatsResque {

    public function perform()
    {
        $db = ezcDbInstance::get();
        $db->reconnect(); // Because it timeouts automatically, this calls to reconnect to database, this is implemented in 2.52v

        if ($this->args['type'] == 'dep') {
            $dep = erLhcoreClassModelDepartament::fetch($this->args['id']);
            self::updateStats($dep);
        }
    }

    public static function updateStats($dep)
    {
        $db = ezcDbInstance::get();

        // Get max load for a specific department
        $stmt = $db->prepare('SELECT SUM(max_chats) as max_chats FROM (SELECT MAX(max_chats) as max_chats FROM `lh_userdep` WHERE dep_id = :dep_id AND lastd_activity > :last_activity GROUP BY user_id) as tmp;');
        $stmt->bindValue(':dep_id',$dep->id,PDO::PARAM_INT);
        $stmt->bindValue(':last_activity',time()-600, PDO::PARAM_INT);
        $stmt->execute();
        $maxChats = (int)$stmt->fetchColumn();

        $stmt = $db->prepare('SELECT SUM(`max_chats`) as `max_chats`, SUM(`active_chats`) AS `active_chats`, SUM(`inactive_chats`) AS `inactive_chats` FROM (SELECT MAX(`max_chats`) as `max_chats`,MAX(`inactive_chats`) AS `inactive_chats`,MAX(`active_chats`) AS `active_chats` FROM `lh_userdep` WHERE dep_id = :dep_id AND hide_online = 0 AND last_activity > :last_activity GROUP BY user_id) as tmp;');
        $stmt->bindValue(':dep_id',$dep->id,PDO::PARAM_INT);
        $stmt->bindValue(':last_activity',time()-300, PDO::PARAM_INT);
        $stmt->execute();
        $maxChatsHard = $stmt->fetch(PDO::FETCH_ASSOC);

        $statsChats = erLhcoreClassModelChat::getCount(array(
            'group' => '`status`, `status_sub`',
            'filter' => array(
                'dep_id' => $dep->id
            ),
            'filterin' => array(
                'status' => array(
                    erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,
                    erLhcoreClassModelChat::STATUS_PENDING_CHAT,
                    erLhcoreClassModelChat::STATUS_BOT_CHAT,
                )
            )
        ),
            'count', false, 'count(`id`) as `total`, `status`, `status_sub`', false, true
        );

        $dep->inactive_chats_cnt = $dep->bot_chats_counter = $dep->pending_chats_counter = $dep->active_chats_counter = 0;

        foreach ($statsChats as $statsChat) {
            if ($statsChat['status'] == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) {
                $dep->active_chats_counter += (int)$statsChat['total'];
            } elseif ($statsChat['status'] == erLhcoreClassModelChat::STATUS_PENDING_CHAT) {
                $dep->pending_chats_counter += (int)$statsChat['total'];
            } elseif ($statsChat['status'] == erLhcoreClassModelChat::STATUS_BOT_CHAT) {
                $dep->bot_chats_counter += (int)$statsChat['total'];
            }

            // Add to inactive chats if it's conditions matches
            if (in_array((int)$statsChat['status'],array(erLhcoreClassModelChat::STATUS_PENDING_CHAT, erLhcoreClassModelChat::STATUS_ACTIVE_CHAT)) && in_array((int)$statsChat['status_sub'],array(erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT, erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW))) {
                $dep->inactive_chats_cnt += $statsChat['total'];
            }
         }

        $dep->max_load = $maxChats;
        $dep->max_load_h = isset($maxChatsHard['max_chats']) ? $maxChatsHard['max_chats'] : 0;
        $dep->inop_chats_cnt = isset($maxChatsHard['inactive_chats']) ? $maxChatsHard['inactive_chats'] : 0;
        $dep->acop_chats_cnt = isset($maxChatsHard['active_chats']) ? $maxChatsHard['active_chats'] : 0;

        $dep->updateThis(array('update' => array('inop_chats_cnt','acop_chats_cnt','active_chats_counter','pending_chats_counter','bot_chats_counter','inactive_chats_cnt','max_load','max_load_h')));

        // Update departments groups statistic
        $depGroups = erLhcoreClassModelDepartamentGroupMember::getList(array('filter' => array('dep_id' => $dep->id)));

        foreach ($depGroups as $depGroup) {
            $stmt = $db->prepare('SELECT SUM(`max_chats`) AS `max_chats` FROM (SELECT MAX(`max_chats`) AS `max_chats` FROM `lh_userdep` WHERE `dep_id` IN (SELECT `dep_id` FROM `lh_departament_group_member` WHERE `dep_group_id` = :dep_group_id) AND `lastd_activity` > :last_activity GROUP BY `user_id`) as `tmp`;');
            $stmt->bindValue(':dep_group_id',$depGroup->dep_group_id,PDO::PARAM_INT);
            $stmt->bindValue(':last_activity',time()-600, PDO::PARAM_INT);
            $stmt->execute();
            $maxChats = (int)$stmt->fetchColumn();

            $stmt = $db->prepare('SELECT SUM(`max_chats`) AS `max_chats`, SUM(`active_chats`) AS `active_chats`, SUM(`inactive_chats`) AS `inactive_chats` FROM (SELECT MAX(`max_chats`) AS `max_chats`,MAX(`inactive_chats`) AS `inactive_chats`,MAX(`active_chats`) AS `active_chats` FROM `lh_userdep` WHERE `dep_id` IN (SELECT `dep_id` FROM `lh_departament_group_member` WHERE `dep_group_id` = :dep_group_id) AND hide_online = 0 AND `last_activity` > :last_activity GROUP BY `user_id`) as `tmp`;');
            $stmt->bindValue(':dep_group_id',$depGroup->dep_group_id,PDO::PARAM_INT);
            $stmt->bindValue(':last_activity',time()-300, PDO::PARAM_INT);
            $stmt->execute();
            $maxChatsHard = $stmt->fetch(PDO::FETCH_ASSOC);

            $depGroupObj = erLhcoreClassModelDepartamentGroup::fetch($depGroup->dep_group_id);

            if ($depGroupObj instanceof erLhcoreClassModelDepartamentGroup) {
                $statsChats = erLhcoreClassModelChat::getCount(array(
                    'group' => '`status`, `status_sub`',
                    'filterin' => array(
                        'status' => array(
                            erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,
                            erLhcoreClassModelChat::STATUS_PENDING_CHAT,
                            erLhcoreClassModelChat::STATUS_BOT_CHAT,
                        ),
                        'dep_id' => erLhcoreClassChat::getDepartmentsByDepGroup(array($depGroupObj->id))
                    )
                ),
                    'count', false, 'count(`id`) as `total`, `status`, `status_sub`', false, true
                );

                $depGroupObj->inachats_cnt = $depGroupObj->achats_cnt = $depGroupObj->pchats_cnt = $depGroupObj->bchats_cnt = 0;

                foreach ($statsChats as $statsChat) {
                    if ($statsChat['status'] == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) {
                        $depGroupObj->achats_cnt += (int)$statsChat['total'];
                    } elseif ($statsChat['status'] == erLhcoreClassModelChat::STATUS_PENDING_CHAT) {
                        $depGroupObj->pchats_cnt += (int)$statsChat['total'];
                    } elseif ($statsChat['status'] == erLhcoreClassModelChat::STATUS_BOT_CHAT) {
                        $depGroupObj->bchats_cnt += (int)$statsChat['total'];
                    }

                    // Add to inactive chats if it's conditions matches
                    if (in_array((int)$statsChat['status'],array(erLhcoreClassModelChat::STATUS_PENDING_CHAT, erLhcoreClassModelChat::STATUS_ACTIVE_CHAT)) && in_array((int)$statsChat['status_sub'],array(erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT, erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW))) {
                        $depGroupObj->inachats_cnt += (int)$statsChat['total'];
                    }
                }

                $depGroupObj->max_load = $maxChats;
                $depGroupObj->max_load_h = isset($maxChatsHard['max_chats']) ? $maxChatsHard['max_chats'] : 0;
                $depGroupObj->inopchats_cnt = isset($maxChatsHard['inactive_chats']) ? $maxChatsHard['inactive_chats'] : 0;
                $depGroupObj->acopchats_cnt = isset($maxChatsHard['active_chats']) ? $maxChatsHard['active_chats'] : 0;
                $depGroupObj->updateThis();
            }
        }
    }

}

?>