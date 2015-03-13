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
        
        <div role="tabpanel" ng-show="transfer_dep_chats.list.length > 0 || transfer_chats.list.length > 0">
        	<!-- Nav tabs -->
        	<ul class="nav nav-pills" role="tablist">
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

            <div class="panel panel-default panel-lhc" ng-show="pending_chats.list.length > 0 || active_chats.list.length > 0 || unread_chats.list.length > 0 || closed_chats.list.length > 0">
            <?php if ($pendingTabEnabled == true) : ?>
            <div class="panel-heading" ng-if="pending_chats.list.length > 0"><a href="<?php echo erLhcoreClassDesign::baseurl('chat/pendingchats')?>"><i class="icon-chat chat-pending"></i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Pending chats');?></a></div>
            <div class="panel-body" id="right-pending-chats" ng-if="pending_chats.list.length > 0">
        			<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_pending_list.tpl.php'));?>
            </div>
            <?php endif;?>
        
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/right_panel_post_pending_multiinclude.tpl.php'));?>
        
        	<?php if ($activeTabEnabled == true) : ?> 
            <div class="panel-heading" ng-if="active_chats.list.length > 0"><a href="<?php echo erLhcoreClassDesign::baseurl('chat/activechats')?>"><i class="icon-chat chat-active"></i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Active chats');?></a></div>
            <div class="panel-body"  id="right-active-chats" ng-show="active_chats.list.length > 0">
        			<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_active_list.tpl.php'));?>
            </div>                     
        	<?php endif;?>
        	
        	<?php if ($unreadTabEnabled == true) : ?>			
            <div class="panel-heading" ng-if="unread_chats.list.length > 0"><a href="<?php echo erLhcoreClassDesign::baseurl('chat/unreadchats')?>"><i class="icon-comment chat-unread"></i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Unread messages');?></a></div>
            <div class="panel-body" ng-if="unread_chats.list.length > 0" id="right-unread-chats">
    			<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_unread_list.tpl.php'));?>
            </div>                        
			<?php endif;?>
			
			<?php if ($closedTabEnabled == true) : ?>        	
            <div class="panel-heading" ng-if="closed_chats.list.length > 0"><a href="<?php echo erLhcoreClassDesign::baseurl('chat/closedchats')?>"><i class="icon-cancel-circled chat-closed"></i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Closed chats');?></a></div>
            <div class="panel-body" id="right-closed-chats" ng-if="closed_chats.list.length > 0">
        			<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_closed_list.tpl.php'));?>  
            </div>            
        	<?php endif;?>        			
        	</div> 
        	  
        	
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