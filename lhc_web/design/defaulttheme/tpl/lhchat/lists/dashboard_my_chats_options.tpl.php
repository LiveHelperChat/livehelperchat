<nav class="float-right" ng-init="lhc.getToggleWidget('only_user',true);">
    <ul class="nav" >
        <li class="nav-item dropright" style="line-height:1">
            <a class="nav-link dropdown-toggle pt-1 mt-0 pl-1 pr-1" data-toggle="dropdown" role="button" aria-expanded="false"><i class="material-icons mr-0">settings_applications</i></a>
            <ul class="dropdown-menu" role="menu">
                <li class="dropdown-item fs12"><a href="#" ng-click="lhc.toggleWidget('only_user')" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Show chats only assigned to me')?>"><i class="material-icons" id="group-chats-status" ng-class="{'chat-active': lhc.toggleWidgetData['only_user'] === true, 'chat-closed': lhc.toggleWidgetData['only_user'] !== true}">done</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Show chats only assigned to me'); ?></a></li>

                <?php if (isset($showChatsLists) && $showChatsLists == true) : ?>
                    <li class="dropdown-divider"></li>
                    <?php if ($pendingTabEnabled == true) : ?>
                        <li role="presentation" class="dropdown-item"><a ng-click="lhc.currentPanel = 'pending-chats';lhc.current_chat_id = 0" class="fs12" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Pending confirm');?>" href="#panel1" aria-controls="panel1" role="tab" data-toggle="tab"><i class="material-icons chat-pending">chat</i>Pending chats <span>{{pending_chats.list.length != false && pending_chats.list.length > 0 ? ' ('+pending_chats.list.length+')' : ''}}</span></a></li>
                    <?php endif;?>

                    <?php if ($activeTabEnabled == true) : ?>
                        <li role="presentation" class="dropdown-item"><a ng-click="lhc.currentPanel = 'active-chats';lhc.current_chat_id = 0" class="fs12" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Active chats');?>" href="#panel2" aria-controls="panel2" role="tab" data-toggle="tab"><i class="material-icons chat-active">chat</i>Active chats <span>{{active_chats.list.length != false && active_chats.list.length > 0 ? ' ('+active_chats.list.length+')' : ''}}</span></a></li>
                    <?php endif;?>

                    <?php if ($unreadTabEnabled == true) : ?>
                        <li role="presentation" class="dropdown-item"><a ng-click="lhc.currentPanel = 'unread-chats';lhc.current_chat_id = 0" class="fs12" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Unread messages');?>" href="#panel3" aria-controls="panel3" role="tab" data-toggle="tab"><i class="material-icons chat-unread">chat</i>Unread chats <span>{{unread_chats.list.length != false && unread_chats.list.length > 0 ? ' ('+unread_chats.list.length+')' : ''}}</span></a></li>
                    <?php endif;?>

                    <?php if ($closedTabEnabled == true) : ?>
                        <li role="presentation" class="dropdown-item"><a ng-click="lhc.currentPanel = 'closed-chats';lhc.current_chat_id = 0" class="fs12" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Closed chats');?>" href="#panel4" aria-controls="panel4" role="tab" data-toggle="tab"><i class="material-icons chat-closed">chat</i><span>Closed chats {{closed_chats.list.length != false && closed_chats.list.length > 0 ? ' ('+closed_chats.list.length+')' : ''}}</span></a></li>
                    <?php endif;?>

                <?php endif; ?>


                <li class="dropdown-divider"></li>
                <?php

                $soundData = erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data;

                $soundMessageEnabled = erLhcoreClassModelUserSetting::getSetting('chat_message',(int)($soundData['new_message_sound_admin_enabled']));
                $soundNewChatEnabled = erLhcoreClassModelUserSetting::getSetting('new_chat_sound',(int)($soundData['new_chat_sound_enabled']));

                $canChangeOnlineStatus = false;
                $currentUser = erLhcoreClassUser::instance();
                if ( $currentUser->hasAccessTo('lhuser','changeonlinestatus') ) {
                    $canChangeOnlineStatus = true;
                    if ( !isset($UserData) ) {
                        $UserData = $currentUser->getUserData(true);
                    }
                }

                $canChangeVisibilityMode = false;
                if ( $currentUser->hasAccessTo('lhuser','changevisibility') ) {
                    $canChangeVisibilityMode = true;
                    if ( !isset($UserData) ) {
                        $UserData = $currentUser->getUserData(true);
                    }
                }
                ?>

                <?php if ($currentUser->hasAccessTo('lhchat','use') ) : ?>

                    <li class="dropdown-item"><a href="#"><i class="material-icons" onclick="return lhinst.disableChatSoundAdmin($(this))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Enable/Disable sound about new messages from users');?>"><?php $soundMessageEnabled == 0 ? print 'volume_off' : print 'volume_up'?></i></a></li>

                    <li class="dropdown-item"><a href="#"><i class="material-icons" onclick="return lhinst.disableNewChatSoundAdmin($(this))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Enable/Disable sound about new pending chats');?>"><?php $soundNewChatEnabled == 0 ? print 'volume_off' : print 'volume_up'?></i></a></li>

                    <?php if ($canChangeVisibilityMode == true) : ?>
                        <li class="dropdown-item"><a href="#" ng-click="lhc.changeVisibility()"><i id="vi-in-user" class="material-icons ng-cloak" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my visibility to visible/invisible');?>" >{{lhc.hideInvisible == true ? 'visibility_off' : 'visibility'}}</i></a></li>
                    <?php endif;?>

                    <?php if ($canChangeOnlineStatus == true) : ?>
                        <li class="dropdown-item"><a href="#" ng-click="lhc.changeOnline()"><i id="online-offline-user" class="material-icons ng-cloak" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my status to online/offline');?>" >{{lhc.hideOnline == true ? 'flash_off' : 'flash_on'}}</i></a></li>
                    <?php endif;?>

                <?php endif;?>


            </ul>
        </li>
    </ul>
</nav>