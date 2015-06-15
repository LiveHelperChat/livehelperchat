<!DOCTYPE html>

<html lang="<?php echo erConfigClassLhConfig::getInstance()->getDirLanguage('content_language')?>" dir="<?php echo erConfigClassLhConfig::getInstance()->getDirLanguage('dir_language')?>" ng-app="lhcApp">
	<head>
		<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head.tpl.php'));?>
	</head>
<body ng-controller="LiveHelperChatCtrl as lhc">

<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu.tpl.php'));?>

<div class="container-fluid">

<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/path.tpl.php'));?>

<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/can_use_chat.tpl.php'));?>

<div class="row">

    <div class="col-sm-<?php $canUseChat == true && (!isset($Result['hide_right_column']) || $Result['hide_right_column'] == false) ? print '8' : print '12'; ?> col-md-<?php $canUseChat == true && (!isset($Result['hide_right_column']) || $Result['hide_right_column'] == false) ? print '9' : print '12'; ?>">
    	<?php echo $Result['content']; ?>
    </div>
    
    <?php if ($canUseChat == true && (!isset($Result['hide_right_column']) || $Result['hide_right_column'] == false)) :    
    $pendingTabEnabled = (int)erLhcoreClassModelUserSetting::getSetting('enable_pending_list',1);
    $activeTabEnabled = (int)erLhcoreClassModelUserSetting::getSetting('enable_active_list',1);
    $closedTabEnabled = (int)erLhcoreClassModelUserSetting::getSetting('enable_close_list',0);
    $unreadTabEnabled = (int)erLhcoreClassModelUserSetting::getSetting('enable_unread_list',1); ?>
    <div class="columns col-sm-4 col-md-3" id="right-column-page" ng-cloak>
    	
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/transfer_panel_container_pre.tpl.php'));?>
        
        <?php if ($transfer_panel_container_pre_enabled == true) : ?>
        	<?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/transfer_panel_container.tpl.php'));?>
        <?php endif;?>
        
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/right_panel_container.tpl.php'));?>
    </div>
    <?php endif; ?>

</div>

<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_footer.tpl.php'));?>

</div>

<?php if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
		$debug = ezcDebug::getInstance();
		echo $debug->generateOutput();
} ?>

</body>
</html>