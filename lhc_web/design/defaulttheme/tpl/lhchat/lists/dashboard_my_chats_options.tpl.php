<nav class="float-right" ng-init="lhc.getToggleWidget('only_user',true);">
    <ul class="nav" >
        <li class="nav-item dropleft" style="line-height:1">
            <a class="nav-link dropdown-toggle pt-1 mt-0 pl-1 pr-1" data-toggle="dropdown" role="button" aria-expanded="false"><i class="material-icons mr-0">settings_applications</i></a>
            <ul class="dropdown-menu" role="menu">
                <li class="dropdown-item fs12"><a href="#" ng-click="lhc.toggleWidget('only_user')" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Show chats only assigned to me')?>"><i class="material-icons" id="group-chats-status" ng-class="{'chat-active': lhc.toggleWidgetData['only_user'] === true, 'chat-closed': lhc.toggleWidgetData['only_user'] !== true}">done</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Show chats only assigned to me'); ?></a></li>
            </ul>
        </li>
    </ul>
</nav>