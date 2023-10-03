<tr>
    <td><strong><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Average')?></i></strong></td>
    <td <?php if (is_array($input->subject_ids) && !empty($input->subject_ids)) : ?>colspan="<?php echo count($input->subject_ids) + 1;?>"<?php endif;?> >
        <?php echo htmlspecialchars($agentStatistic_avg['numberOfChats'])?>, <span class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','As participant')?>"><?php echo htmlspecialchars($agentStatistic_avg['numberOfChatsParticipant'])?></span>
    </td>
    <td>
        <?php echo htmlspecialchars($agentStatistic_avg['numberOfChatsOnline'])?>
    </td>
    <td>
        <?php echo htmlspecialchars($agentStatistic_avg['totalHours_front'])?>, <span class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','As participant')?>"><?php echo htmlspecialchars($agentStatistic_avg['totalHoursParticipant_front'])?></span>
    </td>
    <td>
        <?php echo htmlspecialchars($agentStatistic_avg['totalHoursOnline_front'])?>
    </td>
    <td>
        <?php echo htmlspecialchars($agentStatistic_avg['aveNumber'])?>, <span class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','As participant')?>"><?php echo htmlspecialchars($agentStatistic_avg['aveNumberParticipant'])?></span>
    </td>
    <td>
        <?php echo htmlspecialchars($agentStatistic_avg['avgWaitTime_front'])?>
    </td>
    <td>
        <?php echo htmlspecialchars($agentStatistic_avg['avgChatLengthSeconds_front'])?>
    </td>
    <td nowrap="">
        <?php echo htmlspecialchars($agentStatistic_avg['avgFirstResponseTime_front'])?>, <span class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','As participant')?>"><?php echo htmlspecialchars($agentStatistic_avg['avgFirstResponseTimePar_front'])?></span>
    </td>
    <td nowrap="">
        <?php echo htmlspecialchars($agentStatistic_avg['avgResponseTime_front']);?>, <span class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','As participant')?>"><?php echo htmlspecialchars($agentStatistic_avg['avgResponseTimePar_front']);?></span>
    </td>
    <td nowrap="">
        <?php echo htmlspecialchars($agentStatistic_avg['avgMaximumResponseTime_front']);?>, <span class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','As participant')?>"><?php echo htmlspecialchars($agentStatistic_avg['avgMaximumResponseTimePar_front']);?></span>
    </td>
</tr>