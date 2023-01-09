<?php $currentUser = erLhcoreClassUser::instance(); ?>
<div id="sidebar-wrapper" ng-cloak translate="no">
	<div class="navbar-light sidebar" role="navigation">
		<div class="sidebar-nav navbar-collapse">
			<ul class="nav position-relative" id="side-menu">
                <li class="position-absolute me-0 border-0 pt-2" style="right: 0">
                    <a href="#" ng-click="lhc.toggleList('rmtoggle')" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Hide/Show chats toolbar')?>"><span class="material-icons me-0">menu</span></a>
                </li>
				<li class="nav-item">
                    <a class="nav-link" href="<?php echo erLhcoreClassDesign::baseurl('/')?>" onclick="$('#tabs a[href=\'#dashboard\']').tab('show')"><i class="material-icons md-18">home</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Dashboard')?></a>
                </li>
                <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/sidemenu/chat/chat.tpl.php'));?>
                <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/sidemenu/settings/settings.tpl.php'));?>
    	        <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu_modules_container.tpl.php.tpl.php'));?>	
    	        <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/sidemenu/menu_item_multiinclude.tpl.php'));?>	
            </ul>
		</div>
	</div>
	
	<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/sidemenu/after_sidemnu_multiinclude.tpl.php'));?>
</div>

