<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Chat configuration');?></h1>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post">
<div class="section-container auto" data-section>
  <section class="active">
	    <p class="title" data-section-title><a href="#"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Notifications about new chats');?></a></p>
	    <div class="content" data-section-content>	
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
	    <p class="title" data-section-title><a href="#"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Copyright settings');?></a></p>
	    <div class="content" data-section-content>	
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
							</div>
						</section>
						
					</div>		    		
			</div>		
		</div>
   </section>
	
   <section>
	    <p class="title" data-section-title><a href="#"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Online tracking');?></a></p>
	    <div class="content" data-section-content>	
		    <?php $attribute = 'ignorable_ip'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>	
		    		
		    <?php $attribute = 'track_online_visitors'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>		
		    				    		
		    <?php $attribute = 'sound_invitation'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>		
		    		
		    <?php $attribute = 'pro_active_invite'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>		
		    		
		    <?php $attribute = 'message_seen_timeout'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>		
		    		
		    <?php $attribute = 'track_footprint'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>		
		    		
		    <?php $attribute = 'tracked_users_cleanup'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>		
		    		
		    <?php $attribute = 'pro_active_show_if_offline'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>		
		    			    		
		    <?php $attribute = 'pro_active_limitation'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		</div>
	</section>
	
  <section>
	    <p class="title" data-section-title><a href="#"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Misc');?></a></p>
	    <div class="content" data-section-content>			    		    			    		
		    <?php $attribute = 'voting_days_limit'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		    
		    <?php $attribute = 'session_captcha'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		    		    
		    <?php $attribute = 'list_online_operators'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		    		    
		    <?php $attribute = 'export_hash'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		    		    
		    <?php $attribute = 'disable_popup_restore'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		    		    
		    <?php $attribute = 'reopen_chat_enabled'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		    		    		    
		    <?php $attribute = 'accept_chat_link_timeout'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>		    
		</div>
	</section>
	
  <section>
	    <p class="title" data-section-title><a href="#"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Workflow');?></a></p>
	    <div class="content" data-section-content>	
		    <?php $attribute = 'run_unaswered_chat_workflow'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		    
		    <?php $attribute = 'run_departments_workflow'?>
		    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
		</div>
  </section>
  <?php endif; ?>
</div>
  
<?php if ($currentUser->hasAccessTo('lhchat','administrateconfig')) : ?>
<input type="submit" class="button small radius" name="UpdateConfig" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/listchatconfig','Update')?>"/>
<?php endif; ?>
</form>