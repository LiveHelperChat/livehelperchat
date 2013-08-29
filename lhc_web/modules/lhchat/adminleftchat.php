<?php

//  Just reser session variable if set
CSCacheAPC::getMem()->removeFromArray('lhc_open_chats', (int)$Params['user_parameters']['chat_id']);
exit;

?>