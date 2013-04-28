<?php include(erLhcoreClassDesign::designtpl('lhchatbox/embed_button.tpl.php'));?>
<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Chatbox list');?></h1>

<?php if ($pages->items_total > 0) { ?>
<table cellpadding="0" cellspacing="0" width="100%">
<thead>
    <tr>
        <th width="1%">ID</th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Name');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Identifier');?></th>
        <th></th>
        <th></th>
    </tr>
</thead>
    <?php foreach (erLhcoreClassChatbox::getList(array('limit' => $pages->items_per_page,'offset' => $pages->low)) as $chat) : ?>
    <tr>
        <td><?php echo $chat->id?></td>
        <td><?php echo htmlspecialchars($chat->name)?></td>
        <td><?php echo htmlspecialchars($chat->identifier)?></td>
        <td class="small-1" nowrap>
           <img class="action-image" align="absmiddle" onclick="lhinst.startChatNewWindow('<?php echo $chat->chat_id;?>','<?php echo htmlspecialchars($chat->nick);?>')" src="<?php echo erLhcoreClassDesign::design('images/icons/application_add.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Open in new window');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Open in new window');?>">
	       <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Delete chatbox');?>" href="<?php echo erLhcoreClassDesign::baseurl('chat/delete')?>/<?php echo $chat->id?>"><img class="action-image" align="absmiddle" src="<?php echo erLhcoreClassDesign::design('images/icons/delete.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Delete chat');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Delete chat');?>"></a> <?php echo $chat->id;?>. <?php echo htmlspecialchars($chat->nick);?> (<?php echo date('Y-m-d H:i:s',$chat->chat->time);?>) (<?php echo htmlspecialchars($chat->chat->department);?>)
        </td>
        <td><a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('chatbox/edit')?>/<?php echo $chat->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Edit');?></a></td>
    </tr>
    <?php endforeach; ?>
</table>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<?php } else { ?>
<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Empty...');?></p>
<?php } ?>
