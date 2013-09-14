<?php

// For IE to support headers if chat is installed on different domain
header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');

// Because we do not use anymore third parties cookies, this part is irrelevant now.
// $daysLimit = erLhcoreClassModelChatConfig::fetch('voting_days_limit')->current_value;

// Voting widget have been seen
// setcookie('lhc_vws',1,time() + ($daysLimit * 24 * 60 * 60),'/');

// Perhaps in the feature i will add more actions there, like marking visited chats etc.

exit;
?>