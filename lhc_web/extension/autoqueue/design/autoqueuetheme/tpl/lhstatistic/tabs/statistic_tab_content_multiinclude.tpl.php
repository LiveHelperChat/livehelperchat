<?php

/**
 * Override and append custom tabs content
 */

if ($tab == 'fila') {
    try {

        $isOnlineUser = (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'];

        $db = ezcDbInstance::get();

        $sql = "SELECT u.name, u.surname, last_accepted FROM lh_userdep d INNER JOIN lh_users u ON d.user_id = u.id WHERE ro = 0 AND d.hide_online = 0 AND last_activity > :last_activity GROUP BY user_id ORDER BY pending_chats + active_chats ASC, last_accepted ASC ;";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':last_activity', time() - $isOnlineUser, PDO::PARAM_INT);

        $stmt->execute();

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "Fila 'Tempo Real' de Atendimento (leva em consideração se o atendente tem chats ativos ou pendentes)" . '<br>';
        foreach ($users as $user) {
            echo $user['name'] . ' ' . $user['surname'] . '<br>';
        }

        uasort($users, function($a, $b) {
            return $a['last_accepted'] - $b['last_accepted'];
        });

        echo '<br>' . "Fila 'Estática' de Atendimento (leva em consideração apenas o tempo desde o último chat)" . '<br>';
        foreach ($users as $user) {
            echo $user['name'] . ' ' . $user['surname'] . '<br>';
        }

    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
}

?>