<?php
$currentUser = erLhcoreClassUser::instance();
if ($currentUser->isLogged()) :
$UserData = $currentUser->getUserData(true);

// Just logout if force to logout
if ($UserData->force_logout == 1) {
    $UserData->force_logout = 0;
    $UserData->updateThis(['update' => ['force_logout']]);
    $currentUser->logout();
}

?>
<li class="nav-item dropleft">
    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo htmlspecialchars($UserData->name),' ',htmlspecialchars($UserData->surname)?></a>
    <div class="dropdown-menu" style="min-width: 25rem;" role="menu">

        <div class="row">
            <div class="col-6">
                <div class="pl-2 pt-1 font-weight-bold" ng-non-bindable>
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Hello')?>&nbsp;<?php echo erLhcoreClassDesign::shrt($UserData->name,10,'...',30,ENT_QUOTES)?>!
                </div>
            </div>
            <?php if ($currentUser->hasAccessTo('lhuser','selfedit')) : ?>
                <div class="col-6">
                    <a class="dropdown-item pl-2" href="<?php echo erLhcoreClassDesign::baseurl('user/account')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Account')?>"><i class="material-icons">account_box</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Account')?></a>
                </div>
            <?php endif; ?>
            <div class="col-6">
                <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Toggle between dark and white themes');?>" href="<?php echo erLhcoreClassDesign::baseurl('front/switchdashboard')?>/(action)/mode" class="dropdown-item pl-2"><span class="material-icons">settings_brightness</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Dark/bright');?></a>
            </div>
            <div class="col-6">
                <a class="dropdown-item pl-2" onclick="$(this).attr('href',$(this).attr('href')+'/(csfr)/'+confLH.csrf_token)" href="<?php echo erLhcoreClassDesign::baseurl('user/logout')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Logout');?>"><i class="material-icons">exit_to_app</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Logout');?></a>
            </div>
        </div>


        <hr class="m-0">

        <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_chat_actions_pre.tpl.php'));?>

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
            <div class="row">
                <div class="col-12">
                    <a href="<?php echo erLhcoreClassDesign::baseurl('user/setsetting')?>/auto_uppercase/<?php echo erLhcoreClassModelUserSetting::getSetting('auto_uppercase',1) == 0 ? 1 : 0?>" class="dropdown-item pl-2"><span class="material-icons"><?php erLhcoreClassModelUserSetting::getSetting('auto_uppercase',1) ? print 'check' : print 'remove_done'?></span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Auto uppercase sentences');?></a>
                </div>
                <div class="col-12">
                    <a href="<?php echo erLhcoreClassDesign::baseurl('user/setsetting')?>/no_scroll_bottom/<?php echo erLhcoreClassModelUserSetting::getSetting('no_scroll_bottom',0) == 0 ? 1 : 0?>" class="dropdown-item pl-2"><span class="material-icons"><?php erLhcoreClassModelUserSetting::getSetting('no_scroll_bottom',0) ? print 'check' : print 'remove_done'?></span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Do not scroll to the bottom on chat open');?></a>
                </div>
                <div class="col-12">
                    <a href="<?php echo erLhcoreClassDesign::baseurl('user/setsetting')?>/auto_preload/<?php echo erLhcoreClassModelUserSetting::getSetting('auto_preload',0) == 0 ? 1 : 0?>" class="dropdown-item pl-2"><span class="material-icons"><?php erLhcoreClassModelUserSetting::getSetting('auto_preload',0) ? print 'check' : print 'remove_done'?></span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Auto preload previous visitor chat messages');?></a>
                </div>
                <div class="col-12">
                    <a href="<?php echo erLhcoreClassDesign::baseurl('user/setsetting')?>/scroll_load/<?php echo erLhcoreClassModelUserSetting::getSetting('scroll_load',1) == 0 ? 1 : 0?>" class="dropdown-item pl-2"><span class="material-icons"><?php erLhcoreClassModelUserSetting::getSetting('scroll_load',1) ? print 'check' : print 'remove_done'?></span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Load previous message on scroll');?></a>
                </div>
                <div class="col-6">
                    <a href="#" class="dropdown-item pl-2" onclick="lhinst.disableChatSoundAdmin($(this));event.stopPropagation()" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Enable/Disable sound about new messages from users');?>"><i class="material-icons" ><?php $soundMessageEnabled == 0 ? print 'volume_off' : print 'volume_up'?></i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','New messages');?></a>
                </div>
                <div class="col-6">
                    <a href="#" class="dropdown-item pl-2" onclick="lhinst.disableNewChatSoundAdmin($(this));event.stopPropagation()" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Enable/Disable sound about new pending chats');?>"><i class="material-icons" ><?php $soundNewChatEnabled == 0 ? print 'volume_off' : print 'volume_up'?></i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','New chats');?></a>
                </div>
            </div>

            <hr class="m-0">

            <?php if ($canChangeVisibilityMode || $canChangeOnlineStatus || $canChangeAlwaysOnline) : ?>
            <div class="row">
                <?php if ($canChangeOnlineStatus == true) : ?>
                <div class="col-6">
                    <a href="#" class="dropdown-item pl-2" ng-click="lhc.changeOnline($event)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my status to online/offline');?>"><i id="online-offline-user" class="material-icons ng-cloak" >{{lhc.hideOnline == true ? 'flash_off' : 'flash_on'}}</i>{{lhc.hideOnline == true ? <?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Offline'),ENT_QUOTES))?> : <?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Online'),ENT_QUOTES))?>}}</a>
                </div>
                <?php endif;?>
                <?php if ($canChangeAlwaysOnline == true) : ?>
                <div class="col-6">
                    <a href="#" class="dropdown-item pl-2" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my persistent status to online');?>" ng-click="lhc.changeAlwaysOnline($event)"><i class="material-icons ng-cloak"  >{{lhc.hideOnline == true ? 'flash_off' : (lhc.alwaysOnline == true ? 'toggle_on' : 'toggle_off')}}</i>{{lhc.alwaysOnline == true ? <?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Always online'),ENT_QUOTES))?> : <?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Based on activity'),ENT_QUOTES))?>}}</a>
                </div>
                <?php endif; ?>
                <?php if ($canChangeVisibilityMode == true) : ?>
                    <div class="col-12">
                        <a href="#" class="dropdown-item pl-2" ng-click="lhc.changeVisibility($event)"><i id="vi-in-user" class="material-icons ng-cloak" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my visibility to visible/invisible');?>" >{{lhc.hideInvisible == true ? 'visibility_off' : 'visibility'}}</i>{{lhc.hideInvisible == true ? <?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Invisible'),ENT_QUOTES))?> : <?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Visible'),ENT_QUOTES))?>}}</a>
                    </div>
                <?php endif;?>
            </div>
                <hr class="m-0">
            <?php endif; ?>


        <?php endif;?>

        <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_chat_actions_post_multiinclude.tpl.php'));?>


    </div>
</li>
<?php unset($currentUser);unset($UserData);endif; ?>
