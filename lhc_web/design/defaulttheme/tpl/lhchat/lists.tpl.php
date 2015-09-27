<?php include(erLhcoreClassDesign::designtpl('lhchat/lists_titles/lists.tpl.php'));?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/search_panel.tpl.php')); ?>

<?php if ($pages->items_total > 0) { ?>

<form action="<?php echo $input->form_action,$inputAppend?>" method="post">

<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

<?php include(erLhcoreClassDesign::designtpl('lhchat/lists_chats_parts/append_table_class.tpl.php'));?>

<table cellpadding="0" cellspacing="0" class="table<?php echo $appendTableClass?>" width="100%">
    <thead>
        <tr>
        	<th width="1%"><input class="mb0" type="checkbox" ng-model="check_all_items" /></th>
            <th width="1%">ID</th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Information');?></th>
            <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Status');?></th>
            <th width="1%"></th>
        </tr>
    </thead>
    <?php foreach ($items as $chat) : ?>
    <tr>
    	<td><input ng-checked="check_all_items" class="mb0" type="checkbox" name="ChatID[]" value="<?php echo $chat->id?>" /></td>
        <td><?php echo $chat->id?></td>
        <td>
          <?php if ( !empty($chat->country_code) ) : ?><img src="<?php echo erLhcoreClassDesign::design('images/flags');?>/<?php echo $chat->country_code?>.png" alt="<?php echo htmlspecialchars($chat->country_name)?>" title="<?php echo htmlspecialchars($chat->country_name)?>" />&nbsp;<?php endif; ?>
          <a class="action-image material-icons" data-title="<?php echo htmlspecialchars($chat->nick,ENT_QUOTES);?>" onclick="lhinst.startChatNewWindow('<?php echo $chat->id;?>',$(this).attr('data-title'))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Open in a new window');?>">open_in_new</a>
	      
	      <?php if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT && ($can_delete_global == true || ($can_delete_general == true && $chat->user_id == $current_user_id))) : ?>
	           <a class="csfr-required material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Reject chat');?>" href="<?php echo erLhcoreClassDesign::baseurl('chat/delete')?>/<?php echo $chat->id?>">delete</a>
	      <?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) : ?>
	           
	           <?php if ($can_close_global == true || $chat->user_id == $current_user_id) : ?>
	           <a class="csfr-required material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Close chat');?>" href="<?php echo erLhcoreClassDesign::baseurl('chat/closechat')?>/<?php echo $chat->id?>">close</a>
	           <?php endif;?>
	           
	           <?php if ($can_delete_global == true || ($can_delete_general == true && $chat->user_id == $current_user_id)) : ?>
	           <a class="csfr-required material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Delete chat');?>" href="<?php echo erLhcoreClassDesign::baseurl('chat/delete')?>/<?php echo $chat->id?>">delete</a>
	           <?php endif;?>
	           
	      <?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_CHATBOX_CHAT) : ?>  
	           <?php if ($can_delete_global == true || ($can_delete_general == true && $chat->user_id == $current_user_id)) : ?><a class="csfr-required material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closedchats','Delete chat');?>" href="<?php echo erLhcoreClassDesign::baseurl('chat/delete')?>/<?php echo $chat->id?>">delete</a><?php endif;?>
	      <?php endif;?>
	      
	      <?php echo $chat->id;?>. <?php echo htmlspecialchars($chat->nick);?> (<?php echo date(erLhcoreClassModule::$dateDateHourFormat,$chat->time);?>) (<?php echo $chat->department;?>)
	      
	      <?php if ($chat->has_unread_messages == 1) : ?>
	      <?php
	      $diff = time()-$chat->last_user_msg_time;
	      $hours = floor($diff/3600);
	      $minits = floor(($diff - ($hours * 3600))/60);
	      $seconds = ($diff - ($hours * 3600) - ($minits * 60));
	      ?> | <b><?php echo $hours?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','h.');?> <?php echo $minits ?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','m.');?> <?php echo $seconds?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','s.');?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','ago');?>.</b>
	      <?php endif;?>
        </td>
        <td nowrap="nowrap">
            <?php if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) : ?>
                <i class="material-icons chat-pending">chat</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Pending chat');?>
            <?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) : ?>
                <i class="material-icons chat-active">chat</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Active chat');?>
            <?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?>
                <i class="material-icons chat-closed">chat</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Closed chat');?>
            <?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_CHATBOX_CHAT) : ?>
                 <i class="material-icons chat-active">chat</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Chatbox chat');?>
            <?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT) : ?>
                 <i class="material-icons chat-active">face</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Operators chat');?>
            <?php endif;?>        
        </td>
        <td><?php if ($chat->fbst == 1) : ?><i class="material-icons up-voted">thumb_up</i><?php elseif ($chat->fbst == 2) : ?><i class="material-icons down-voted">thumb_down<i><?php endif;?></td>
    </tr>
    <?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<input type="submit" name="doDelete" class="btn btn-danger" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Delete selected');?>" />

</form>

<?php } else { ?>
<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Empty...');?></p>
<?php } ?>
