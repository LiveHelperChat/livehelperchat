<h1><?php echo htmlspecialchars($survey)?></h1>

<?php include(erLhcoreClassDesign::designtpl('lhsurvey/collected/search_panel.tpl.php')); ?>

<?php if ($pages->items_total > 0) : ?>
<table class="table" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Chat');?></th>
    <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Department');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Operator');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Stars');?></th>
    <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Time');?></th>
</tr>
</thead>
<?php foreach ($items as $item) : ?>
    <tr>
    	<td nowrap="nowrap"><a class="material-icons" data-title="<?php echo htmlspecialchars($item->chat->nick,ENT_QUOTES);?>" onclick="lhinst.startChatNewWindow('<?php echo $item->chat_id;?>',$(this).attr('data-title'))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Open in a new window');?>">open_in_new</a><a href="#" onclick="return lhc.previewChat(<?php echo $item->chat_id?>)"><?php echo htmlspecialchars($item->chat_id)?></a></td>
    	<td><?php echo htmlspecialchars($item->department_name)?></td>
    	<td><?php echo htmlspecialchars($item->user)?></td>
    	<td><?php echo htmlspecialchars($item->stars)?></td>
    	<td nowrap="nowrap"><?php echo htmlspecialchars($item->ftime_front)?></td>
    </tr>
<?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<?php else : ?>
    <br>
    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Empty...');?></p>
<?php endif; ?>