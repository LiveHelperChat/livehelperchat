<?php

echo json_encode(array('isonline' => erLhcoreClassChat::isOnlineUser((int)$Params['user_parameters']['user_id'])));
exit;
?>