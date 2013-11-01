<?php
$filter = $chat->online_user_id == 0 ? array('chat_id' => $chat->id) : array('online_user_id' => $chat->online_user_id);
foreach (erLhcoreClassModelChatOnlineUserFootprint::getList(array('filter' => $filter)) as $footprintItems) : ?>
<li>
<a target="_blank" href="<?php echo htmlspecialchars($footprintItems->page);?>"><?php echo $footprintItems->time_ago?> | <?php echo htmlspecialchars($footprintItems->page);?></a>
</li>
<?php endforeach;?>