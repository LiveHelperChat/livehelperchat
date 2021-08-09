<?php

/**
 * If for some reason you have changed assigned departments matching rules
 * You can run this script and mails will be set correct departments
 *
 * php cron.php -s site_admin -c cron/util/update_mails_departments
 *
 */
echo "Starting departments updates\n";

$db = ezcDbInstance::get();
$sql = 'SELECT

`lhc_mailconv_mailbox`.`id`,
`lhc_mailconv_mailbox`.`mail`,
`lhc_mailconv_match_rule`.`dep_id`,
       1 as counter

FROM lhc_mailconv_match_rule, 
lhc_mailconv_mailbox

WHERE JSON_CONTAINS(`lhc_mailconv_match_rule`.`mailbox_id`,`lhc_mailconv_mailbox`.`id` )';

$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$oneRuleMailbox = array();
foreach ($rows as $index => $row) {
    if (!isset($oneRuleMailbox[$row['id']])){
        $oneRuleMailbox[$row['id']] = $row;
    } else {
        $oneRuleMailbox[$row['id']]['counter'] = $oneRuleMailbox[$row['id']]['counter'] + 1;
    }
}

foreach ($oneRuleMailbox as $mailbox) {
    if ($mailbox['counter'] == 1) {
        echo "Updating - ",$mailbox['id'],'-',$mailbox['mail'],' to department ',$mailbox['dep_id'],"\n";
        $sql = "UPDATE lhc_mailconv_conversation SET dep_id = ".(int)$mailbox['dep_id'].' WHERE mailbox_id = '.$mailbox['id'];
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $sql = "UPDATE lhc_mailconv_msg SET dep_id = ".(int)$mailbox['dep_id'].' WHERE mailbox_id = '.$mailbox['id'];
        $stmt = $db->prepare($sql);
        $stmt->execute();
    } else {
        echo "To many matching rules for the mailbox - ",$mailbox['id'],'-',$mailbox['mail'],"\n";
    }
}



