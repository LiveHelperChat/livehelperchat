<h6><?php echo count($previousChats);?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','chats were found')?></h6>
<table class="table table-hover table-small">
    <thead>
    <tr>
        <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','ID')?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Chat duration')?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Started')?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Ended')?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Previous chat assigned')?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Current chat assigned')?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Finished assign')?></th>
        <th>
            <i class="material-icons chat-pending" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Pending Chats')?>">chat</i>
        </th>
        <th>
            <i class="material-icons chat-active" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Active Chats')?>">chat</i>
        </th>
        <th>
            <i class="material-icons text-danger" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Inactive Chats')?>">chat</i>
        </th>
    </tr>
    </thead>
    <?php $hideStatusText = true;?>
    <?php foreach ($previousChats as $chat) :
        $assignHistoryData = [];
        $assignHistory = erLhcoreClassModelmsg::findOne(['customfilter' => ['`meta_msg` != \'\' AND JSON_EXTRACT(meta_msg,\'$.content.assign_action.user_id\') = '.$chat->user_id], 'filter' => ['user_id' => -1, 'chat_id' => $chat->id]]);
        if (is_object($assignHistory)){
            $assignHistoryData = json_decode($assignHistory->meta_msg, true);
        }
        ?>
        <tr>
            <td nowrap="nowrap"><a class="material-icons"  data-title="pavlocasino77" onclick="lhinst.startChatNewWindow('1647602253',$(this).attr('data-title'))">open_in_new</a><?php include(erLhcoreClassDesign::designtpl('lhchat/lists_chats_parts/status_column.tpl.php'));?><?php echo $chat->id?></td>
            <td>
                <?php if ($chat->chat_duration > 0) : ?>
                    <?php echo erLhcoreClassChat::formatSeconds($chat->chat_duration)?>
                <?php endif; ?>
            </td>
            <td><?php echo date(erLhcoreClassModule::$dateDateHourFormat,get_class($chat) == 'erLhcoreClassModelChat' ? $chat->time :  $chat->time/1000);?></td>
            <td><?php echo date(erLhcoreClassModule::$dateDateHourFormat,get_class($chat) == 'erLhcoreClassModelChat' ? $chat->cls_time :  $chat->cls_time/1000);?></td>
            <td nowrap="">
                <?php if (isset($assignHistoryData['content']['assign_action']['last_accepted'])) : ?>
                    <?php echo $assignHistoryData['content']['assign_action']['last_accepted'] > 0 ? date(erLhcoreClassModule::$dateDateHourFormat,$assignHistoryData['content']['assign_action']['last_accepted']) : 'n/a';?>
                <?php endif; ?>
            </td>
            <td nowrap="">
                <?php if (isset($assignHistoryData['content']['assign_action']['sla'])) : ?>
                    <?php if ($assignHistoryData['content']['assign_action']['sla'] && $assignHistoryData['content']['assign_action']['sla'] == 1) : ?>
                        <span class="material-icons text-success" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Last assigned was updated successfully')?>">done</span>
                    <?php else : ?>
                        <span class="material-icons text-danger" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Last assigned update failed')?>">clear</span>
                    <?php endif; ?>
                <?php endif;?>
                <?php if (is_object($assignHistory)) : ?>
                    <?php echo date(erLhcoreClassModule::$dateDateHourFormat,$assignHistory->time);?>
                <?php endif; ?>
            </td>
            <td nowrap="">
                <?php if (isset($assignHistoryData['content']['assign_action']['sac'])) : ?>
                    <?php if ($assignHistoryData['content']['assign_action']['sac'] && $assignHistoryData['content']['assign_action']['sac'] == 1) : ?>
                        <span class="material-icons text-success" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Active chats were updated successfully')?>">done</span>
                    <?php else : ?>
                        <span class="material-icons text-danger" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/history','Active chats update failed')?>">clear</span>
                    <?php endif; ?>
                <?php endif;?>
                <?php if (isset($assignHistoryData['content']['assign_action']['assign_finished'])) : ?>
                    <?php echo date(erLhcoreClassModule::$dateDateHourFormat,$assignHistoryData['content']['assign_action']['assign_finished']);?>
                <?php endif; ?>
            </td>
            <td>
                <?php if (isset($assignHistoryData['content']['assign_action']['pending_chats'])) : ?>
                    <?php echo $assignHistoryData['content']['assign_action']['pending_chats'];?>
                <?php endif; ?>
            </td>
            <td>
                <?php if (isset($assignHistoryData['content']['assign_action']['active_chats'])) : ?>
                    <?php echo $assignHistoryData['content']['assign_action']['active_chats'];?>
                <?php endif; ?>
            </td>
            <td>
                <?php if (isset($assignHistoryData['content']['assign_action']['inactive_chats'])) : ?>
                    <?php echo $assignHistoryData['content']['assign_action']['inactive_chats'];?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>