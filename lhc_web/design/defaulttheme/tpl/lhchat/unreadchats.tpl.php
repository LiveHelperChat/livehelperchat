<?php include(erLhcoreClassDesign::designtpl('lhchat/lists_titles/unreadchats.tpl.php'));?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/search_panel.tpl.php')); ?>

<?php if ($pages->items_total > 0) { ?>
<?php include(erLhcoreClassDesign::designtpl('lhchat/lists_chats_parts/append_table_class.tpl.php'));?>

<table cellpadding="0" cellspacing="0" class="table<?php echo $appendTableClass?>" width="100%">
<thead>
    <tr>
        <th width="1%">ID</th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/unreadchats','Information');?></th>
        <th width="1%"></th>
    </tr>
</thead>
    <?php foreach ($items as $chat) : ?>
    <tr>
        <td><?php echo $chat->id?></td>
        <td>
          <?php if ( !empty($chat->country_code) ) : ?><img src="<?php echo erLhcoreClassDesign::design('images/flags');?>/<?php echo $chat->country_code?>.png" alt="<?php echo htmlspecialchars($chat->country_name)?>" title="<?php echo htmlspecialchars($chat->country_name)?>" />&nbsp;<?php endif; ?>
	      <a class="material-icons" data-title="<?php echo htmlspecialchars($chat->nick,ENT_QUOTES);?>" onclick="lhinst.startChatNewWindow('<?php echo $chat->id;?>',$(this).attr('data-title'))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>">open_in_new</a>
	      <?php if ($chat->status == 1 && ($can_close_global == true ||  $chat->user_id == $current_user_id)) : ?><a class="csfr-required" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Close chat');?>" href="<?php echo erLhcoreClassDesign::baseurl('chat/closechat')?>/<?php echo $chat->id?>"><img class="action-image" align="absmiddle" src="<?php echo erLhcoreClassDesign::design('images/icons/cancel.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Close chat');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Close chat');?>"></a><?php endif;?>
	      <?php if ($can_delete_global == true || ($can_delete_general == true && $chat->user_id == $current_user_id)) : ?><a class="csfr-required material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Delete chat');?>" href="<?php echo erLhcoreClassDesign::baseurl('chat/delete')?>/<?php echo $chat->id?>">delete</a><?php endif;?> <?php echo $chat->id;?>. <?php echo htmlspecialchars($chat->nick);?> (<?php echo date(erLhcoreClassModule::$dateDateHourFormat,$chat->time);?>) (<?php echo htmlspecialchars($chat->department);?>) | <?php
	      $diff = time()-$chat->last_user_msg_time;
	      $hours = floor($diff/3600);
	      $minits = floor(($diff - ($hours * 3600))/60);
	      $seconds = ($diff - ($hours * 3600) - ($minits * 60));
	      ?><b><?php echo $hours?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','h.');?> <?php echo $minits ?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','m.');?> <?php echo $seconds?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','s.');?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','ago');?>.</b>
        </td>
        <td><?php if ($chat->fbst == 1) : ?><i class="material-icons up-voted">thumb_up</i><?php elseif ($chat->fbst == 2) : ?><i class="material-icons down-voted">thumb_down<i><?php endif;?></td>
    </tr>
    <?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<?php } else { ?>
<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/unreadchats','Empty...');?></p>
<?php } ?>
