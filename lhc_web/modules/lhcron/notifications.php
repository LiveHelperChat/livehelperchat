<?php
/**
 * php cron.php -s site_admin -c cron/notifications
 *
 * Run every minute. This cronjob send's push notification to visitors.
 *
 * */
echo "Starting chat/notifications\n";

include 'lib/vendor/autoload.php';

erLhcoreClassNotifications::informAboutUnreadMessages();

/*
1. Send notification to those chat's where visitor has unread message from operator.
2. Last sync stopped 4 - 5 minutes. (It sends last unread message. Like we don't know what message was seen by visitor exactly and which not)
3. We should log within chat. Last notified message id. Also mark chat as notification was send
4. If operator sends another message. We reset flag about notification. So cronjobs sends notification only for new messages. Since we have logged it.
5. Notification should be send for all chats. Not only active one where visitor did not read. But also for closed one.
*/