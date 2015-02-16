<?php
$canListOnlineUsers = false;
$canListOnlineUsersAll = false;
$currentUser = erLhcoreClassUser::instance();

if (erLhcoreClassModelChatConfig::fetch('list_online_operators')->current_value == 1) {
    $canListOnlineUsers = $currentUser->hasAccessTo('lhuser', 'userlistonline');
    $canListOnlineUsersAll = $currentUser->hasAccessTo('lhuser', 'userlistonlineall');
}

$pendingTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_pending_list', 1);
$activeTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_active_list', 1);
$closedTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_close_list', 0);
$unreadTabEnabled = erLhcoreClassModelUserSetting::getSetting('enable_unread_list', 1);

$frontTabsOrder = explode(',', erLhcoreClassModelChatConfig::fetch('front_tabs')->current_value);

?>
 
<?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/online_settings_general.tpl.php')); ?>

<div ng-controller="OnlineCtrl as online" ng-init='groupByField = <?php echo json_encode($ogroupBy)?>;online.maxRows=<?php echo (int)$omaxRows?>;online.updateTimeout=<?php echo (int)$oupdTimeout?>;online.userTimeout = <?php echo (int)$ouserTimeout?>;online.department=<?php echo (int)$onlineDepartment?>;online.soundEnabled=<?php echo $soundUserNotification == 1 ? 'true' : 'false'?>;online.notificationEnabled=<?php echo $browserNotification == 1 ? 'true' : 'false'?>'>
       
    <div role="tabpanel" id="tabs" ng-cloak>
        <ul class="nav nav-pills" role="tablist">
            <?php foreach ($frontTabsOrder as $frontTab) : ?>
            		<?php if (trim($frontTab) == 'online_users') : ?> 
            		      <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/section_online_users_tab.tpl.php')); ?>
            		<?php elseif (trim($frontTab) == 'online_map') : ?>
            		      <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/section_map_online_tab.tpl.php')); ?> 
            		<?php elseif (trim($frontTab) == 'pending_chats') : ?>
            		      <?php if ($pendingTabEnabled == true) : ?>
            		          <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_pending_list_tab.tpl.php'));?>
            		      <?php endif;?>
            		<?php elseif (trim($frontTab) == 'active_chats') : ?>
            		      <?php if ($activeTabEnabled == true) : ?>
            		          <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_active_list_tab.tpl.php'));?>
            		      <?php endif;?>
            	    <?php elseif (trim($frontTab) == 'unread_chats') : ?>
                           <?php if ($unreadTabEnabled == true) : ?>
                               <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_unread_list_tab.tpl.php'));?>
            		       <?php endif;?>
            		<?php elseif (trim($frontTab) == 'closed_chats') : ?>    
                            <?php if ($closedTabEnabled == true) : ?>
                                <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_closed_list_tab.tpl.php'));?>
            		        <?php endif;?>
            		<?php elseif (trim($frontTab) == 'online_operators') : ?>    
                            <?php if ($canListOnlineUsers == true || $canListOnlineUsersAll == true) : ?>
                                <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_online_op_list_tab.tpl.php'));?>
                    		<?php endif;?>            		
            		<?php endif;?>
            <?php endforeach;?>
        </ul>
        
        <div class="tab-content" ng-cloak>    		
    		      <?php foreach ($frontTabsOrder as $frontTab) : ?>    
                      <?php if (trim($frontTab) == 'online_users') : ?>    
                      <div role="tabpanel" class="tab-pane" id="onlineusers">
                            <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/section_online_users.tpl.php')); ?>
                      </div>
                      <?php elseif (trim($frontTab) == 'online_map') : ?>
                      <div role="tabpanel" class="tab-pane" id="map">
                            <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/section_map_online.tpl.php')); ?>   
                        </div>   
                      <?php elseif (trim($frontTab) == 'pending_chats') : ?>
                          <?php if ($pendingTabEnabled == true) : ?>
                          <div role="tabpanel" class="tab-pane" id="pendingchats">
                                <div id="pending-chat-list">
                              		<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_pending_list.tpl.php'));?>
                              	</div>
                				<br /> <a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('chat/pendingchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All pending chats');?></a>
                          </div>
                          <?php endif;?>     
                     <?php elseif (trim($frontTab) == 'active_chats') : ?>
                     
                      <?php if ($activeTabEnabled == true) : ?>
                      <div role="tabpanel" class="tab-pane" id="activechats">
                            <div id="active-chat-list">
                         		<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_active_list.tpl.php'));?>
                         	</div>
                			<br /> <a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('chat/activechats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All active chats');?></a>
                      </div>                      
                      <?php endif;?>
                      
                    <?php elseif (trim($frontTab) == 'unread_chats') : ?>
                    
                      <?php if ($unreadTabEnabled == true) : ?>
                      <div role="tabpanel" class="tab-pane" id="unreadchats">
                            <div id="unread-chat-list"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_unread_list.tpl.php'));?></div>
                			<br /> <a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('chat/unreadchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All unread chats');?></a>
                      </div>
                      <?php endif;?>
                      
                    <?php elseif (trim($frontTab) == 'closed_chats') : ?>
                    
                      <?php if ($closedTabEnabled == true) : ?>
                      <div role="tabpanel" class="tab-pane" id="closedchats">
                          <div id="closed-chat-list"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_closed_list.tpl.php'));?></div>
                		  <br /> <a class="small button round" href="<?php echo erLhcoreClassDesign::baseurl('chat/closedchats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','All closed chats');?></a>
                      </div>                     
                      <?php endif;?>
                                          
                    <?php elseif (trim($frontTab) == 'online_operators') : ?>
                    
                      <?php if ($canListOnlineUsers == true || $canListOnlineUsersAll == true) : ?>
                      <div role="tabpanel" class="tab-pane" id="onlineoperators">
                            <div id="online-operator-list"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_online_op_list.tpl.php'));?></div>
                      </div>                      
                      <?php endif; ?>
                      
                    <?php endif; ?>
                 <?php endforeach;?>
    		</div>
    	</div>
    </div>


<script>
$( document ).ready(function() {
	lhinst.attachTabNavigator();
	$('#right-column-page').removeAttr('id');
	$('#tabs a:first').tab('show')
});
<?php include(erLhcoreClassDesign::designtpl('lhchat/part/opened_chats_js.tpl.php')); ?>
</script>