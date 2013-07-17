<ul class="foot-print-content circle">
<?php foreach (erLhcoreClassModelChatOnlineUserFootprint::getList(array('filter' => array('chat_id' => $chat->id))) as $footprintItems) : ?>
<li>
<a target="_blank" href="<?php echo htmlspecialchars($footprintItems->page);?>"><?php echo $footprintItems->vtime_front?> | <?php echo htmlspecialchars($footprintItems->page);?></a>
</li>
<?php endforeach;?>
</ul>