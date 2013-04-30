<?php

$chatbox = erLhcoreClassModelChatbox::fetch($Params['user_parameters']['id']);
$chatbox->removeThis();
erLhcoreClassModule::redirect('chatbox/list');
exit;

?>