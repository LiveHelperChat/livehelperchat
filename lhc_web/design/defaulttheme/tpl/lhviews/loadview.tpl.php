<?php if ($search->scope == 'chat') : ?>
    <?php if (!$list_mode) : ?>
    <div role="tabpanel" id="tabs" ng-cloak>
        <ul class="nav nav-pills" role="tablist">
            <li role="presentation" class="nav-item"><a class="nav-link active" href="#chatlist" aria-controls="chatlist" ng-non-bindable role="tab" data-toggle="tab" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Chat list');?>">
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
                            <th width="10%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Information');?></th>
                            <th nowrap width="45%">
                                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Nick');?>
                            </th>
                            <th width="15%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Operator');?></th>
                            <th width="10%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Department');?></th>
                            <th width="10%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Status');?></th>
                            <th width="5%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Date');?></th>
                        </tr>
                        </thead>
                        <?php foreach ($items as $chat) : ?>
                            <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/start_row.tpl.php')); ?>
                            <td nowrap="">
                                <?php foreach ($chat->aicons as $aicon) : ?>
                                    <i class="material-icons" style="color: <?php isset($aicon['c']) ? print htmlspecialchars($aicon['c']) : print '#6c757d'?>" title="<?php isset($aicon['t']) ? print htmlspecialchars($aicon['t']) : htmlspecialchars($aicon['i'])?> {{icon.t ? icon.t : icon.i}}"><?php isset($aicon['i']) ? print htmlspecialchars($aicon['i']) : htmlspecialchars($aicon)?></i>
                                <?php endforeach; ?>

                                <?php if ( !empty($chat->country_code) ) : ?><img src="<?php echo erLhcoreClassDesign::design('images/flags');?>/<?php echo $chat->country_code?>.png" alt="<?php echo htmlspecialchars($chat->country_name)?>" title="<?php echo htmlspecialchars($chat->country_name)?>" />&nbsp;<?php endif; ?>

                                <span class="action-image material-icons <?php echo $chat->user_status_front == 2 ? ' icon-user-away' : ($chat->user_status_front == 0 ? ' icon-user-online' : ' icon-user-offline')?>" onclick="lhc.previewChat(<?php echo $chat->id?>)">info_outline</span>

                                <a ng-non-bindable class="action-image material-icons" data-title="<?php echo htmlspecialchars($chat->nick,ENT_QUOTES);?>" onclick="lhinst.startChatNewWindow('<?php echo $chat->id;?>',$(this).attr('data-title'))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/pendingchats','Open in a new window');?>">open_in_new</a>

                                <a href="#" onclick="ee.emitEvent('angularStartChatbyId',[<?php echo $chat->id?>])"><?php echo $chat->id?></a>

                                <?php if ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_OFFLINE_REQUEST) : ?><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/activechats','Offline request')?>" class="material-icons">mail</i><?php endif?>

                                <span ng-non-bindable><?php echo ($chat->product !== false ? ' '.htmlspecialchars((string)$chat->product) : '');?></span></a>

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
                                <?php echo htmlspecialchars($chat->nick);?>
                            </td>
                            <td ng-non-bindable nowrap="nowrap">
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
                            <td nowrap="nowrap">
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
