<?php

erLhcoreClassGroup::deleteGroup((int)$Params['user_parameters']['group_id']);

erLhcoreClassModule::redirect('user/grouplist');
exit;

?>