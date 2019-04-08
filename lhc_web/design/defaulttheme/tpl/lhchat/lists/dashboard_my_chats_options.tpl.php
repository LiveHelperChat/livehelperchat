<li class="nav-item dropright ml-auto" ng-init="lhc.getToggleWidget('only_user',true);">
    <a class="nav-link dropdown-toggle pl-2 pr-2 pt-0 pb-0" data-toggle="dropdown" role="button" aria-expanded="false"><i class="material-icons mr-0">&#xf493;</i></a>
    <ul class="dropdown-menu" role="menu">
        <li class="dropdown-item fs12"><a href="#" ng-click="lhc.toggleWidget('only_user')" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Show chats only assigned to me')?>"><i class="material-icons" id="group-chats-status" ng-class="{'chat-active': lhc.toggleWidgetData['only_user'] === true, 'chat-closed': lhc.toggleWidgetData['only_user'] !== true}">&#xf12c;</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Show chats only assigned to me'); ?></a></li>

        <?php if (isset($showChatsLists) && $showChatsLists == true) : ?>
            <li class="dropdown-item fs12"><a href="<?php echo erLhcoreClassDesign::baseurl('/')?>"><i class="material-icons">&#xf2dc;</i>Return to dashboard</a></li>
        <?php else : ?>
        <li class="dropdown-item fs12"><a href="<?php echo erLhcoreClassDesign::baseurl('chat/chattabs')?>"><i class="material-icons">&#xf293;</i>Fullscreen window</a></li>
        <?php endif; ?>

        <li class="dropdown-item fs12"><a href="#" class="nav-item" onclick="return lhc.revealModal({'url':WWW_DIR_JAVASCRIPT +'chat/dashboardwidgets'})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Configure dashboard')?>"><i class="material-icons">&#xfa06;</i>Dashboard configuration</a></li>

        <?php if (isset($showChatsLists) && $showChatsLists == true) : ?>
            <li class="dropdown-divider"></li>
            <?php if ($pendingTabEnabled == true) : ?>
                <li role="presentation" class="dropdown-item"><a ng-click="lhc.currentPanel = 'pending-chats';lhc.current_chat_id = 0" class="fs12" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Pending confirm');?>" href="#panel1" aria-controls="panel1" role="tab" data-toggle="tab"><i class="material-icons chat-pending">&#xfb55;</i>Pending chats <span>{{pending_chats.list.length != false && pending_chats.list.length > 0 ? ' ('+pending_chats.list.length+')' : ''}}</span></a></li>
            <?php endif;?>

            <?php if ($activeTabEnabled == true) : ?>
                <li role="presentation" class="dropdown-item"><a ng-click="lhc.currentPanel = 'active-chats';lhc.current_chat_id = 0" class="fs12" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Active chats');?>" href="#panel2" aria-controls="panel2" role="tab" data-toggle="tab"><i class="material-icons chat-active">&#xfb55;</i>Active chats <span>{{active_chats.list.length != false && active_chats.list.length > 0 ? ' ('+active_chats.list.length+')' : ''}}</span></a></li>
            <?php endif;?>

            <?php if ($unreadTabEnabled == true) : ?>
                <li role="presentation" class="dropdown-item"><a ng-click="lhc.currentPanel = 'unread-chats';lhc.current_chat_id = 0" class="fs12" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Unread messages');?>" href="#panel3" aria-controls="panel3" role="tab" data-toggle="tab"><i class="material-icons chat-unread">&#xfb55;</i>Unread chats <span>{{unread_chats.list.length != false && unread_chats.list.length > 0 ? ' ('+unread_chats.list.length+')' : ''}}</span></a></li>
            <?php endif;?>

            <?php if ($closedTabEnabled == true) : ?>
                <li role="presentation" class="dropdown-item"><a ng-click="lhc.currentPanel = 'closed-chats';lhc.current_chat_id = 0" class="fs12" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Closed chats');?>" href="#panel4" aria-controls="panel4" role="tab" data-toggle="tab"><i class="material-icons chat-closed">&#xfb55;</i><span>Closed chats {{closed_chats.list.length != false && closed_chats.list.length > 0 ? ' ('+closed_chats.list.length+')' : ''}}</span></a></li>
            <?php endif;?>

        <?php endif; ?>


        <li class="dropdown-divider"></li>
        <?php

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

            <li class="dropdown-item"><a href="#" ng-click="lhc.setSettingAjax('chat_message',lhc.n_msg_snd == 1 ? 0 : 1,'n_msg_snd')"><i class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Enable/Disable sound about new messages from users');?>">{{lhc.n_msg_snd == true ? '&#xf57e;' : '&#xf581;'}}</i><span class="nav-link-text">New messages</span></a></li>
            <li class="dropdown-item"><a href="#" ng-click="lhc.setSettingAjax('new_chat_sound',lhc.n_chat_snd == 1 ? 0 : 1,'n_chat_snd')"><i class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Enable/Disable sound about new pending chats');?>">{{lhc.n_chat_snd == true ? '&#xf57e;' : '&#xf581;'}}</i><span class="nav-link-text">New chats</span></a></li>

            <?php if ($canChangeVisibilityMode == true) : ?>
                <li class="dropdown-item"><a href="#" ng-click="lhc.changeVisibility()"><i id="vi-in-user" class="material-icons ng-cloak" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my visibility to visible/invisible');?>" >{{lhc.hideInvisible == true ? '&#xf209' : '&#xf208'}}</i>Visible/Invisible</a></li>
            <?php endif;?>

            <?php if ($canChangeOnlineStatus == true) : ?>
                <li class="dropdown-item"><a href="#" ng-click="lhc.changeOnline()"><i id="online-offline-user" class="material-icons ng-cloak" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my status to online/offline');?>" >{{lhc.hideOnline == true ? '&#xf243' : '&#xf241'}}</i>Online/Offline</a></li>
            <?php endif;?>

        <?php endif;?>


    </ul>
</li>
