<td><?php echo $info->agentName; ?></td>
<td><a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?><?php echo isset($input) ? erLhcoreClassSearchHandler::getURLAppendFromInput($input,false,array('user_ids')) : ''?>/(user_ids)/<?php echo $info->userId?>"><?php echo $info->numberOfChats; ?></a></td>
<td><?php echo $info->numberOfChatsParticipant; ?></td>
<?php if (is_array($input->subject_ids) && !empty($input->subject_ids)) : ?>
    <?php foreach ($input->subject_ids as $subjectId) : ?>
        <td nowrap="">
            <?php foreach ($info->subject_stats as $subjectStat) : if ($subjectStat['subject_id'] == $subjectId) : ?>
                <?php echo htmlspecialchars($subjectStat['number_of_chats']),', ',$subjectStat['perc']?>%
            <?php endif;endforeach; ?>
        </td>
    <?php endforeach; ?>
<?php endif; ?>
<td><a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?><?php echo isset($input) ? erLhcoreClassSearchHandler::getURLAppendFromInput($input,false,array('user_ids')) : ''?>/(user_ids)/<?php echo $info->userId?>"><?php echo $info->numberOfChatsOnline; ?></a></td>
<td><a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?><?php echo isset($input) ? erLhcoreClassSearchHandler::getURLAppendFromInput($input,false,array('user_ids')) : ''?>/(user_ids)/<?php echo $info->userId?>/(chat_status_ids)/2/(chat_duration_from)/1/(chat_duration_till)/3600"><?php echo $info->totalHours_front; ?></a></td>
<td><?php echo $info->totalHoursParticipant_front; ?></td>
<td><a href="<?php echo erLhcoreClassDesign::baseurl('statistic/onlinehours')?><?php echo erLhcoreClassSearchHandler::getURLAppendFromInput($input);?>/(user_id)/<?php echo $info->userId?>"><?php echo $info->totalHoursOnline_front; ?></a></td>
<td><?php echo $info->aveNumber; ?></td>
<td><?php echo $info->aveNumberParticipant; ?></td>
<td><?php echo $info->avgWaitTime_front; ?></td>
<td><?php echo $info->avgChatLength; ?></td>