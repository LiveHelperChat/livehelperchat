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
             
             
             <div class="row">
                <div class="col-xs-6">
            	     <div class="form-group">
                        <label><input type="checkbox" name="ownntfonly" value="on" <?php erLhcoreClassModelUserSetting::getSetting('ownntfonly',0) == 1 ? print 'checked="checked"' : '' ?>> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Show notification only if I am an owner pending chat')?></label>
                        <br/><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','You will stop receive notifications for pending chats if you are not an owner')?></i></small>
            	     </div>
            	     
                     <div class="form-group">
                        <label><input type="checkbox" name="sn_off" value="on" <?php erLhcoreClassModelUserSetting::getSetting('sn_off',1) == 1 ? print 'checked="checked"' : '' ?>> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Show notifications if I am offline')?></label>
                        <br/><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','You will not receive notifications if you are not online')?></i></small>
            	     </div>
            	        	     
            	     
            	     <div class="form-group">
                        <label><input type="checkbox" name="show_alert_chat" value="on" <?php erLhcoreClassModelUserSetting::getSetting('show_alert_chat',0) == 1 ? print 'checked="checked"' : '' ?>> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Show alert for new chats')?></label>
            	     </div>

            	     <div class="form-group">
                        <label><input type="checkbox" name="show_alert_transfer" value="on" <?php erLhcoreClassModelUserSetting::getSetting('show_alert_transfer',1) == 1 ? print 'checked="checked"' : '' ?>> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Show alerts for transferred chats')?></label>
                         <br/><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','You will receive alert notification if chat is transferred directly to you. You will be able to accept it directly from alert.')?></i></small>
            	     </div>

                </div>
                <div class="col-xs-6">
                
                     <?php if ((int)erLhcoreClassModelChatConfig::fetchCache('activity_track_all')->current_value == 1) : ?>
                     <div class="alert alert-warning"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Activity tracking is set at global level. Your settings will be be ignored. Timeout value still will be taken from your account settings.')?></div>
                     <?php endif; ?>
                     
                     <div class="form-group">
                        <label><input type="checkbox" name="trackactivity" value="on" <?php erLhcoreClassModelUserSetting::getSetting('trackactivity',0) == 1 ? print 'checked="checked"' : '' ?>> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Change my online/offline status based on my activity')?></label>
                        <br/><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','After certain period of time if no actions are detected you will be marked as offline automatically')?></i></small>
            	     </div>
            	     
                     <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Choose timeout value')?></label>
                        <br/><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Select after how long of inactivity you will be marked as offline automatically')?></i></small>
                        <select class="form-control" name="trackactivitytimeout">
                            <option value="-1" <?php echo erLhcoreClassModelUserSetting::getSetting('trackactivitytimeout',-1) == -1 ? 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Use default system value')?> (<?php echo (int)erLhcoreClassModelChatConfig::fetchCache('activity_timeout')->current_value?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','minutes')?>)</option>
                            <option value="300" <?php echo erLhcoreClassModelUserSetting::getSetting('trackactivitytimeout',-1) == 300 ? 'selected="selected"' : ''?>>5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','minutes')?></option>
                            <option value="600" <?php echo erLhcoreClassModelUserSetting::getSetting('trackactivitytimeout',-1) == 600 ? 'selected="selected"' : ''?>>10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','minutes')?></option>
                            <option value="1800" <?php echo erLhcoreClassModelUserSetting::getSetting('trackactivitytimeout',-1) == 1800 ? 'selected="selected"' : ''?>>30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','minutes')?></option>
                        </select>
            	     </div>
            	     
                </div>	
             </div>	
                	     
    	     <input type="submit" class="btn btn-default" name="UpdateNotifications_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Update');?>" />
         </form>
         
	 </div>	
</div>





