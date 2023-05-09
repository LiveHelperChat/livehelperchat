<?php $currentUser = erLhcoreClassUser::instance(); ?>
<nav class="navbar navbar-expand-lg border-bottom p-0 ps-1 top-menu-bar-lhc" translate="no">
    <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head_logo_back_office.tpl.php'));?>

    <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head_side_control.tpl.php'));?>

    <div ng-cloak class="version-updated float-start" ng-if="lhc.lhcPendingRefresh == true || lhc.lhcConnectivityProblem == true || lhc.inActive == true">
        <div ng-if="lhc.lhcPendingRefresh == true"><i class="material-icons">update</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','This window will be automatically refreshed in {{lhc.lhcVersionCounter}} seconds due to a version update.');?></div>
        <div ng-if="lhc.lhcConnectivityProblem == true"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','You have weak internet connection or the server has problems. Try to refresh the  page. Error code {{lhc.lhcConnectivityProblemExplain}}');?></div>
        <div ng-if="lhc.inActive == true"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','You went offline because of inactivity. Please close other chat windows if you have any');?></div>
    </div>

    <?php if (erLhcoreClassModelUserSetting::getSetting('hide_quick_notifications',0) == 0) : ?>
    <div ng-cloak class="text-muted float-start fs12 abbr-list d-none d-sm-block" ng-if="!(lhc.lhcPendingRefresh == true || lhc.lhcConnectivityProblem == true || lhc.inActive == true) && lhc.last_actions.length > 0">
            <span class="material-icons action-image" ng-click="lhc.last_actions_index = lhc.last_actions_index + 1" ng-if="lhc.last_actions_index < lhc.last_actions.length - 1">
                expand_more
            </span>
            <span ng-if="lhc.last_actions_index > 0" ng-click="lhc.last_actions_index = lhc.last_actions_index - 1" class="material-icons action-image">
                expand_less
            </span>
            <span class="material-icons" ng-if="lhc.last_actions_index > 0">
                hourglass_full
            </span>
            <span ng-if="lhc.last_actions[lhc.last_actions_index].type == 'user_wrote'"><b>{{lhc.last_actions[lhc.last_actions_index].nick}}</b> - <i>{{lhc.last_actions[lhc.last_actions_index].msg}}</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','in chat');?> - {{lhc.last_actions[lhc.last_actions_index].chat_id}}</span>
            <span ng-if="lhc.last_actions[lhc.last_actions_index].type != 'user_wrote' && lhc.last_actions[lhc.last_actions_index].type != 'mac' && lhc.last_actions[lhc.last_actions_index].type != 'mac_history'"><b>{{lhc.last_actions[lhc.last_actions_index].nick}}</b> - <i>{{lhc.last_actions[lhc.last_actions_index].msg}}</i> - {{lhc.last_actions[lhc.last_actions_index].chat_id}}</span>
            <span ng-if="lhc.last_actions[lhc.last_actions_index].type == 'mac'"><b>{{lhc.last_actions[lhc.last_actions_index].nick}}</b> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','active chat was opened');?> - {{lhc.last_actions[lhc.last_actions_index].chat_id}}</span>
            <span ng-if="lhc.last_actions[lhc.last_actions_index].type == 'mac_history'"><b>{{lhc.last_actions[lhc.last_actions_index].nick}}</b> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','previously loaded chat was opened');?> - {{lhc.last_actions[lhc.last_actions_index].chat_id}}</span>
    </div>
    <?php endif; ?>

    <button class="navbar-toggler btn border-0 btn-outline-secondary pb-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="material-icons me-0">menu</span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav ms-auto">

            <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_multiinclude.tpl.php'));?>

            <?php if ($currentUser->hasAccessTo('lhchat','use') && $currentUser->hasAccessTo('lhuser','changeonlinestatus'))  : ?>
                    <li class="list-inline-item nav-item"><a href="#" class="nav-link"><i id="online-offline-user" class="material-icons ng-cloak" ng-click="lhc.changeOnline()" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my status to online/offline');?>" >{{lhc.hideOnline == true ? 'flash_off' : 'flash_on'}}</i></a></li>
            <?php endif; ?>
            
            <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/user_box.tpl.php'));?>

        </ul>
    </div>

</nav>