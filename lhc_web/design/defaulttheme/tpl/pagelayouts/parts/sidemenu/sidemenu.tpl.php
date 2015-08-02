<?php $currentUser = erLhcoreClassUser::instance(); ?>
<div id="sidebar-wrapper" ng-cloak>
	<div class="navbar-default sidebar" role="navigation">
		<div class="sidebar-nav navbar-collapse">
			<ul class="nav" id="side-menu">
				<li><a href="<?php echo erLhcoreClassDesign::baseurl('/')?>"><i class="icon-home"></i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Dashboard')?></a></li>
                <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/sidemenu/chat/chat.tpl.php'));?>
                <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/sidemenu/settings/settings.tpl.php'));?>
    	        <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_modules_container.tpl.php.tpl.php'));?>	
            </ul>
		</div>
	</div>
</div>
