<td><?php echo $info->agentName; ?></td>
<td><?php echo $info->numberOfChats; ?></td>
<td><?php echo $info->numberOfChatsOnline; ?></td>
<td><?php echo $info->totalHours_front; ?></td>
<td><a href="<?php echo erLhcoreClassDesign::baseurl('statistic/onlinehours')?><?php echo erLhcoreClassSearchHandler::getURLAppendFromInput($input);?>/(user_id)/<?php echo $info->userId?>"><?php echo $info->totalHoursOnline_front; ?></a></td>
<td><?php echo $info->aveNumber; ?></td>
<td><?php echo $info->avgWaitTime_front; ?></td>
<td><?php echo $info->avgChatLength; ?></td>