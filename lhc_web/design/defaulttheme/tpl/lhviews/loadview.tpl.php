<?php if ($search->scope == 0) : ?>
    <?php if (!$list_mode) : ?>
    <div role="tabpanel" id="tabs" ng-cloak>
        <ul class="nav nav-pills" role="tablist">
            <li role="presentation" class="nav-item"><a class="nav-link active" href="#chatlist" aria-controls="chatlist" role="tab" data-toggle="tab" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Chat list');?>">
                    <?php echo htmlspecialchars($search->name)?> </a>
            </li>
        </ul>
        <div class="tab-content" ng-cloak>
            <div role="tabpanel" class="tab-pane form-group active" id="chatlist">
                <div id="view-content-list">
                    <?php endif; ?>

                    <table cellpadding="0" cellspacing="0" class="table table-sm" width="100%">
                        <thead>
                        <tr>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Information');?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Operator');?></th>
                            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Department');?></th>
                            <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Status');?></th>
                            <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Date');?></th>
                        </tr>
                        </thead>
                        <?php foreach ($items as $chat) : ?>
                            <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/start_row.tpl.php')); ?>
                            <td>
                                <?php foreach ($chat->aicons as $aicon) : ?>
                                    <i class="material-icons" style="color: <?php isset($aicon['c']) ? print htmlspecialchars($aicon['c']) : print '#6c757d'?>" title="<?php isset($aicon['t']) ? print htmlspecialchars($aicon['t']) : htmlspecialchars($aicon['i'])?> {{icon.t ? icon.t : icon.i}}"><?php isset($aicon['i']) ? print htmlspecialchars($aicon['i']) : htmlspecialchars($aicon)?></i>
                                <?php endforeach; ?>

                                <span title="<?php echo $chat->id;?>" class="material-icons fs12 mr-0<?php echo $chat->user_status_front == 2 ? ' icon-user-away' : ($chat->user_status_front == 0 ? ' icon-user-online' : ' icon-user-offline')?>" class="">&#xE3A6;</span>&nbsp;

                                <?php if ( !empty($chat->country_code) ) : ?><img src="<?php echo erLhcoreClassDesign::design('images/flags');?>/<?php echo $chat->country_code?>.png" alt="<?php echo htmlspecialchars($chat->country_name)?>" title="<?php echo htmlspecialchars($chat->country_name)?>" />&nbsp;<?php endif; ?>
                                <a class="material-icons" onclick="lhc.previewChat(<?php echo $chat->id?>)">info_outline</a>

                                <a ng-non-bindable class="action-image material-icons" data-title="<?php echo htmlspecialchars($chat->nick,ENT_QUOTES);?>" onclick="lhinst.startChatNewWindow('<?php echo $chat->id;?>',$(this).attr('data-title'))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Open in a new window');?>">open_in_new</a>

                                <?php if ($chat->can_edit_chat && ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT && ($can_delete_global == true || ($can_delete_general == true && $chat->user_id == $current_user_id)))) : ?>
                                    <a class="csfr-required material-icons" onclick="return confirm(confLH.transLation.delete_confirm)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Reject chat');?>" href="<?php echo erLhcoreClassDesign::baseurl('chat/delete')?>/<?php echo $chat->id?>">delete</a>
                                <?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) : ?>

                                    <?php if ($chat->can_edit_chat && ($can_close_global == true || $chat->user_id == $current_user_id)) : ?>
                                        <a class="csfr-required material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Close chat');?>" href="<?php echo erLhcoreClassDesign::baseurl('chat/closechat')?>/<?php echo $chat->id?>">close</a>
                                    <?php endif;?>

                                    <?php if ($chat->can_edit_chat && ($can_delete_global == true || ($can_delete_general == true && $chat->user_id == $current_user_id))) : ?>
                                        <a class="csfr-required material-icons" onclick="return confirm(confLH.transLation.delete_confirm)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Delete chat');?>" href="<?php echo erLhcoreClassDesign::baseurl('chat/delete')?>/<?php echo $chat->id?>">delete</a>
                                    <?php endif;?>

                                <?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_CHATBOX_CHAT) : ?>
                                    <?php if ($chat->can_edit_chat && ($can_delete_global == true || ($can_delete_general == true && $chat->user_id == $current_user_id))) : ?><a onclick="return confirm(confLH.transLation.delete_confirm)" class="csfr-required material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closedchats','Delete chat');?>" href="<?php echo erLhcoreClassDesign::baseurl('chat/delete')?>/<?php echo $chat->id?>">delete</a><?php endif;?>
                                <?php endif;?>

                                <?php if ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_OFFLINE_REQUEST) : ?><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Offline request')?>" class="material-icons">mail</i><?php endif?>

                                <a onclick="ee.emitEvent('angularStartChatbyId',[<?php echo $chat->id?>])"><span ng-non-bindable><?php echo htmlspecialchars($chat->nick);?></span><span ng-non-bindable><?php echo ($chat->product !== false ? ', '.htmlspecialchars((string)$chat->product) : '');?></span></a>

                                <?php if ($chat->has_unread_messages == 1) : ?>
                                    <?php
                                    $diff = time()-$chat->last_user_msg_time;
                                    $hours = floor($diff/3600);
                                    $minits = floor(($diff - ($hours * 3600))/60);
                                    $seconds = ($diff - ($hours * 3600) - ($minits * 60));
                                    ?> | <b><?php echo $hours?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','h.');?> <?php echo $minits ?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','m.');?> <?php echo $seconds?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','s.');?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','ago');?>.</b>
                                <?php endif;?>
                            </td>
                            <td ng-non-bindable>
                                <?php echo htmlspecialchars($chat->user);?>
                            </td>
                            <td ng-non-bindable nowrap="nowrap">
                                <?php echo htmlspecialchars($chat->department);?>
                            </td>
                            <td nowrap="nowrap">
                                <?php if ($chat->fbst == 1) : ?><i class="material-icons up-voted">thumb_up</i><?php elseif ($chat->fbst == 2) : ?><i class="material-icons down-voted">thumb_down<i><?php endif;?>
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
                                <?php elseif ($chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT) : ?>
                                    <i class="material-icons chat-active">android</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Bot chat');?>
                                <?php endif;?>
                                <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_chats_parts/status_multiinclude.tpl.php'));?>
                            </td>
                            <td nowrap="nowrap" class="pl-3">
                                <?php echo $chat->start_last_action_front?>
                            </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    
                    <?php if (isset($pages)) : ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
                    <?php endif;?>

                    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

                    <?php if (!$list_mode) : ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

<?php else : ?>

<?php endif; ?>
