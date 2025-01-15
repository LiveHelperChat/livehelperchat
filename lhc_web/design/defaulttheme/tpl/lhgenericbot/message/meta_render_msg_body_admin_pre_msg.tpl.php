<?php if (is_array($metaMessageData)) : ?>
    <?php if (isset($metaMessageData['content']) && is_array($metaMessageData['content'])) : foreach ($metaMessageData['content'] as $type => $metaMessage) : ?>
        <?php if ($type == 'accept_action') : // Chat was accepted ?>

            <?php if (isset($metaMessage['puser_id']) && $metaMessage['puser_id'] > 0) : ?>
                <span class="material-icons text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Chat was assigned to chat opener event it had other agent assigned at that moment')?> - [<?php echo htmlspecialchars($metaMessage['puser_id'])?>]" >account_circle_off</span>
            <?php endif; ?>

            <span class="material-icons text-success" <?php if (isset($metaMessage['ol']) && is_array($metaMessage['ol']) && !empty($metaMessage['ol'])) : ?>title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Opened chat by')?> - <?php echo htmlspecialchars(implode(', ',$metaMessage['ol']))?>"<?php endif;?> >login</span>
        <?php elseif ($type == 'assign_action') : // Chat was assigned to user?>
            <?php $partsInfo =
                [
                    erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history', 'Previous chat assigned') . ' - ' . ($metaMessage['last_accepted'] > 0 ? date('Y-m-d H:i:s', (int)$metaMessage['last_accepted']) : 'n/a'),
                    erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history', 'Current chat assigned') . ' - ' . date('Y-m-d H:i:s', (int)$msg['time']),
                    erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history', 'Finished assign') . ' - ' . date('Y-m-d H:i:s', (int)$metaMessage['assign_finished']),
                    erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history', 'Pending chats') . ' - ' . $metaMessage['pending_chats'],
                    erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history', 'Active chats') . ' - ' . $metaMessage['active_chats'],
                    erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history', 'Inactive chats') . ' - ' . $metaMessage['inactive_chats'],
                    erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history', 'Active chats update') . ' - ' . ($metaMessage['sac'] ? 'Y' : 'N'),
                    erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history', 'Last assigned update') . ' - ' . ($metaMessage['sla'] ? 'Y' : 'N'),
                ];
            ?>
            <span class="material-icons <?php echo ($metaMessage['sac'] != 1 || $metaMessage['sla'] != 1) ? 'text-danger' : 'text-info'?>" title="<?php echo implode("\n", $partsInfo)?>">switch_account</span>
        <?php elseif ($type == 'transfer_action_user') : // Chat was transferred to other user?>
            <span class="material-icons text-warning">logout</span>
        <?php elseif ($type == 'close_reason') :
            $partsInfo = [];
            if (isset($metaMessage['reason'])) {
                $partsInfo[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Reason') . ' - ' . $metaMessage['reason'];
            }
            if (isset($metaMessage['last_user_msg_time'])) {
                $partsInfo[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Last user message') . ' - ' . ($metaMessage['last_user_msg_time'] > 0 ? date('Y-m-d H:i:s', (int)$metaMessage['last_user_msg_time']) : 'n/a');
            }
            if (isset($metaMessage['last_op_msg_time'])) {
                $partsInfo[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Last operator message') . ' - ' . ($metaMessage['last_op_msg_time'] > 0 ? date('Y-m-d H:i:s', (int)$metaMessage['last_op_msg_time']) : 'n/a');
            }
            if (isset($metaMessage['lsync'])) {
                $partsInfo[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Last time visitor seen') . ' - ' . ($metaMessage['lsync'] > 0 ? date('Y-m-d H:i:s', (int)$metaMessage['lsync']) : 'n/a');
            }
            if (isset($metaMessage['delay'])) {
                $partsInfo[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Delay') . ' - ' . (is_numeric($metaMessage['delay']) ? (($msg['time'] - $metaMessage['delay']) .' s. &lt; ' .  date('Y-m-d H:i:s', (int)$metaMessage['delay'])) : $metaMessage['delay']);
            }
            ?>
            <span class="material-icons text-muted" title="<?php echo implode("\n", $partsInfo)?>">info</span>
        <?php elseif ($type == 'transfer_action_dep') : // Chat was transfered to departmnet?>
            <span class="material-icons text-info">home</span>
        <?php elseif ($type == 'change_owner_action') : // Chat owner was changed?>
            <span class="material-icons text-info">swap_horiz</span>
        <?php elseif ($type == 'change_dep_action') : // Chat department was changed?>
            <span class="material-icons text-info">location_away</span>
        <?php elseif ($type == 'reply_to') : // Chat department was changed?>
            <blockquote class="blockquote" title="<?php echo htmlspecialchars($metaMessage['iwh_msg_id']); ?>">
                <?php if (isset($metaMessage['db_msg_id'])) { $messageReplyTo = erLhcoreClassModelmsg::fetch($metaMessage['db_msg_id']); } ?>
                <?php if (isset($messageReplyTo) && is_object($messageReplyTo)) : ?>
                    <?php echo htmlspecialchars($messageReplyTo->msg); ?>
                <?php else: ?>
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Reply To')?>: <?php echo htmlspecialchars($metaMessage['iwh_msg_id']); ?>
                <?php endif; ?>
            </blockquote>
        <?php endif; ?>
    <?php endforeach; endif; ?>
<?php endif; ?>