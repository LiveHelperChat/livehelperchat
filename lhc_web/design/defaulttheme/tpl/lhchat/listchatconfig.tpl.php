<?php include(erLhcoreClassDesign::designtpl('lhchat/lists_titles/listchatconfig.tpl.php'));?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post">

<div role="tabpanel">

		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/listchatconfig_tabs/links.tpl.php'));?>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="notifications">
    			 <div class="form-group">
    			     <input type="button" class="btn btn-primary btn-lg" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Request notification permission')?>" onclick="lhinst.requestNotificationPermission()" />
    				 <?php include(erLhcoreClassDesign::designtpl('lhchat/user_settings.tpl.php'));?>
    			 </div>
			</div>
			
			<?php if ($currentUser->hasAccessTo('lhchat','administrateconfig')) : ?>
			<div role="tabpanel" class="tab-pane" id="copyright">
			    <div class="section-container auto" data-section>
    			    <div role="tabpanel">
                      <!-- Nav tabs -->
                      <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#applicationame" aria-controls="applicationame" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Application name');?></a></li>
                        <li role="presentation"><a href="#sitesettings" aria-controls="sitesettings" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Site settings');?></a></li>
                      </ul>                
                      <!-- Tab panes -->
                      <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="applicationame">
                                <?php $attribute = 'application_name'?>
    		    				<?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="sitesettings">
                                <?php $attribute = 'customer_company_name'?>
    		    				<?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    					
    							<?php $attribute = 'customer_site_url'?>
    		    				<?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    					
    							<?php $attribute = 'accept_tos_link'?>
    		    				<?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
                        </div>
                      </div>                                  
                    </div>
				</div>
			</div>	
			
			<div role="tabpanel" class="tab-pane" id="onlinetracking">
			    <?php $attribute = 'ignorable_ip'; ?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>	
    		    
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/listchatconfig/banned_ip_range.tpl.php'));?>
    		    		
    		    <?php $attribute = 'track_online_visitors';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>		
    		    		
    		    <?php $attribute = 'track_if_offline';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>		
    		    				    				    		
    		    <?php $attribute = 'need_help_tip';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>		
    		    				    				    		
    		    <?php $attribute = 'need_help_tip_timeout';?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    				    		
    		    <?php $attribute = 'sound_invitation';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>		
    		    		
    		    <?php $attribute = 'pro_active_invite';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>		
    		    		
    		    <?php $attribute = 'track_footprint';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>		
    		    
    		    <?php $attribute = 'pro_active_show_if_offline';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    
    		    <?php $attribute = 'message_seen_timeout'?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    
    		    <?php $attribute = 'tracked_users_cleanup'?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>		
    		    			    		
    		    <?php $attribute = 'pro_active_limitation'?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
			</div>

			<div role="tabpanel" class="tab-pane" id="misc">
			    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/listchatconfig/text_cookie_related.tpl.php'));?>		    		    
    		    <?php $attribute = 'track_domain'?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    		    		    
    		    <?php $attribute = 'explicit_http_mode'?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    		    		    
    		    <?php $attribute = 'use_secure_cookie';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    		    		    
    		    <?php $attribute = 'disable_html5_storage';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    
    		    <?php $attribute = 'session_captcha';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/listchatconfig/text_chat_related.tpl.php'));?>
    		    <?php $attribute = 'list_online_operators';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    
    		    <?php $attribute = 'disable_popup_restore';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    
    		    <?php $attribute = 'bbc_button_visible';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    		    
    		    <?php $attribute = 'reopen_chat_enabled';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    		    
    		    <?php $attribute = 'reopen_as_new';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    		    
    		    <?php $attribute = 'automatically_reopen_chat';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    		    
    		    <?php $attribute = 'allow_reopen_closed';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		   			     		    
    		    <?php $attribute = 'min_phone_length';?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    
    		    <?php $attribute = 'export_hash'?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    
    		    <?php $attribute = 'update_ip'?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    
    		    <?php $attribute = 'accept_chat_link_timeout'?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>	
    		    
    		    <?php $attribute = 'activity_timeout'?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>	
    		    
    		    <?php $attribute = 'activity_track_all';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>	
    		    
    		    <?php $attribute = 'disable_print';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    		    
    		    <?php $attribute = 'disable_send';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    
    		    <?php $attribute = 'max_message_length'?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?> 
    		    
    		    <?php $attribute = 'mheight'?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?> 
    		    
    		    <?php $attribute = 'hide_disabled_department';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/listchatconfig/front_tabs.tpl.php'));?>
   		    
                <?php include(erLhcoreClassDesign::designtpl('lhchat/part/listchatconfig/hide_right_column_frontpage.tpl.php'));?>
    		    
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/listchatconfig/dashboard_order.tpl.php'));?>
    		    
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/listchatconfig/suggest_leave_msg.tpl.php'));?>
    		    
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/listchatconfig/text_misc.tpl.php'));?>
    		    <?php $attribute = 'voting_days_limit'?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		     
    		    <?php $attribute = 'ignore_user_status';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		     
    		    <?php $attribute = 'faq_email_required';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    <hr>	
    		    <?php $attribute = 'show_language_switcher';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		     
    		    <?php $attribute = 'show_languages';?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    <hr>		    
    		    <?php $attribute = 'autoclose_timeout'?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    
    		    <?php $attribute = 'autopurge_timeout'?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>

    		    <?php $attribute = 'assign_workflow_timeout'?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>

    		    <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Close button')?></h4>
    		    <?php $attribute = 'hide_button_dropdown'; $boolValue = true; ?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>

    		    <?php $attribute = 'on_close_exit_chat'; $boolValue = true; ?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>

			</div>
						
			<div role="tabpanel" class="tab-pane" id="visitoractivity">
			 
                <?php include(erLhcoreClassDesign::designtpl('lhchat/part/listchatconfig/text_widget_change_status.tpl.php'));?>
			    
			    <?php $attribute = 'checkstatus_timeout'?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		    
			    <?php $attribute = 'track_is_online';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
    		 
	            <?php $attribute = 'track_activity'; $boolValue = true;?>
	            <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?> 
	 
	            <?php $attribute = 'track_mouse_activity'; $boolValue = true;?>
		        <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?> 
	  		   
    		   	
    		   	<?php $systemconfig = erLhcoreClassModelChatConfig::fetch('online_if');?>	  
    		    <div class="form-group"> 
        		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/listchatconfig/label_online_if.tpl.php'));?>
        		    <select class="form-control" name="online_ifValueParam">
        		          <option value="0" <?php echo $systemconfig->value == 0 ? 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','(Widget is open) or (closed and user has activity in last 5 minutes and ping respond)');?></option>    		        
        		          <option value="1" <?php echo $systemconfig->value == 1 ? 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','(Widget is open or closed) and (user has activity in last 5 minutes and ping respond)');?></option>
        		    </select>
    		    </div>
    		    
			</div>

			<div role="tabpanel" class="tab-pane" id="workflow">
                <?php include(erLhcoreClassDesign::designtpl('lhchat/part/listchatconfig/run_unaswered_chat_workflow.tpl.php'));?>

    		    <?php $attribute = 'run_departments_workflow';$boolValue = true;?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>

    		    <?php $attribute = 'inform_unread_message';?>
    		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>    		    
			</div>

			<?php include(erLhcoreClassDesign::designtpl('lhchat/listchatconfig/screen_sharing.tpl.php'));?>
										
			<?php endif;?>		
							
		</div>
</div>

  
<?php if ($currentUser->hasAccessTo('lhchat','administrateconfig')) : ?>
<input type="submit" class="btn btn-default" name="UpdateConfig" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Update')?>"/>
<?php endif; ?>
</form>