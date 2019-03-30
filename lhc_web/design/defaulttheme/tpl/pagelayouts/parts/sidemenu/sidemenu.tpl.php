<?php $currentUser = erLhcoreClassUser::instance(); ?>
<div id="sidebar-wrapper" ng-cloak>
	<div class="navbar-light bg-light sidebar" role="navigation">
		<div class="sidebar-nav navbar-collapse">

            <div ng-cloak class="version-updated float-right" ng-if="lhc.lhcPendingRefresh == true"><i class="material-icons">update</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','This window will be automatically refreshed in {{lhc.lhcVersionCounter}} seconds due to a version update.');?></div>

            <button class="btn btn-outline-light mr-auto" type="button" ng-click="lhc.toggleList('lmtoggle')" title="Expand or collapse left menu" aria-expanded="true" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <ul class="nav" id="side-menu">

                <li class="nav-item"><a class="nav-link" href="<?php echo erLhcoreClassDesign::baseurl('/')?>"><?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head_logo_back_office.tpl.php'));?><span class="nav-link-text"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Dashboard')?></span></a></li>

                <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/user_box.tpl.php'));?>
                <?php $currentUser = erLhcoreClassUser::instance(); ?>
                <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/sidemenu/chat/chat.tpl.php'));?>
                <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/sidemenu/settings/settings.tpl.php'));?>
    	        <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_modules_container.tpl.php.tpl.php'));?>	
    	        <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/sidemenu/menu_item_multiinclude.tpl.php'));?>

                <?php if ($canUseChat == true && (!isset($Result['hide_right_column']) || $Result['hide_right_column'] == false)) :?>
                <li class="nav-item">
                    <a class="nav-link" href="#" ng-click="lhc.toggleList('lmtoggler')" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Expand or collapse right menu')?>"><i class="material-icons">menu</i><span class="nav-link-text">Hide show right column</span></a>
                </li>
                <?php endif; ?>
            </ul>



		</div>
	</div>
	
	<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/sidemenu/after_sidemnu_multiinclude.tpl.php'));?>
</div>

