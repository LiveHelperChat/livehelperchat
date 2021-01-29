<nav class="float-right" ng-init="lhc.getToggleWidget('track_open_chats');lhc.getToggleWidget('group_offline_chats')">
    <ul class="nav">
        <li class="nav-item dropleft">
            <a class="nav-link dropdown-toggle text-secondary" id="menu-chat-options" data-toggle="dropdown" role="button" aria-expanded="false"><i class="material-icons mr-0">settings_applications</i></a>
            <div class="dropdown-menu" role="menu">
                <a class="dropdown-item" href="#" ng-click="lhc.appendActiveChats()" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Open last 10 my active chats')?>"><i class="material-icons chat-active">chat</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Open my active chats'); ?></a>
                <a class="dropdown-item" href="#" ng-click="lhc.toggleWidget('track_open_chats')" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Last 10 your active chats will be always visible')?>"><i class="material-icons" ng-class="{'chat-active': lhc.toggleWidgetData['track_open_chats'] === true, 'chat-closed': lhc.toggleWidgetData['track_open_chats'] !== true}">done</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Keep my active chats'); ?></a>
                <a class="dropdown-item" href="#" ng-click="lhc.toggleWidget('group_offline_chats')" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Hide nicknames for offline chats')?>"><i class="material-icons" id="group-chats-status" ng-class="{'chat-active': lhc.toggleWidgetData['group_offline_chats'] === true, 'chat-closed': lhc.toggleWidgetData['group_offline_chats'] !== true}">done</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Hide nicknames for offline chats'); ?></a>
                <a class="dropdown-item" href="<?php echo erLhcoreClassDesign::baseurl('front/switchdashboard')?>" >
                        <i class="material-icons">home</i>
                        <?php if ((int)erLhcoreClassModelUserSetting::getSetting('new_dashboard',1) == 1) : ?>
                            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Old dashboard'); ?>
                        <?php else : ?>
                            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'New dashboard'); ?>
                        <?php endif; ?>
                </a>
                <?php if ((int)erLhcoreClassModelUserSetting::getSetting('new_dashboard',1) == 1) : ?>
                    <a class="dropdown-item" href="<?php echo erLhcoreClassDesign::baseurl('front/switchdashboard')?>/(action)/tabs"><i class="material-icons">chat</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Hide/Show chat tabs'); ?></a>
                    <a class="dropdown-item" href="<?php echo erLhcoreClassDesign::baseurl('front/switchdashboard')?>/(action)/left_list"><i class="material-icons">widgets</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Tabs/List in left column'); ?></a>
                <?php endif; ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/open_active_chat_tab_multiinclude.tpl.php'));?>
                <div class="dropdown-item">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" ng-model="lhc.chat_to_open" ng-keyup="$event.keyCode == 13 ? lhc.startChatByID(lhc.chat_to_open) : ''" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Chat ID to open')?>" aria-describedby="inputGroupPrepend" required>
                        <div class="input-group-append">
                            <button class="btn btn-secondary" ng-click="lhc.startChatByID(lhc.chat_to_open)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Open a chat')?>" type="button"><i class="material-icons mr-0">chat</i></button>
                        </div>
                    </div>
                </div>
            </div>
        </li>
    </ul>
</nav>