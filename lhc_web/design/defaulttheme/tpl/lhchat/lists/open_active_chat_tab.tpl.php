<div class="navbar-right mr-0" ng-init="lhc.getToggleWidget('track_open_chats')">
    <ul class="nav navbar-nav">  
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="material-icons mr-0">settings_applications</i></a>
            <ul class="dropdown-menu" role="menu">
                <li><a href="#" ng-click="lhc.appendActiveChats()" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Open last 10 my active chats')?>"><i class="material-icons chat-active">chat</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Open my active chats'); ?></a></li>
                <li><a href="#" ng-click="lhc.toggleWidget('track_open_chats')" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Last 10 your active chats will be always visible')?>"><i class="material-icons" ng-class="{'chat-active': lhc.toggleWidgetData['track_open_chats'] === true, 'chat-closed': lhc.toggleWidgetData['track_open_chats'] !== true}">done</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Keep my active chats'); ?></a></li>
            </ul>
        </li>
    </ul>
</div>