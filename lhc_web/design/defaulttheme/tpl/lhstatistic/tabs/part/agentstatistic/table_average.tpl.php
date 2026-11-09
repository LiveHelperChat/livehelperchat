<?php
$agentAverageIcon = '<span class="material-icons text-muted fs-6 align-middle" role="img" title="' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Average across agents with non-zero values') . '" aria-label="' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Average across agents with non-zero values') . '">person</span>';
?>
<tr class="text-nowrap">
    <td><strong><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Average')?></i></strong> <?php echo $agentAverageIcon?></td>
    <td <?php if (is_array($input->subject_ids) && !empty($input->subject_ids)) : ?>colspan="<?php echo count($input->subject_ids) + 1;?>"<?php endif;?> >
        <?php echo htmlspecialchars($agentStatistic_avg['numberOfChats'])?>, <span class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','As participant')?>"><?php echo htmlspecialchars($agentStatistic_avg['numberOfChatsParticipant'])?></span> <?php echo $agentAverageIcon?>
    </td>
    <td>
        <?php echo htmlspecialchars($agentStatistic_avg['numberOfChatsOnline'])?> <?php echo $agentAverageIcon?>
    </td>
    <td>
        <?php echo htmlspecialchars($agentStatistic_avg['totalHours_front'])?>, <span class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','As participant')?>"><?php echo htmlspecialchars($agentStatistic_avg['totalHoursParticipant_front'])?></span> <?php echo $agentAverageIcon?>
    </td>
    <td>
        <?php echo htmlspecialchars($agentStatistic_avg['totalHoursOnline_front'])?> <?php echo $agentAverageIcon?>
    </td>
    <td>
        <?php echo htmlspecialchars($agentStatistic_avg['totalHoursOffline_front'])?> <?php echo $agentAverageIcon?>
    </td>
    <?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/part/agentstatistic/table_average_after_offline_multiinclude.tpl.php'));?>
    <td>
        <?php echo htmlspecialchars($agentStatistic_avg['aveNumber'])?>, <span class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','As participant')?>"><?php echo htmlspecialchars($agentStatistic_avg['aveNumberParticipant'])?></span> <?php echo $agentAverageIcon?>
    </td>
    <td>
        <?php echo htmlspecialchars($agentStatistic_avg['avgWaitTime_front'])?> <?php echo $agentAverageIcon?>
    </td>
    <td>
        <?php echo htmlspecialchars($agentStatistic_avg['avgChatLengthSeconds_front'])?> <?php echo $agentAverageIcon?>
    </td>
    <td>
    <?php echo htmlspecialchars($agentStatistic_avg['avgChatLengthParticipant_front'])?> <?php echo $agentAverageIcon?>
    </td>
    <td nowrap="">
        <?php echo htmlspecialchars($agentStatistic_avg['avgFirstResponseTime_front'])?>, <span class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','As participant')?>"><?php echo htmlspecialchars($agentStatistic_avg['avgFirstResponseTimePar_front'])?></span> <?php echo $agentAverageIcon?>
    </td>
    <td nowrap="">
        <?php echo htmlspecialchars($agentStatistic_avg['avgResponseTime_front']);?>, <span class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','As participant')?>"><?php echo htmlspecialchars($agentStatistic_avg['avgResponseTimePar_front']);?></span> <?php echo $agentAverageIcon?>
    </td>
    <td nowrap="">
        <?php echo htmlspecialchars($agentStatistic_avg['avgMaximumResponseTime_front']);?>, <span class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','As participant')?>"><?php echo htmlspecialchars($agentStatistic_avg['avgMaximumResponseTimePar_front']);?></span> <?php echo $agentAverageIcon?>
    </td>
    <td>
        <?php echo htmlspecialchars($agentStatistic_avg['mail_statistic_total'])?> <?php echo $agentAverageIcon?>
    </td>
    <td>
        <?php echo htmlspecialchars($agentStatistic_avg['mail_statistic_'.erLhcoreClassModelMailconvMessage::RESPONSE_UNRESPONDED])?> <?php echo $agentAverageIcon?>
    </td>
    <td>
        <?php echo htmlspecialchars($agentStatistic_avg['mail_statistic_'.erLhcoreClassModelMailconvMessage::RESPONSE_NOT_REQUIRED])?> <?php echo $agentAverageIcon?>
    </td>
    <td>
        <?php echo htmlspecialchars($agentStatistic_avg['mail_statistic_'.erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL])?> <?php echo $agentAverageIcon?>
    </td>
    <td>
        <?php echo htmlspecialchars($agentStatistic_avg['mail_statistic_'.erLhcoreClassModelMailconvMessage::RESPONSE_NORMAL])?> <?php echo $agentAverageIcon?>
    </td>
</tr>