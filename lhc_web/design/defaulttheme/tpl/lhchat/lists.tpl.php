<div role="tabpanel" id="tabs" ng-cloak>
        <ul class="nav nav-pills" role="tablist">
             <li role="presentation" class="active nav-item"><a class="nav-link" href="#chatlist" aria-controls="chatlist" role="tab" data-toggle="tab" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Chat list');?>"><i class="material-icons mr-0">&#xf2fd;</i></a></li>
             <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/chats_dashboard_list_tab.tpl.php')); ?>
        </ul>
        
        <div class="tab-content" ng-cloak> 
                <div role="tabpanel" class="tab-pane form-group active" id="chatlist">
                    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/search_panel.tpl.php')); ?>

                    <?php if ($pages->items_total > 0) { ?>
                    
                    <form action="<?php echo $input->form_action,$inputAppend?>" method="post">
                    
                    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
                    
                    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_chats_parts/append_table_class.tpl.php'));?>
                    
                    <table cellpadding="0" cellspacing="0" class="table<?php echo $appendTableClass?>" width="100%">
                        <thead>
                            <tr>
                            	<th width="1%"><input class="mb-0" type="checkbox" ng-model="check_all_items" /></th>
                                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Information');?></th>
                                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Operator');?></th>
                                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Department');?></th>
                                <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Status');?></th>
                                <th width="1%"></th>
                            </tr>
                        </thead>
                        <?php foreach ($items as $chat) : ?>
                        <tr>
                        	<td><?php if ($chat->can_edit_chat == true) : ?><input ng-checked="check_all_items" class="mb-0" type="checkbox" name="ChatID[]" value="<?php echo $chat->id?>" /><?php endif;?></td>
                            <td>        
                              <span title="<?php echo $chat->id;?>" class="material-icons fs12 mr-0<?php echo $chat->user_status_front == 2 ? ' icon-user-away' : ($chat->user_status_front == 0 ? ' icon-user-online' : ' icon-user-offline')?>" class="">&#xf643;</span>&nbsp;
                            
                              <?php if ( !empty($chat->country_code) ) : ?><img src="<?php echo erLhcoreClassDesign::design('images/flags');?>/<?php echo $chat->country_code?>.png" alt="<?php echo htmlspecialchars($chat->country_name)?>" title="<?php echo htmlspecialchars($chat->country_name)?>" />&nbsp;<?php endif; ?>
                              <a class="material-icons" onclick="lhc.previewChat(<?php echo $chat->id?>)">&#xf2fd;</a>
                              
                              <a class="action-image material-icons" data-title="<?php echo htmlspecialchars($chat->nick,ENT_QUOTES);?>" onclick="lhinst.startChatNewWindow('<?php echo $chat->id;?>',$(this).attr('data-title'))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Open in a new window');?>">&#xf3cc;</a>

                    	      <?php if ($chat->can_edit_chat && ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT && ($can_delete_global == true || ($can_delete_general == true && $chat->user_id == $current_user_id)))) : ?>
                    	           <a class="csfr-required material-icons" onclick="return confirm(confLH.transLation.delete_confirm)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Reject chat');?>" href="<?php echo erLhcoreClassDesign::baseurl('chat/delete')?>/<?php echo $chat->id?>">&#xf1c0;</a>
                    	      <?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) : ?>
                    	           
                    	           <?php if ($chat->can_edit_chat && ($can_close_global == true || $chat->user_id == $current_user_id)) : ?>
                    	           <a class="csfr-required material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Close chat');?>" href="<?php echo erLhcoreClassDesign::baseurl('chat/closechat')?>/<?php echo $chat->id?>">&#xf156;</a>
                    	           <?php endif;?>
                    	           
                    	           <?php if ($chat->can_edit_chat && ($can_delete_global == true || ($can_delete_general == true && $chat->user_id == $current_user_id))) : ?>
                    	           <a class="csfr-required material-icons" onclick="return confirm(confLH.transLation.delete_confirm)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Delete chat');?>" href="<?php echo erLhcoreClassDesign::baseurl('chat/delete')?>/<?php echo $chat->id?>">&#xf1c0;</a>
                    	           <?php endif;?>
                    	           
                    	      <?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_CHATBOX_CHAT) : ?>  
                    	           <?php if ($chat->can_edit_chat && ($can_delete_global == true || ($can_delete_general == true && $chat->user_id == $current_user_id))) : ?><a onclick="return confirm(confLH.transLation.delete_confirm)" class="csfr-required material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closedchats','Delete chat');?>" href="<?php echo erLhcoreClassDesign::baseurl('chat/delete')?>/<?php echo $chat->id?>">&#xf1c0;</a><?php endif;?>
                    	      <?php endif;?>

                                <?php if ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_OFFLINE_REQUEST) : ?><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Offline request')?>" class="material-icons">&#xf5ef;</i><?php endif?>

                                <a ng-click="lhc.startChat('<?php echo $chat->id?>','<?php echo htmlspecialchars($chat->nick,ENT_QUOTES)?>')"><?php echo htmlspecialchars($chat->nick);?>, <small><i><?php echo date(erLhcoreClassModule::$dateDateHourFormat,$chat->time);?></i></small>, <?php echo htmlspecialchars($chat->department),($chat->product !== false ? ' | '.htmlspecialchars((string)$chat->product) : '');?></a>


                    	      <?php if ($chat->has_unread_messages == 1) : ?>
                    	      <?php
                    	      $diff = time()-$chat->last_user_msg_time;
                    	      $hours = floor($diff/3600);
                    	      $minits = floor(($diff - ($hours * 3600))/60);
                    	      $seconds = ($diff - ($hours * 3600) - ($minits * 60));
                    	      ?> | <b><?php echo $hours?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','h.');?> <?php echo $minits ?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','m.');?> <?php echo $seconds?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','s.');?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','ago');?>.</b>
                    	      <?php endif;?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($chat->user);?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($chat->department);?>
                            </td>
                            <td nowrap="nowrap">
                                <?php if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) : ?>
                                    <i class="material-icons chat-pending">&#xfb55;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Pending chat');?>
                                <?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) : ?>
                                    <i class="material-icons chat-active">&#xfb55;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Active chat');?>
                                <?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?>
                                    <i class="material-icons chat-closed">&#xfb55;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Closed chat');?>
                                <?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_CHATBOX_CHAT) : ?>
                                     <i class="material-icons chat-active">&#xfb55;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Chatbox chat');?>
                                <?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT) : ?>
                                     <i class="material-icons chat-active">&#xf643;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Operators chat');?>
                                <?php endif;?>
                                    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_chats_parts/status_multiinclude.tpl.php'));?>
                            </td>
                            <td><?php if ($chat->fbst == 1) : ?><i class="material-icons up-voted">&#xf514;</i><?php elseif ($chat->fbst == 2) : ?><i class="material-icons down-voted">&#xf512;<i><?php endif;?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                    
                    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>
                    
                    <?php if (isset($pages)) : ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
                    <?php endif;?>

                    <div class="btn-group" role="group" aria-label="...">
                        <input type="submit" name="doClose" class="btn btn-warning" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Close selected');?>" />
                        <input type="submit" name="doDelete" class="btn btn-danger" onclick="return confirm(confLH.transLation.delete_confirm)" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Delete selected');?>" />
                    </div>

                    </form>
                    
                    <?php } else { ?>
                    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Empty...');?></p>
                    <?php } ?>
                       
                </div>

                <div role="tabpanel" class="tab-pane form-group" id="chatdashboard">
                    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/chats_dashboard_list.tpl.php')); ?>
                </div>

        </div>
</div>

<script>
$( document ).ready(function() {
	lhinst.attachTabNavigator();
	$('#right-column-page').removeAttr('id');
	$('#tabs a:first').tab('show')
});
</script>


