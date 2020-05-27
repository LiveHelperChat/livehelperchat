<?php
$currentUser = erLhcoreClassUser::instance();
if ($currentUser->isLogged()) :
$UserData = $currentUser->getUserData(true); ?>
<li class="nav-item dropleft">
    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo htmlspecialchars($UserData->name),' ',htmlspecialchars($UserData->surname)?></a>
    <div class="dropdown-menu" role="menu">
        <a class="dropdown-item pl-2" href="<?php echo erLhcoreClassDesign::baseurl('user/account')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Account')?>"><i class="material-icons">account_box</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Account')?></a>
        <a class="dropdown-item pl-2" href="<?php echo erLhcoreClassDesign::baseurl('user/logout')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Logout');?>"><i class="material-icons">exit_to_app</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Logout');?></a>
        <?php if ($currentUser->hasAccessTo('lhsystem','use')) : ?>
            <a class="dropdown-item pl-2" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Configuration');?>" href="<?php echo erLhcoreClassDesign::baseurl('system/configuration')?>"><i class="material-icons">settings_applications</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Settings');?></a>
        <?php endif; ?>

        <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_chat_actions_pre.tpl.php'));?>

        <?php if ($parts_top_menu_chat_actions_enabled == true && $currentUser->hasAccessTo('lhchat','allowchattabs')) : ?>
            <a class="dropdown-item pl-2" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chat tabs');?>" href="#" onclick="javascript:lhinst.chatTabsOpen()"><i class="material-icons">chat</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chat tabs');?></a>
        <?php endif;?>

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

        $canChangeAlwaysOnline = false;
        if ( $currentUser->hasAccessTo('lhuser','changealwaysonline') ) {
            $canChangeAlwaysOnline = true;
            if ( !isset($UserData) ) {
                $UserData = $currentUser->getUserData(true);
            }
        }

        ?>

        <?php if ($currentUser->hasAccessTo('lhchat','use') ) : ?>

            <a href="#" class="dropdown-item pl-2" onclick="lhinst.disableChatSoundAdmin($(this));event.stopPropagation()" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Enable/Disable sound about new messages from users');?>"><i class="material-icons" ><?php $soundMessageEnabled == 0 ? print 'volume_off' : print 'volume_up'?></i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','New messages');?></a>
            <a href="#" class="dropdown-item pl-2" onclick="lhinst.disableNewChatSoundAdmin($(this));event.stopPropagation()" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Enable/Disable sound about new pending chats');?>"><i class="material-icons" ><?php $soundNewChatEnabled == 0 ? print 'volume_off' : print 'volume_up'?></i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','New chats');?></a>

            <?php if ($canChangeVisibilityMode == true) : ?>
                <a href="#" class="dropdown-item pl-2" ng-click="lhc.changeVisibility($event)"><i id="vi-in-user" class="material-icons ng-cloak" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my visibility to visible/invisible');?>" >{{lhc.hideInvisible == true ? 'visibility_off' : 'visibility'}}</i>{{lhc.hideInvisible == true ? <?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Invisible'),ENT_QUOTES))?> : <?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Visible'),ENT_QUOTES))?>}}</a>
            <?php endif;?>

            <?php if ($canChangeOnlineStatus == true) : ?>
                <a href="#" class="dropdown-item pl-2" ng-click="lhc.changeOnline($event)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my status to online/offline');?>"><i id="online-offline-user" class="material-icons ng-cloak" >{{lhc.hideOnline == true ? 'flash_off' : 'flash_on'}}</i>{{lhc.hideOnline == true ? <?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Offline'),ENT_QUOTES))?> : <?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Online'),ENT_QUOTES))?>}}</a>
            <?php endif;?>

        <?php if ($canChangeAlwaysOnline == true) : ?>
                <a href="#" class="dropdown-item pl-4" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my persistent status to online');?>" ng-click="lhc.changeAlwaysOnline($event)"><i class="material-icons ng-cloak"  >{{lhc.hideOnline == true ? 'flash_off' : (lhc.alwaysOnline == true ? 'toggle_on' : 'toggle_off')}}</i>{{lhc.alwaysOnline == true ? <?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Always online'),ENT_QUOTES))?> : <?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Based on activity'),ENT_QUOTES))?>}}</a>
        <?php endif; ?>

        <?php endif;?>




    </div>
</li>
<?php unset($currentUser);unset($UserData);endif; ?>
