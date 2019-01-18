<nav class="float-right" ng-init="lhc.getToggleWidget('track_open_chats');lhc.getToggleWidget('group_offline_chats')">
    <ul class="nav">
        <li class="nav-item dropleft">
            <a class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="material-icons mr-0">settings_applications</i></a>
            <ul class="dropdown-menu" role="menu">
                <li class="dropdown-item"><a href="#" ng-click="lhc.appendActiveChats()" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Open last 10 my active chats')?>"><i class="material-icons chat-active">chat</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Open my active chats'); ?></a></li>
                <li class="dropdown-item"><a href="#" ng-click="lhc.toggleWidget('track_open_chats')" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Last 10 your active chats will be always visible')?>"><i class="material-icons" ng-class="{'chat-active': lhc.toggleWidgetData['track_open_chats'] === true, 'chat-closed': lhc.toggleWidgetData['track_open_chats'] !== true}">done</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Keep my active chats'); ?></a></li>
                <li class="dropdown-item"><a href="#" ng-click="lhc.toggleWidget('group_offline_chats')" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Hide nicknames for offline chats')?>"><i class="material-icons" id="group-chats-status" ng-class="{'chat-active': lhc.toggleWidgetData['group_offline_chats'] === true, 'chat-closed': lhc.toggleWidgetData['group_offline_chats'] !== true}">done</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Hide nicknames for offline chats'); ?></a></li>
            </ul>
        </li>
    </ul>
</nav>