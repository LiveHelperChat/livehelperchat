<?php

$dashboardOrder = json_decode(erLhcoreClassModelUserSetting::getSetting('dwo',''),true);

if ($dashboardOrder === null) {
	if ($dashboardOrder == '') {
		$dashboardOrder = json_decode(erLhcoreClassModelChatConfig::fetch('dashboard_order')->current_value,true);
	}
}

$columnsTotal = count($dashboardOrder);
$columnSize = 12 / $columnsTotal;

?>
<div class="row" id="dashboard-body" ng-init='lhc.setUpListNames(["actived","closedd","unreadd","pendingd","operatord","departmentd"])'>
     <a class="dashboard-configuration" onclick="return lhc.revealModal({'url':WWW_DIR_JAVASCRIPT +'chat/dashboardwidgets'})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Configure dashboard')?>"><i class="material-icons mr-0">&#xE871;</i></a>
     <?php foreach ($dashboardOrder as $widgets) : ?>
        <div class="col-md-<?php echo $columnSize+2?> col-lg-<?php echo $columnSize?> sortable-column-dashboard">
            <?php foreach ($widgets as $wiget) : ?>
                <?php if ($wiget == 'online_operators') : ?>
                 
                     <?php if ($canListOnlineUsers == true || $canListOnlineUsersAll == true) : ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/online_operators.tpl.php'));?>
                     <?php endif;?>
                 
                <?php elseif ($wiget == 'active_chats') : ?>
                
                     <?php if ($activeTabEnabled == true && $online_chat_enabled_pre == true) : ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/active_chats.tpl.php'));?>
                     <?php endif;?>
                     
                <?php elseif ($wiget == 'online_visitors') : ?>
                
                     <?php if ($online_visitors_enabled_pre == true && $currentUser->hasAccessTo('lhchat', 'use_onlineusers') == true) : ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/online_visitors.tpl.php'));?>
                     <?php endif;?>
                    
                <?php elseif ($wiget == 'departments_stats') : ?>
                
                    <?php if ($online_chat_enabled_pre == true && $canseedepartmentstats == true) : ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/departments_stats.tpl.php'));?>
                    <?php endif;?>
                    
                <?php elseif ($wiget == 'pending_chats') : ?>
                
                    <?php if ($pendingTabEnabled == true && $online_chat_enabled_pre == true) : ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/pending_chats.tpl.php'));?>
                    <?php endif;?>
                    
                <?php elseif ($wiget == 'unread_chats') : ?>
                
                    <?php if ($unreadTabEnabled == true && $online_chat_enabled_pre == true) : ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/unread_chats.tpl.php'));?>
                    <?php endif;?>
                    
                <?php elseif ($wiget == 'transfered_chats') : ?>
                
                    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/transfer_panel_container_pre.tpl.php'));?>
            
                    <?php if ($transfer_panel_container_pre_enabled == true) : ?>
                            <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/transfered_chats.tpl.php'));?>
                    <?php endif;?>
                    
                <?php elseif ($wiget == 'closed_chats') : ?>
                
                    <?php if ($online_chat_enabled_pre == true && $closedTabEnabled == true) : ?>                
                        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/closed_chats.tpl.php'));?>
                    <?php endif;?>
                    
                <?php elseif ($wiget == 'my_chats') : ?>  
                  
                    <?php if ($mchatsTabEnabled == true) : ?>             
                        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/my_chats.tpl.php'));?>
                    <?php endif;?>
                    
                <?php else : ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/extension_panel_multiinclude.tpl.php'));?>
                <?php endif;?>
            <?php endforeach;?>           
            
        </div>
     <?php endforeach;?>
</div>
<?php $popoverInitialized = true; ?>
