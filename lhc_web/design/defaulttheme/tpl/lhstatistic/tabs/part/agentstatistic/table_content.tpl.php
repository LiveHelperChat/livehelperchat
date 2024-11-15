<td><?php echo $info->agentName; ?></td>
<td nowrap="">
    <a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?><?php echo isset($input) ? erLhcoreClassSearchHandler::getURLAppendFromInput($input,false,array('user_ids')) : ''?>/(user_ids)/<?php echo $info->userId?>"><?php echo $info->numberOfChats; ?></a>, <span class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','As participant')?>"><?php echo $info->numberOfChatsParticipant; ?></span>
</td>
<?php if (is_array($input->subject_ids) && !empty($input->subject_ids)) : ?>
    <?php foreach ($input->subject_ids as $subjectId) : ?>
        <td nowrap="">
            <?php foreach ($info->subject_stats as $subjectStat) : if ($subjectStat['subject_id'] == $subjectId) : ?>
                <?php echo htmlspecialchars($subjectStat['number_of_chats']),', ',$subjectStat['perc']?>%
            <?php endif;endforeach; ?>
        </td>
    <?php endforeach; ?>
<?php endif; ?>
<td nowrap="nowrap"><a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?><?php echo isset($input) ? erLhcoreClassSearchHandler::getURLAppendFromInput($input,false,array('user_ids')) : ''?>/(user_ids)/<?php echo $info->userId?>"><?php echo $info->numberOfChatsOnline; ?></a></td>
<td nowrap="nowrap"><a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?><?php echo isset($input) ? erLhcoreClassSearchHandler::getURLAppendFromInput($input,false,array('user_ids')) : ''?>/(user_ids)/<?php echo $info->userId?>/(chat_status_ids)/2/(chat_duration_from)/1/(chat_duration_till)/3600"><?php echo $info->totalHours_front; ?></a><?php if ($info->totalHours_front != '') : ?>, <?php endif; ?><span class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','As participant')?>"><?php echo $info->totalHoursParticipant_front; ?></span></td>
<td nowrap="nowrap"><a href="<?php echo erLhcoreClassDesign::baseurl('statistic/onlinehours')?><?php echo erLhcoreClassSearchHandler::getURLAppendFromInput($input);?>/(user_id)/<?php echo $info->userId?>"><?php echo $info->totalHoursOnline_front; ?></a></td>
<td nowrap="nowrap"><?php echo $info->aveNumber; ?>, <span class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','As participant')?>"><?php echo $info->aveNumberParticipant; ?></span></td>
<td nowrap="nowrap"><?php echo $info->avgWaitTime_front; ?></td>
<td nowrap="nowrap"><?php echo $info->avgChatLength; ?></td>
<td nowrap="nowrap">
    <?php echo isset($info->avgFirstResponseTime_front) ? $info->avgFirstResponseTime_front : ''; ?>, <span class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','As participant')?>"><?php echo isset($info->avgFirstResponseTimePar_front) ? $info->avgFirstResponseTimePar_front : ''; ?></span>
</td>
<td nowrap="nowrap">
    <?php echo isset($info->avgResponseTime_front) ? $info->avgResponseTime_front : ''; ?>, <span class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','As participant')?>"><?php echo isset($info->avgResponseTimePar_front) ? $info->avgResponseTimePar_front : ''; ?></span>
</td>
<td nowrap="nowrap">
    <?php echo isset($info->avgMaximumResponseTime_front) ? $info->avgMaximumResponseTime_front : ''; ?>, <span class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','As participant')?>"><?php echo isset($info->avgMaximumResponseTimePar_front) ? $info->avgMaximumResponseTimePar_front : ''; ?></span>
</td>