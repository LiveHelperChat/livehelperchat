<?php if ($pages->items_total > 0) : ?>
<table class="table" cellpadding="0" cellspacing="0">
<thead>
<tr>
	<th width="1%"></th>
    <th width="1%">
    <?php if ($input->group_results == true) : ?>
        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Chats');?>
    <?php else : ?>
        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Chat');?>
    <?php endif;?>
    </th>
    <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Department');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Operator');?></th>
    <th><?php echo implode(', ', $starFields)?></th>
    <?php if ($input->group_results !== true) : ?>
    <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Time');?></th>
    <?php endif;?>
</tr>
</thead>
<?php foreach ($items as $item) : ?>
    <tr>
    	<td nowrap="nowrap"><a class="material-icons" onclick="lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('survey/collecteditem')?>/<?php echo $item->id?>'})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','View information');?>">info_outline</a></td>
    	<td nowrap="nowrap">
    	<?php if ($input->group_results == true) : ?>
    	   <?php echo $item->virtual_chats_number?>
    	<?php else : ?>
    	   <a class="material-icons" data-title="<?php echo htmlspecialchars($item->chat->nick,ENT_QUOTES);?>" onclick="lhinst.startChatNewWindow('<?php echo $item->chat_id;?>',$(this).attr('data-title'))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Open in a new window');?>">open_in_new</a><a href="#" onclick="return lhc.previewChat(<?php echo $item->chat_id?>)"><?php echo htmlspecialchars($item->chat_id)?></a>
    	<?php endif;?>
    	</td>
    	<td><?php echo htmlspecialchars($item->department_name)?></td>
    	<td><?php echo htmlspecialchars($item->user)?></td>
    	<td>
    	<?php if ($input->group_results == true) : ?>
    	   <?php echo htmlspecialchars(round($item->virtual_total_stars/$item->virtual_chats_number,2))?>
    	<?php else : ?>
    	   <?php $stars = array(); foreach ($enabledStars as $n) {$stars[] = $item->{'max_stars_' . $n};};echo implode(', ', $stars);?>
    	<?php endif;?>
    	</td>
    	<?php if ($input->group_results !== true) : ?>
    	<td nowrap="nowrap"><?php echo htmlspecialchars($item->ftime_front)?></td>
    	<?php endif;?>
    </tr>
<?php endforeach; ?>
</table>

<?php if (isset($_GET['show']) && is_numeric($_GET['show'])) : ?>
<script>lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('survey/collecteditem')?>/<?php echo (int)$_GET['show']?>'});</script>
<?php endif; ?>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<?php else : ?>
    <br>
    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Empty...');?></p>
<?php endif; ?>