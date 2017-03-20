<div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_notifications') : ?>active<?php endif;?>" id="notifications">
     <div class="form-group">
         <div class="pull-left">
            <?php include(erLhcoreClassDesign::designtpl('lhchat/user_settings.tpl.php'));?>
		 </div>
		 
		 <br/>
	     <br/>
	     
	     <form action="<?php echo erLhcoreClassDesign::baseurl('user/account')?>#notifications" method="post" enctype="multipart/form-data">
	         <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
	     
    	     <div class="form-group">
    	       <input type="button" class="btn btn-primary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Request notification permission')?>" onclick="lhinst.requestNotificationPermission()" />
             </div>
             
    	     <div class="form-group">
                <label><input type="checkbox" name="show_alert_chat" value="on" <?php erLhcoreClassModelUserSetting::getSetting('show_alert_chat',0) == 1 ? print 'checked="checked"' : '' ?>> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Show alert for new chats')?></label>
    	     </div>
             
    	     <div class="form-group">
                <label><input type="checkbox" name="sn_off" value="on" <?php erLhcoreClassModelUserSetting::getSetting('sn_off',1) == 1 ? print 'checked="checked"' : '' ?>> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Show notifications if I am offline')?></label>
    	     </div>
                	     
    	     <input type="submit" class="btn btn-default" name="UpdateNotifications_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Update');?>" />
         </form>
         
	 </div>	
</div>





