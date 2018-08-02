<tr>
    <td><strong><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Average')?></i></strong></td>
    <td>
        <?php echo htmlspecialchars($agentStatistic_avg['numberOfChats'])?>
    </td>
    <td>
        <?php echo htmlspecialchars($agentStatistic_avg['numberOfChatsOnline'])?>
    </td>
    <td>
        <?php echo htmlspecialchars($agentStatistic_avg['totalHours_front'])?>
    </td>
    <td>
        <?php echo htmlspecialchars($agentStatistic_avg['totalHoursOnline_front'])?>
    </td>
    <td>
        <?php echo htmlspecialchars($agentStatistic_avg['aveNumber'])?>
    </td>
    <td>
        <?php echo htmlspecialchars($agentStatistic_avg['avgWaitTime_front'])?>
    </td>
    <td>
        <?php echo htmlspecialchars($agentStatistic_avg['avgChatLengthSeconds_front'])?>
    </td>
</tr>