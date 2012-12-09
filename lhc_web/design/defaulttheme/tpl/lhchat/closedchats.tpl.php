<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closedchats','Closed chats list');?></h1> 
<?php if ($pages->items_total > 0) { ?>
<div class="pagination-info"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closedchats',"Page %currentpage of %totalpage",array('currentpage' => $pages->current_page,'totalpage' => $pages->num_pages))?></div>

<table class="lentele" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <th width="1%">ID</th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closedchats','Information');?></th>
    </tr>
    <?php foreach (erLhcoreClassChat::getClosedChats($pages->items_per_page,$pages->low) as $chat) : ?>
    <tr>
        <td><?php echo $chat['id']?></td>
        <td>
           <img class="action-image" align="absmiddle" onclick="lhinst.startChatNewWindow('<?php echo $chat['id'];?>','<?php echo htmlspecialchars($chat['nick']);?>')" src="<?php echo erLhcoreClassDesign::design('images/icons/application_add.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closedchats','Open in new window');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closedchats','Open in new window');?>">
           
           <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closedchats','Delete chat');?>" href="<?php echo erLhcoreClassDesign::baseurl('chat/delete/'.$chat['id'])?>"><img class="action-image" align="absmiddle" src="<?php echo erLhcoreClassDesign::design('images/icons/delete.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closedchats','Delete chat');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closedchats','Delete chat');?>"></a> <?php echo $chat['id'];?>. <?php echo htmlspecialchars($chat['nick']);?> (<?php echo date('Y-m-d H:i',$chat['time']);?>) (<?php echo $chat['name'];?>)
        </td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="2"><?php echo $pages->display_pages();?></td>
    </tr>
</table>
<?php } else { ?>
<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closedchats','Empty...');?>
<?php } ?>
