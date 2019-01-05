<?php $currentUser = erLhcoreClassUser::instance(); ?>
<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom p-0 pl-1">
    <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head_logo_back_office.tpl.php'));?>

    <button class="btn btn-outline-light" type="button" ng-click="lhc.toggleList('lmtoggle')" title="Expand or collapse left menu" aria-expanded="true" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div ng-cloak class="version-updated float-left" ng-if="lhc.lhcPendingRefresh == true"><i class="material-icons">update</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','This window will be automatically refreshed in {{lhc.lhcVersionCounter}} seconds due to a version update.');?></div>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav ml-auto">
            <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_chat_actions_pre.tpl.php'));?>
            <?php if ($parts_top_menu_chat_actions_enabled == true && $currentUser->hasAccessTo('lhchat','allowchattabs')) : ?>
                <li class="li-icon nav-item"><a class="nav-link" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chat tabs');?>" href="#" onclick="javascript:lhinst.chatTabsOpen()"><i class="material-icons">chat</i></a></li>
            <?php endif;?>

            <?php if ($currentUser->hasAccessTo('lhsystem','use')) : ?>
                <li class="li-icon nav-item"><a class="nav-link" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Configuration');?>" href="<?php echo erLhcoreClassDesign::baseurl('system/configuration')?>"><i class="material-icons">settings_applications</i></a></li>
            <?php endif; ?>

            <?php $hideULSetting = true;?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/user_settings.tpl.php'));?>

            <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/user_box.tpl.php'));?>

        </ul>
    </div>
</nav>