<?php foreach (erLhcoreClassModelChatOnlineUserFootprint::getList(array('filter' => array('chat_id' => $chat->id))) as $footprintItems) : ?>
<li>
<a target="_blank" href="<?php echo htmlspecialchars($footprintItems->page);?>"><?php echo $footprintItems->time_ago?> | <?php echo htmlspecialchars($footprintItems->page);?></a>
</li>
<?php endforeach;?>