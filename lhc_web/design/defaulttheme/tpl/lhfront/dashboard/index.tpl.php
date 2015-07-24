<?php
$departmentNames = array();
$departmentList = array();
$departments = erLhcoreClassModelDepartament::getList($departmentParams);

foreach ($departments as $department) {
    $departmentNames[$department->id] = $department->name;
    $departmentList[] = array(
        'id' => $department->id,
        'name' => $department->name
    );
}

$dashboardOrder = explode('|', erLhcoreClassModelChatConfig::fetch('dashboard_order')->current_value);

$columnsTotal = count($dashboardOrder);
$columnSize = 12 / $columnsTotal;

?>
<div class="row" id="dashboard-body" ng-init='lhc.userDepartments=<?php echo json_encode($departmentList,JSON_HEX_APOS)?>;lhc.userDepartmentsNames=<?php echo json_encode($departmentNames,JSON_HEX_APOS)?>;lhc.setUpListNames(["actived","closedd","unreadd","pendingd","operatord","departmentd"])'>
     <?php for ($i = 0; $i < $columnsTotal; $i++) : $widgets = explode(',', $dashboardOrder[$i]); ?>
        <div class="col-md-<?php echo $columnSize?>">
            <?php foreach ($widgets as $wiget) : ?>
                <?php if ($wiget == 'online_operators') : ?>
                 
                     <?php if ($canListOnlineUsers == true || $canListOnlineUsersAll == true) : ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/online_operators.tpl.php'));?>
                     <?php endif;?>
                 
                <?php elseif ($wiget == 'active_chats') : ?>
                
                     <?php if ($activeTabEnabled == true && $online_chat_enabled_pre == true) : ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/active_chats.tpl.php'));?>
                     <?php endif;?>
                    
                <?php elseif ($wiget == 'departments_stats') : ?>
                
                    <?php if ($online_chat_enabled_pre == true) : ?>
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
                <?php else : ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/extension_panel_multiinclude.tpl.php'));?>
                <?php endif;?>
            <?php endforeach;?>
        </div>
     <?php endfor;?>
</div>

<?php $popoverInitialized = true; ?>
<script>
$( document ).ready(function() {
	$('#dashboard-body, #onlineusers, #map').popover({
		  trigger:'hover',
		  html : true, 
		  selector: '[data-toggle="popover"]',
		  content: function () {
			 if ($(this).is('[data-popover-content]')) {
				 return $('#'+$(this).attr('data-popover-content')+'-'+$(this).attr('data-chat-id')).html();
		     } else {
		    	 return $('#popover-content-'+$(this).attr('data-chat-id')).html();
			 }
		  },
		  title: function () {
			 return  $('#popover-title-'+$(this).attr('data-chat-id')).html();
		  }
		});
    $(".btn-block-department").on("click", "[data-stopPropagation]", function(e) {
        e.stopPropagation();
    });
});
</script>
