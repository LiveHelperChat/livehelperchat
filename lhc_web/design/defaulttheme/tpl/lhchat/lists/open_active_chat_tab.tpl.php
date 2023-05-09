<nav class="float-end" ng-init="lhc.getToggleWidget('track_open_chats');lhc.getToggleWidget('group_offline_chats')">
    <ul class="nav">
        <li class="nav-item dropend">
            <a class="nav-link dropdown-toggle text-secondary ps-2 pe-2" id="menu-chat-options" data-bs-toggle="dropdown" role="button" aria-expanded="false"><i class="material-icons me-0">settings_applications</i></a>
            <div class="dropdown-menu" role="menu">
                <a class="dropdown-item" href="#" ng-click="lhc.appendActiveChats()" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Open last 10 my active chats')?>"><i class="material-icons chat-active">chat</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Open my active chats'); ?></a>
                <a class="dropdown-item" href="#" ng-click="lhc.toggleWidget('track_open_chats')" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Last 10 your active chats will be always visible')?>"><i class="material-icons" ng-class="{'chat-active': lhc.toggleWidgetData['track_open_chats'] === true, 'chat-closed': lhc.toggleWidgetData['track_open_chats'] !== true}">done</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Keep my active chats'); ?></a>
                <a class="dropdown-item" href="#" ng-click="lhc.toggleWidget('group_offline_chats')" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Hide nicknames for offline chats')?>"><i class="material-icons" id="group-chats-status" ng-class="{'chat-active': lhc.toggleWidgetData['group_offline_chats'] === true, 'chat-closed': lhc.toggleWidgetData['group_offline_chats'] !== true}">done</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Hide nicknames for offline chats'); ?></a>
                <a class="dropdown-item csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('front/settings')?>/(action)/reset" ><i class="material-icons">search_off</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Reset widget filters'); ?></a>

                <?php if ($currentUser->hasAccessTo('lhfront','switch_dashboard')) : ?>
                <a class="dropdown-item csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('front/switchdashboard')?>" >
                        <i class="material-icons">home</i>
                        <?php if ((int)erLhcoreClassModelUserSetting::getSetting('new_dashboard',1) == 1) : ?>
                            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Old dashboard'); ?>
                        <?php else : ?>
                            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'New dashboard'); ?>
                        <?php endif; ?>
                </a>
                <?php endif; ?>

                <?php if ((int)erLhcoreClassModelUserSetting::getSetting('new_dashboard',1) == 1) : ?>
                    <a id="chats-order-mode" data-mode="<?php if ((int)erLhcoreClassModelUserSetting::getSetting('static_order', 0) == 1) : ?>static<?php else : ?>dynamic<?php endif; ?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Click to switch to static/dynamic')?>" class="dropdown-item csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('front/switchdashboard')?>/(action)/static_order"><i class="material-icons">sort</i>
                        <?php if ((int)erLhcoreClassModelUserSetting::getSetting('static_order', 0) == 1) : ?>
                            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'In static chats order mode'); ?></a>
                        <?php else : ?>
                            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'In dynamic chats order mode'); ?></a>
                        <?php endif; ?>
                <?php endif; ?>

                <?php if ((int)erLhcoreClassModelUserSetting::getSetting('new_dashboard',1) == 1) : ?>
                <a ng-click="<?php if ((int)erLhcoreClassModelUserSetting::getSetting('column_chats', 0) == 1) : ?>lhc.removeLocalSetting('lhc_rch')<?php else : ?>lhc.storeLocalSetting('lhc_rch',1)<?php endif;?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Click to switch modes')?>" class="dropdown-item csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('front/switchdashboard')?>/(action)/column_chats">
                    <?php if ((int)erLhcoreClassModelUserSetting::getSetting('column_chats', 0) == 1) : ?>
                        <i class="material-icons">view_column</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Multiple chats view'); ?></a>
                    <?php else : ?>
                        <i class="material-icons">view_sidebar</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Single chat view'); ?></a>
                    <?php endif; ?>
                <?php endif; ?>

                <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/options/new_dashboard_options.tpl.php'));?>

                <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/open_active_chat_tab_multiinclude.tpl.php'));?>
                <div class="dropdown-item">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" ng-model="lhc.chat_to_open" ng-keyup="$event.keyCode == 13 ? lhc.startChatByID(lhc.chat_to_open) : ''" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Chat ID to open')?>" aria-describedby="inputGroupPrepend" required>
                        <button class="btn btn-secondary" ng-click="lhc.startChatByID(lhc.chat_to_open)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Open a chat')?>" type="button"><i class="material-icons me-0">chat</i></button>
                    </div>
                </div>
            </div>
        </li>
    </ul>
</nav>