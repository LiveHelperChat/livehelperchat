<?php $currentUser = erLhcoreClassUser::instance(); ?>
<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom p-0 pl-1 top-menu-bar-lhc">
    <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head_logo_back_office.tpl.php'));?>

    <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head_side_control.tpl.php'));?>

    <div ng-cloak class="version-updated float-left" ng-if="lhc.lhcPendingRefresh == true || lhc.lhcConnectivityProblem == true">
        <div ng-if="lhc.lhcPendingRefresh == true"><i class="material-icons">update</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','This window will be automatically refreshed in {{lhc.lhcVersionCounter}} seconds due to a version update.');?></div>
        <div ng-if="lhc.lhcConnectivityProblem == true">You have weak internet connection or the server has problems. Try to refresh the  page. Error code {{lhc.lhcConnectivityProblemExplain}}</div>
    </div>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav ml-auto">
            <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_multiinclude.tpl.php'));?>

            <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/user_box.tpl.php'));?>
            <li class="li-icon nav-item">
                <a class="nav-link" ng-click="lhc.toggleList('lmtoggler')" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Expand or collapse right menu')?>">
                    <span class="navbar-toggler-icon"></span>
                </a>
            </li>
        </ul>
    </div>

</nav>