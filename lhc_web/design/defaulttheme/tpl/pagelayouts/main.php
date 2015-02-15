<!DOCTYPE html>

<html lang="<?php echo erConfigClassLhConfig::getInstance()->getDirLanguage('content_language')?>" dir="<?php echo erConfigClassLhConfig::getInstance()->getDirLanguage('dir_language')?>" ng-app="lhcApp">
	<head>
		<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head.tpl.php'));?>
	</head>
<body ng-controller="LiveHelperChatCtrl as lhc">



<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu.tpl.php'));?>

<div class="container-fluid">


<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/path.tpl.php'));?>
<?php $canUseChat = erLhcoreClassUser::instance()->hasAccessTo('lhchat','use'); ?>

<div class="row">

    <div class="col-sm-<?php $canUseChat == true ? print '8' : print '12'; ?> col-md-<?php $canUseChat == true ? print '9' : print '12'; ?>">
    	<?php echo $Result['content']; ?>
    </div>

    <?php if ($canUseChat == true) :    
    $pendingTabEnabled = (int)erLhcoreClassModelUserSetting::getSetting('enable_pending_list',1);
    $activeTabEnabled = (int)erLhcoreClassModelUserSetting::getSetting('enable_active_list',1);
    $closedTabEnabled = (int)erLhcoreClassModelUserSetting::getSetting('enable_close_list',0);
    $unreadTabEnabled = (int)erLhcoreClassModelUserSetting::getSetting('enable_unread_list',1);
    ?>
    <div class="columns col-sm-4 col-md-3" id="right-column-page" ng-cloak>
        
        <div role="tabpanel">
        	<!-- Nav tabs -->
        	<ul class="nav nav-tabs" role="tablist">
        		<li role="presentation" class="active"><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chats transferred to you directly');?>" href="#transferedperson" aria-controls="transferedperson" role="tab" data-toggle="tab"><i class="icon-user"></i><span class="tru-cnt"></span></a></li>
        		<li role="presentation"><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Transferred to your department');?>" href="#transfereddep" aria-controls="transfereddep" role="tab" data-toggle="tab"><i class="icon-users"></i><span class="trd-cnt"></span></a></li>
        	</ul>
        
        	<!-- Tab panes -->
        	<div class="tab-content">
        		<div role="tabpanel" class="tab-pane active" id="transferedperson">
        		    <div id="right-transfer-chats">
			      		<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_transfer_chats.tpl.php'));?>
		            </div>
        		</div>
        		<div role="tabpanel" class="tab-pane" id="transfereddep">
        		    <div id="right-transfer-departments">
			      		<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_transfer_chats_departments.tpl.php'));?>
		            </div>
        		</div>
        	</div>
        </div>

    		<?php if ($pendingTabEnabled == true) : ?>
    		<div ng-show="pending_chats.list.length > 0">
			<h4><a href="<?php echo erLhcoreClassDesign::baseurl('chat/pendingchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Pending chats');?></a></h4>
    		<div id="right-pending-chats">
				<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_pending_list.tpl.php'));?>
            </div>
            <hr>
            </div>        	
        	<?php endif;?>

        	<?php if ($activeTabEnabled == true) : ?>
        	<div ng-show="active_chats.list.length > 0">
			<h4><a href="<?php echo erLhcoreClassDesign::baseurl('chat/activechats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Active chats');?></a></h4>
    		<div id="right-active-chats">
    			<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_active_list.tpl.php'));?>
            </div>
        	<hr>
        	</div>
        	<?php endif;?>

			<?php if ($unreadTabEnabled == true) : ?>
			<div ng-show="unread_chats.list.length > 0">
	        <h4><a href="<?php echo erLhcoreClassDesign::baseurl('chat/unreadchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Unread messages');?></a></h4>
    		<div id="right-unread-chats">
    			<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_unread_list.tpl.php'));?>           		
            </div>
            <hr>
            </div>
			<?php endif;?>

        	<?php if ($closedTabEnabled == true) : ?>
        	<div ng-show="closed_chats.list.length > 0">
	        <h4><a href="<?php echo erLhcoreClassDesign::baseurl('chat/closedchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Closed chats');?></a></h4>
    		<div id="right-closed-chats">        		
        		<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_closed_list.tpl.php'));?>        		
            </div>
            </div>
        	<?php endif;?>
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