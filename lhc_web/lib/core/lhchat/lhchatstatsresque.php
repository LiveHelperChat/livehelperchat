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
        $stmt = $db->prepare('SELECT SUM(max_chats) as max_chats FROM (SELECT MAX(max_chats) as max_chats FROM `lh_userdep` WHERE dep_id = :dep_id AND last_activity > :last_activity GROUP BY user_id) as tmp;');
        $stmt->bindValue(':dep_id',$dep->id,PDO::PARAM_INT);
        $stmt->bindValue(':last_activity',time()-300, PDO::PARAM_INT);
        $stmt->execute();
        $maxChats = (int)$stmt->fetchColumn();

        $statsChats = erLhcoreClassModelChat::getCount(array(
            'group' => 'status',
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
            'count', false, 'count(`id`) as `total`, `status`', false, true
        );

        foreach ($statsChats as $statsChat) {
            if ($statsChat['status'] == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) {
                $dep->active_chats_counter = $statsChat['total'];
            } elseif ($statsChat['status'] == erLhcoreClassModelChat::STATUS_PENDING_CHAT) {
                $dep->pending_chats_counter = $statsChat['total'];
            } elseif ($statsChat['status'] == erLhcoreClassModelChat::STATUS_BOT_CHAT) {
                $dep->bot_chats_counter = $statsChat['total'];
            }
        }

        $dep->max_load = $maxChats;
        $dep->updateThis(array('update' => array('active_chats_counter','pending_chats_counter','bot_chats_counter','max_load')));

        // Update departments groups statistic
        $depGroups = erLhcoreClassModelDepartamentGroupMember::getList(array('filter' => array('dep_id' => $dep->id)));

        foreach ($depGroups as $depGroup) {
            $stmt = $db->prepare('SELECT SUM(`max_chats`) AS `max_chats` FROM (SELECT MAX(`max_chats`) AS `max_chats` FROM `lh_userdep` WHERE `dep_id` IN (SELECT `dep_id` FROM `lh_departament_group_member` WHERE `dep_group_id` = :dep_group_id) AND `last_activity` > :last_activity GROUP BY `user_id`) as `tmp`;');
            $stmt->bindValue(':dep_group_id',$depGroup->dep_group_id,PDO::PARAM_INT);
            $stmt->bindValue(':last_activity',time()-300, PDO::PARAM_INT);
            $stmt->execute();
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!empty($stats)) {
                $depGroupObj = erLhcoreClassModelDepartamentGroup::fetch($depGroup->dep_group_id);
                if ($depGroupObj instanceof erLhcoreClassModelDepartamentGroup) {

                    $statsChats = erLhcoreClassModelChat::getCount(array(
                        'group' => 'status',
                        'filterin' => array(
                            'status' => array(
                                erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,
                                erLhcoreClassModelChat::STATUS_PENDING_CHAT,
                                erLhcoreClassModelChat::STATUS_BOT_CHAT,
                            ),
                            'dep_id' => erLhcoreClassChat::getDepartmentsByDepGroup(array($depGroupObj->id))
                        )
                    ),
                        'count', false, 'count(`id`) as `total`, `status`', false, true
                    );

                    foreach ($statsChats as $statsChat) {
                        if ($statsChat['status'] == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) {
                            $depGroupObj->achats_cnt = $statsChat['total'];
                        } elseif ($statsChat['status'] == erLhcoreClassModelChat::STATUS_PENDING_CHAT) {
                            $depGroupObj->pchats_cnt = $statsChat['total'];
                        } elseif ($statsChat['status'] == erLhcoreClassModelChat::STATUS_BOT_CHAT) {
                            $depGroupObj->bchats_cnt = $statsChat['total'];
                        }
                    }

                    $depGroupObj->max_load = $stats['max_chats'];
                    $depGroupObj->updateThis();
                }
            }
        }
    }

}

?>