<fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Active chats list');?></legend> 

<?php if ($pages->items_total > 0) { ?>
<div class="pagination-info"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats',"Page %currentpage of %totalpage",array('currentpage' => $pages->current_page,'totalpage' => $pages->num_pages))?></div>
<table class="lentele" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <th width="1%">ID</th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Information');?></th>
    </tr>
    <?php foreach (erLhcoreClassChat::getActiveChats($pages->items_per_page,$pages->low) as $chat) : ?>
    <tr>
        <td><?php echo $chat['id']?></td>
        <td>
           <img class="action-image" align="absmiddle" onclick="lhinst.startChatNewWindow('<?php echo $chat['id'];?>','<?php echo htmlspecialchars($chat['nick']);?>')" src="<?php echo erLhcoreClassDesign::design('images/icons/application_add.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Open in new window');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Open in new window');?>">
           <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Close chat');?>" href="<?php echo erLhcoreClassDesign::baseurl('chat/closechat/'.$chat['id'])?>"><img class="action-image" align="absmiddle" onclick="lhinst.closeActiveChatDialog('<?php echo $chat['id'];?>',$('#tabs'),false)" src="<?php echo erLhcoreClassDesign::design('images/icons/cancel.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Close chat');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Close chat');?>"></a>
	       <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Delete chat');?>" href="<?php echo erLhcoreClassDesign::baseurl('chat/delete/'.$chat['id'])?>"><img class="action-image" align="absmiddle" src="<?php echo erLhcoreClassDesign::design('images/icons/delete.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Delete chat');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Delete chat');?>"></a> <?php echo $chat['id'];?>. <?php echo htmlspecialchars($chat['nick']);?> (<?php echo date('Y-m-d H:i',$chat['time']);?>) (<?php echo $chat['name'];?>)
        </td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="2"><?php echo $pages->display_pages();?></td>
    </tr>
</table>

<?php } else { ?>
<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Empty...');?>
<?php } ?>
</fieldset>