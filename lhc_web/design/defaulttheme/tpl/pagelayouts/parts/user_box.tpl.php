<?php
$currentUser = erLhcoreClassUser::instance();
if ($currentUser->isLogged()) :
$UserData = $currentUser->getUserData(true); ?>
<li class="nav-item dropleft">
    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo htmlspecialchars($UserData->name),' ',htmlspecialchars($UserData->surname)?></a>
    <div class="dropdown-menu" role="menu">
        <a class="dropdown-item" href="<?php echo erLhcoreClassDesign::baseurl('user/account')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Account')?>"><i class="material-icons">account_box</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Account')?></a>
        <a class="dropdown-item" href="<?php echo erLhcoreClassDesign::baseurl('user/logout')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Logout');?>"><i class="material-icons">exit_to_app</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Logout');?></a>
        <?php if ($currentUser->hasAccessTo('lhsystem','use')) : ?>
            <a class="dropdown-item" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Configuration');?>" href="<?php echo erLhcoreClassDesign::baseurl('system/configuration')?>"><i class="material-icons">settings_applications</i></a>
        <?php endif; ?>

        <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_chat_actions_pre.tpl.php'));?>

        <?php if ($parts_top_menu_chat_actions_enabled == true && $currentUser->hasAccessTo('lhchat','allowchattabs')) : ?>
            <a class="dropdown-item" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chat tabs');?>" href="#" onclick="javascript:lhinst.chatTabsOpen()"><i class="material-icons">chat</i></a>
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

        ?>

        <?php if ($currentUser->hasAccessTo('lhchat','use') ) : ?>

            <a href="#" class="dropdown-item"><i class="material-icons" onclick="return lhinst.disableChatSoundAdmin($(this))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Enable/Disable sound about new messages from users');?>"><?php $soundMessageEnabled == 0 ? print 'volume_off' : print 'volume_up'?></i></a>
            <a href="#" class="dropdown-item"><i class="material-icons" onclick="return lhinst.disableNewChatSoundAdmin($(this))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Enable/Disable sound about new pending chats');?>"><?php $soundNewChatEnabled == 0 ? print 'volume_off' : print 'volume_up'?></i></a>

            <?php if ($canChangeVisibilityMode == true) : ?>
                <a href="#" class="dropdown-item"><i id="vi-in-user" class="material-icons ng-cloak" ng-click="lhc.changeVisibility()" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my visibility to visible/invisible');?>" >{{lhc.hideInvisible == true ? 'visibility_off' : 'visibility'}}</i></a>
            <?php endif;?>

            <?php if ($canChangeOnlineStatus == true) : ?>
                <a href="#" class="dropdown-item"><i id="online-offline-user" class="material-icons ng-cloak" ng-click="lhc.changeOnline()" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my status to online/offline');?>" >{{lhc.hideOnline == true ? 'flash_off' : 'flash_on'}}</i></a>
            <?php endif;?>

        <?php endif;?>




    </div>
</li>
<?php unset($currentUser);unset($UserData);endif; ?>
