<?php foreach ($operators as $operator) : ?>
<tr>
	<td><?php echo htmlspecialchars((string)$operator)?></td>
	<td><?php echo $operator->statistic_total_chats?></td>
	<td><?php echo $operator->statistic_total_messages?></td>
	<td>
	  <span class="up-voted"><i class="material-icons up-voted">thumb_up</i><?php echo $operator->statistic_upvotes?></span>
	  <span class="down-voted"><i class="material-icons down-voted">thumb_down</i><?php echo $operator->statistic_downvotes?></span>
	</td>
	<td><?php echo $operator->lastactivity_ago?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','ago');?></td>
</tr>
<?php endforeach;?>