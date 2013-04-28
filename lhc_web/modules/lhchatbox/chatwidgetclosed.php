<?php

// For IE to support headers if chat is installed on different domain
header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');

// This is called then user closes chat widget
// We mark session variable as user closed the chat
CSCacheAPC::getMem()->setSession('lhc_chatbox_is_opened',false);
exit;

?>