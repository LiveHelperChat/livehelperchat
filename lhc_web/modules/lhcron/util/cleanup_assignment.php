<?php

/**
 * php cron.php -s site_admin -c cron/util/cleanup_assignment
 **/

$offset = 0;
do {
    $users = erLhcoreClassModelUser::getList(['limit' => 100, 'offset' => $offset, 'filter' => ['disabled' => 0]]);
    foreach ($users as $user) {
        foreach (erLhcoreClassModelUserDep::getList(['limit' => 1000, 'filter' => ['type' => 1, 'user_id' => $user->id]]) as $dep) {
            $userDepGroup = erLhcoreClassModelDepartamentGroupUser::findOne(['filter' => ['user_id' => $user->id, 'dep_group_id' => $dep->dep_group_id]]);
            if (!$userDepGroup) {
                echo "Department group not assigned - User ID [{$user->id}] Department group [$dep->dep_group_id]\n";
                $dep->removeThis();
            }
        }
    }
    $offset += 100;
} while (count($users) === 100);

?>