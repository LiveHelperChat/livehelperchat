<?php
$onlineCheck = (int)erLhcoreClassModelChatConfig::fetch('checkstatus_timeout')->current_value;
if ($onlineCheck > 0) {
    $onlineCheck = "recent_visit:(ou.last_visit_seconds_ago < 15),online_user:(ou.last_check_time_ago < " . ($onlineCheck+3) . ")";
} else {
    $onlineCheck = 'recent_visit:(ou.last_visit_seconds_ago < 15)';
}
?>