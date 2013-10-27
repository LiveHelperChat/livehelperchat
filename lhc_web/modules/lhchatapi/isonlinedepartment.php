<?php

echo json_encode(array('isonline' =>  erLhcoreClassChat::isOnline((int)$Params['user_parameters']['department_id'],true)));
exit;
?>