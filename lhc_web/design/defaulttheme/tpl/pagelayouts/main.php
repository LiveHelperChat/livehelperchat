<!DOCTYPE html>

<html class="h-100" lang="<?php echo erConfigClassLhConfig::getInstance()->getDirLanguage('content_language')?>" dir="<?php echo erConfigClassLhConfig::getInstance()->getDirLanguage('dir_language')?>" ng-app="lhcApp">
	<head>
		<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head.tpl.php'));?>
	</head>
<body ng-controller="LiveHelperChatCtrl as lhc" class="h-100" ng-init="lhc.getToggleWidget('pending_chats_sort',<?php (int)erLhcoreClassModelChatConfig::fetchCache('reverse_pending')->current_value == 1 ? print "'true'" : print "'false'"?>);">
<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_top_content_multiinclude.tpl.php'));?>
<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_head_multiinclude.tpl.php'));?>

<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu.tpl.php'));?>

<div id="wrapper" ng-class="{toggled: lmtoggle, toggledr : lmtoggler}" class="h-100">

<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/sidemenu/sidemenu.tpl.php'));?>

    <div id="page-content-wrapper" class="h-100">

        <div class="row h-100">

            <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/can_use_chat.tpl.php'));?>

            <div id="middle-column-page" class="h-100 col-sm-<?php $canUseChat == true && (!isset($Result['hide_right_column']) || $Result['hide_right_column'] == false) ? print '8' : print '12'; ?> col-md-<?php $canUseChat == true && (!isset($Result['hide_right_column']) || $Result['hide_right_column'] == false) ? print '9' : print '12'; ?>">

                <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/path.tpl.php'));?>

                <?php echo $Result['content']; ?>
            </div>
            
            <?php if ($canUseChat == true && (!isset($Result['hide_right_column']) || $Result['hide_right_column'] == false)) :    
            $pendingTabEnabled = (int)erLhcoreClassModelUserSetting::getSetting('enable_pending_list',1);
            $activeTabEnabled = (int)erLhcoreClassModelUserSetting::getSetting('enable_active_list',1);
            $closedTabEnabled = (int)erLhcoreClassModelUserSetting::getSetting('enable_close_list',0);
            $unreadTabEnabled = (int)erLhcoreClassModelUserSetting::getSetting('enable_unread_list',1); ?>
            <div class="columns col-sm-4 col-md-3 right-column-page-general" id="right-column-page" ng-cloak>
            	
                <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/transfer_panel_container_pre.tpl.php'));?>
                
                <?php if ($transfer_panel_container_pre_enabled == true) : ?>
                	<?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/transfer_panel_container.tpl.php'));?>
                <?php endif;?>
                
                <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/right_panel_container.tpl.php'));?>
            </div>
            <?php endif; ?>
        
        </div>
    
    </div>

</div>


    <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_footer.tpl.php'));?>

<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_bottom_content_multiinclude.tpl.php'));?>

<?php if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
		$debug = ezcDebug::getInstance();
		echo $debug->generateOutput();
} ?>

</body>
</html>