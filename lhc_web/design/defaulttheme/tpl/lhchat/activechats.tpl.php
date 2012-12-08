<fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Active chats list');?></legend> 

<? if ($pages->items_total > 0) { ?>
<div class="pagination-info"><?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats',"Page %currentpage of %totalpage",array('currentpage' => $pages->current_page,'totalpage' => $pages->num_pages))?></div>
<table class="lentele" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <th width="1%">ID</th>
        <th><?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Information');?></th>
    </tr>
    <? foreach (erLhcoreClassChat::getActiveChats($pages->items_per_page,$pages->low) as $chat) : ?>
    <tr>
        <td><?=$chat['id']?></td>
        <td>
           <img class="action-image" align="absmiddle" onclick="lhinst.startChatNewWindow('<?=$chat['id'];?>','<?=htmlspecialchars($chat['nick']);?>')" src="<?=erLhcoreClassDesign::design('images/icons/application_add.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Open in new window');?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Open in new window');?>">
           <a title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Close chat');?>" href="<?=erLhcoreClassDesign::baseurl('chat/closechat/'.$chat['id'])?>"><img class="action-image" align="absmiddle" onclick="lhinst.closeActiveChatDialog('<?=$chat['id'];?>',$('#tabs'),false)" src="<?=erLhcoreClassDesign::design('images/icons/cancel.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Close chat');?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Close chat');?>"></a>
	       <a title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Delete chat');?>" href="<?=erLhcoreClassDesign::baseurl('chat/delete/'.$chat['id'])?>"><img class="action-image" align="absmiddle" src="<?=erLhcoreClassDesign::design('images/icons/delete.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Delete chat');?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Delete chat');?>"></a> <?=$chat['id'];?>. <?=htmlspecialchars($chat['nick']);?> (<?=date('Y-m-d H:i',$chat['time']);?>) (<?=$chat['name'];?>)
        </td>
    </tr>
    <? endforeach; ?>
    <tr>
        <td colspan="2"><?=$pages->display_pages();?></td>
    </tr>
</table>

<? } else { ?>
<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Empty...');?>
<? } ?>
</fieldset>