<fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Pending chats list');?></legend> 
<? if ($pages->items_total > 0) { ?>
<div class="pagination-info"><?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats',"Page %currentpage of %totalpage",array('currentpage' => $pages->current_page,'totalpage' => $pages->num_pages))?></div>

<table class="lentele" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <th width="1%">ID</th>
        <th><?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Information');?></th>
    </tr>
    <? foreach (erLhcoreClassChat::getPendingChats($pages->items_per_page,$pages->low) as $chat) : ?>
    <tr>
        <td><?=$chat['id']?></td>
        <td>
          <img class="action-image" align="absmiddle" onclick="lhinst.startChatNewWindow('<?=$chat['id'];?>','<?=htmlspecialchars($chat['nick']);?>')" src="<?=erLhcoreClassDesign::design('images/icons/application_add.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Open in new window');?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Open in new window');?>">
	      <a title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Reject chat');?>" href="<?=erLhcoreClassDesign::baseurl('chat/delete/'.$chat['id'])?>"><img class="action-image" align="absmiddle" src="<?=erLhcoreClassDesign::design('images/icons/cancel.png');?>" alt="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Reject chat');?>" title="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Reject chat');?>"></a>
	      <?=$chat['id'];?>. <?=htmlspecialchars($chat['nick']);?> (<?=date('Y-m-d H:i',$chat['time']);?>) (<?=$chat['name'];?>)
        </td>
    </tr>
    <? endforeach; ?>
    <tr>
        <td colspan="2"><?=$pages->display_pages();?></td>
    </tr>
</table>
<? } else { ?>
<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Empty...');?>
<? } ?>
</fieldset>