<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Chat configuration');?></h1>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post">
<div class="section-container auto" data-section data-options="deep_linking: true">
  <section class="active">
	    <p class="title" data-section-title><a href="#notifications"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Notifications about new chats');?></a></p>
	    <div class="content" data-section-content data-slug="notifications">	
		    <div>			    
				<input type="button" class="button radius" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Request notification permission')?>" onclick="lhinst.requestNotificationPermission()" />
				<div class="row">
					<div class="columns small-2 end">
						<?php include(erLhcoreClassDesign::designtpl('lhchat/user_settings.tpl.php'));?>
					</div>
				</div>				
			</div>		
		</div>
  </section>
  
  <?php if ($currentUser->hasAccessTo('lhchat','administrateconfig')) : ?>
  <section>
	    <p class="title" data-section-title><a href="#copyright"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Copyright settings');?></a></p>
	    <div class="content" data-section-content data-slug="copyright">	
		    <div>		
		    		<div class="section-container auto" data-section>
					  	<section class="active">
						    <p class="title" data-section-title><a href="#"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Application name');?></a></p>
						    <div class="content" data-section-content>	
							    <?php $attribute = 'application_name'?>
		    					<?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>		
							</div>
						</section>
						
					  	<section>
						    <p class="title" data-section-title><a href="#"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Site settings');?></a></p>
						    <div class="content" data-section-content>	
							    <?php $attribute = 'customer_company_name'?>
		    					<?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		    					
							    <?php $attribute = 'customer_site_url'?>
		    					<?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		    					
							    <?php $attribute = 'accept_tos_link'?>
		    					<?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>		    						    						
							</div>
						</section>
						
					</div>		    		
			</div>		
		</div>
   </section>
	
   <section>
	    <p class="title" data-section-title><a href="#onlinetracking"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Online tracking');?></a></p>
	    <div class="content" data-section-content data-slug="onlinetracking">	
		    <?php $attribute = 'ignorable_ip'; ?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>	
		    		
		    <?php $attribute = 'track_online_visitors';$boolValue = true;?>
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
	</section>
	
  <section>
	    <p class="title" data-section-title><a href="#misc"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Misc');?></a></p>
	    <div class="content" data-section-content data-slug="misc">			    		    			    		
		    
		    <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Cookie related');?></h4>		    		    
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
		    
		    <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Chat related');?></h4>
		    <?php $attribute = 'list_online_operators';$boolValue = true;?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		    
		    <?php $attribute = 'disable_popup_restore';$boolValue = true;?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		    
		    <?php $attribute = 'bbc_button_visible';$boolValue = true;?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		    		    
		    <?php $attribute = 'reopen_chat_enabled';$boolValue = true;?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		    		    
		    <?php $attribute = 'automatically_reopen_chat';$boolValue = true;?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		    		    
		    <?php $attribute = 'allow_reopen_closed';$boolValue = true;?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		    		    
		    <?php $attribute = 'reopen_as_new';$boolValue = true;?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		    
		    <?php $attribute = 'export_hash'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		    
		    <?php $attribute = 'update_ip'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		    
		    <?php $attribute = 'accept_chat_link_timeout'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>	
		    
		    <?php $attribute = 'disable_print';$boolValue = true;?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		    		    
		    <?php $attribute = 'disable_send';$boolValue = true;?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		    
		    <?php $attribute = 'max_message_length'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?> 
		    
		    <?php $attribute = 'hide_disabled_department';$boolValue = true;?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		    
		    <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Misc');?></h4>
		    <?php $attribute = 'voting_days_limit'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		     
		    <?php $attribute = 'ignore_user_status';$boolValue = true;?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		     
		    <?php $attribute = 'faq_email_required';$boolValue = true;?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		    
		    <?php $attribute = 'autoclose_timeout'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		    
		    <?php $attribute = 'autopurge_timeout'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		    	    
		</div>
	</section>
	
  <section>
	    <p class="title" data-section-title><a href="#workflow"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Workflow');?></a></p>
	    <div class="content" data-section-content data-slug="workflow">	
		    <?php $attribute = 'run_unaswered_chat_workflow'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		    
		    <?php $attribute = 'run_departments_workflow';$boolValue = true;?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		</div>
  </section>
  <?php endif; ?>
</div>
  
<?php if ($currentUser->hasAccessTo('lhchat','administrateconfig')) : ?>
<input type="submit" class="button small radius" name="UpdateConfig" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Update')?>"/>
<?php endif; ?>
</form>