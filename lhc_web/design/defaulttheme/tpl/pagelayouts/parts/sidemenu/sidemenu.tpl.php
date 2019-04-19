<?php $currentUser = erLhcoreClassUser::instance(); ?>
<div id="sidebar-wrapper" ng-cloak>
	<div class="navbar-light bg-light sidebar" role="navigation">
		<div class="sidebar-nav navbar-collapse">

            <div ng-cloak class="version-updated float-right" ng-if="lhc.lhcPendingRefresh == true"><i class="material-icons">update</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','This window will be automatically refreshed in {{lhc.lhcVersionCounter}} seconds due to a version update.');?></div>

            <div class="row border-bottom">

            <div class="col-12 col-md-6 quick-options-toggler text-center text-md-left">
                <button class="btn mr-auto pr-0 pl-1" type="button" ng-click="lhc.toggleList('lmtoggle')" title="Expand or collapse left menu" aria-expanded="true" aria-label="Toggle navigation">
                    <i class="material-icons d-none d-md-block fs24">{{lmtoggle ? '&#xf142' : '&#xf141'}}</i>
                    <i class="material-icons d-block d-md-none fs24">{{lmtoggle ? '&#xf141' : '&#xf142'}}</i>
                </button>
                <a ng-click="lhc.activateDashboard()" class="mr-auto"><i class="material-icons">&#xf56e;</i></a>
            </div>


            <?php

            $canChangeOnlineStatus = false;

            $currentUser = erLhcoreClassUser::instance();
            if ( $currentUser->hasAccessTo('lhuser','changeonlinestatus') ) {
                $canChangeOnlineStatus = true;
            }

            $canChangeVisibilityMode = false;
            if ( $currentUser->hasAccessTo('lhuser','changevisibility') ) {
                $canChangeVisibilityMode = true;
            }
            ?>

            <?php if ($currentUser->hasAccessTo('lhchat','use') ) : ?>
            <div class="col-12 col-md-6 text-center text-md-right pb-1 pt-1 quick-options">
                <?php if ($canChangeVisibilityMode == true) : ?>
                    <a href="#" class="pl-1 pr-1"><i id="vi-in-user" class="material-icons ng-cloak" ng-click="lhc.changeVisibility()" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my visibility to visible/invisible');?>" >{{lhc.hideInvisible == true ? '&#xf209' : '&#xf208'}};</i>
                    </a>
                <?php endif;?>

                <?php if ($canChangeOnlineStatus == true) : ?>
                    <a href="#" class="pl-1 pr-1"><i id="online-offline-user" class="material-icons ng-cloak" ng-click="lhc.changeOnline()" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/user_settings','Change my status to online/offline');?>" >{{lhc.hideOnline == true ? '&#xf243' : '&#xf241'}}</i></a>
                <?php endif;?>
            </div>
            <?php endif;?>

            </div>


            <ul class="nav" id="side-menu">

                <li class="nav-item"><a class="nav-link" href="<?php echo erLhcoreClassDesign::baseurl('/')?>"><?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head_logo_back_office.tpl.php'));?><span class="nav-link-text"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Dashboard')?></span></a></li>

                <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/user_box.tpl.php'));?>
                <?php $currentUser = erLhcoreClassUser::instance(); ?>
                <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/sidemenu/chat/chat.tpl.php'));?>
                <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/sidemenu/settings/settings.tpl.php'));?>
    	        <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_modules_container.tpl.php.tpl.php'));?>	
    	        <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/sidemenu/menu_item_multiinclude.tpl.php'));?>

                <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','use') == true && (!isset($Result['hide_right_column']) || $Result['hide_right_column'] == false)) :?>
                <li class="nav-item">
                    <a class="nav-link" href="#" ng-click="lhc.toggleList('lmtoggler')" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Expand or collapse right menu')?>"><i class="material-icons">&#xf1d9;</i><span class="nav-link-text">Hide show right column</span></a>
                </li>
                <?php endif; ?>
            </ul>



		</div>
	</div>
	
	<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/sidemenu/after_sidemnu_multiinclude.tpl.php'));?>
</div>

