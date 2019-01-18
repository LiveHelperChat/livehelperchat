<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/list','Chatbox list');?></h1>

<?php if ($pages->items_total > 0) { ?>
<table class="table" width="100%">
<thead>
    <tr>
        <th width="1%">ID</th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/list','Name');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/list','Identifier');?></th>
        <th></th>
        <th></th>
        <th></th>
    </tr>
</thead>
    <?php foreach (erLhcoreClassChatbox::getList(array('limit' => $pages->items_per_page, 'offset' => $pages->low)) as $chat) : ?>
    <tr>
        <td><?php echo $chat->id?></td>
        <td><?php echo htmlspecialchars($chat->name)?></td>
        <td><?php echo htmlspecialchars($chat->identifier)?></td>
        <td class="small-1" nowrap>
           <img class="action-image" align="absmiddle" onclick="lhinst.startChatNewWindow('<?php echo $chat->chat_id;?>','<?php echo htmlspecialchars($chat->nick);?>')" src="<?php echo erLhcoreClassDesign::design('images/icons/application_add.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Open in a new window');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Open in a new window');?>">
	       <?php echo $chat->id;?>. <?php echo htmlspecialchars($chat->nick);?> <?php echo date(erLhcoreClassModule::$dateDateHourFormat,$chat->chat->time);?>
        </td>
        <td class="small-1" nowrap><a class="btn btn-default btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('chatbox/edit')?>/<?php echo $chat->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Edit');?></a></td>
        <td class="small-1" nowrap><a class="csfr-required btn btn-danger btn-xs" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/messages','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('chatbox/delete')?>/<?php echo $chat->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Delete');?></a></td>
    </tr>
    <?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<?php } else { ?>
<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Empty...');?></p>
<?php } ?>

<a href="<?php echo erLhcoreClassDesign::baseurl('chatbox/new')?>" class="btn btn-default"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New');?></a>
