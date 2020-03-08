<td><?php echo $info->agentName; ?></td>
<td><a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?><?php echo isset($input) ? erLhcoreClassSearchHandler::getURLAppendFromInput($input,false,array('user_ids')) : ''?>/(user_ids)/<?php echo $info->userId?>"><?php echo $info->numberOfChats; ?></a></td>
<td><a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?><?php echo isset($input) ? erLhcoreClassSearchHandler::getURLAppendFromInput($input,false,array('user_ids')) : ''?>/(user_ids)/<?php echo $info->userId?>"><?php echo $info->numberOfChatsOnline; ?></a></td>
<td><a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?><?php echo isset($input) ? erLhcoreClassSearchHandler::getURLAppendFromInput($input,false,array('user_ids')) : ''?>/(user_ids)/<?php echo $info->userId?>/(chat_status)/2/(chat_duration_from)/1/(chat_duration_till)/3600"><?php echo $info->totalHours_front; ?></a></td>
<td><a href="<?php echo erLhcoreClassDesign::baseurl('statistic/onlinehours')?><?php echo erLhcoreClassSearchHandler::getURLAppendFromInput($input);?>/(user_id)/<?php echo $info->userId?>"><?php echo $info->totalHoursOnline_front; ?></a></td>
<td><?php echo $info->aveNumber; ?></td>
<td><?php echo $info->avgWaitTime_front; ?></td>
<td><?php echo $info->avgChatLength; ?></td>