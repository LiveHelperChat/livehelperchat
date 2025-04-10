<div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_notifications') : ?>active<?php endif;?>" id="notifications">
     <div class="form-group">
         <div class="float-start">
            <?php include(erLhcoreClassDesign::designtpl('lhchat/user_settings.tpl.php'));?>
		 </div>
		 
		 <br/>
	     <br/>
	     
	     <form action="<?php echo erLhcoreClassDesign::baseurl('user/account')?>#notifications" method="post" enctype="multipart/form-data">
	         <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

             <div class="row pb-2">
                 <div class="col-12">
                     <div class="input-group">
                         <input type="button" class="btn btn-sm btn-primary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Request notification permission')?>" onclick="lhinst.requestNotificationPermission()" />
                         <input type="number" id="test_chat_id" class="form-control form-control-sm" value="" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','You can test notification by entering chat ID')?>"/>
                         <input type="button" class="btn btn-sm btn-primary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Test notification')?>" onclick="ee.emitEvent('svelteTestNotification',[document.getElementById('test_chat_id').value])" />
                         <span class="input-group-text fs12"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','If you do not see a notification after a click it means your settings are not correct, or you do not allow notifications.')?></span>
                     </div>
                 </div>
             </div>

             <div class="row">
                <div class="col-6">
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

                    <div class="form-group">
                        <label><input type="checkbox" name="hide_quick_notifications" value="1" <?php erLhcoreClassModelUserSetting::getSetting('hide_quick_notifications',0) == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Hide quick notifications');?></label>
                        <br/><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Quick notifications are the ones that you see at the top left corner of the application.')?></i></small>
                    </div>


                </div>
                <div class="col-6">
                
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
                        <select class="form-control form-control-sm" name="trackactivitytimeout">
                            <option value="-1" <?php echo erLhcoreClassModelUserSetting::getSetting('trackactivitytimeout',-1) == -1 ? 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Use default system value')?> (<?php echo (int)erLhcoreClassModelChatConfig::fetchCache('activity_timeout')->current_value?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','minutes')?>)</option>
                            <option value="300" <?php echo erLhcoreClassModelUserSetting::getSetting('trackactivitytimeout',-1) == 300 ? 'selected="selected"' : ''?>>5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','minutes')?></option>
                            <option value="600" <?php echo erLhcoreClassModelUserSetting::getSetting('trackactivitytimeout',-1) == 600 ? 'selected="selected"' : ''?>>10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','minutes')?></option>
                            <option value="1800" <?php echo erLhcoreClassModelUserSetting::getSetting('trackactivitytimeout',-1) == 1800 ? 'selected="selected"' : ''?>>30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','minutes')?></option>
                            <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhuser','largeactivitytimeout')) : ?>
                            <option value="3600" <?php echo erLhcoreClassModelUserSetting::getSetting('trackactivitytimeout',-1) == 3600 ? 'selected="selected"' : ''?>>1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','hour')?>
                            <option value="14400" <?php echo erLhcoreClassModelUserSetting::getSetting('trackactivitytimeout',-1) == 14400 ? 'selected="selected"' : ''?>>4 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','hours')?>
                            <option value="28800" <?php echo erLhcoreClassModelUserSetting::getSetting('trackactivitytimeout',-1) == 28800 ? 'selected="selected"' : ''?>>8 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','hours')?>
                            <option value="43200" <?php echo erLhcoreClassModelUserSetting::getSetting('trackactivitytimeout',-1) == 43200 ? 'selected="selected"' : ''?>>12 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','hours')?>
                            <option value="86400" <?php echo erLhcoreClassModelUserSetting::getSetting('trackactivitytimeout',-1) == 86400 ? 'selected="selected"' : ''?>>1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','day')?>
                            <option value="432000" <?php echo erLhcoreClassModelUserSetting::getSetting('trackactivitytimeout',-1) == 432000 ? 'selected="selected"' : ''?>>5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','days')?>
                            <option value="864000" <?php echo erLhcoreClassModelUserSetting::getSetting('trackactivitytimeout',-1) == 864000 ? 'selected="selected"' : ''?>>10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','days')?>
                            <option value="1296000" <?php echo erLhcoreClassModelUserSetting::getSetting('trackactivitytimeout',-1) == 1296000 ? 'selected="selected"' : ''?>>15 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','days')?>
                            <?php endif; ?>
                        </select>
            	     </div>
            	     
                </div>	
             </div>	
                	     
    	     <input type="submit" class="btn btn-secondary" name="UpdateNotifications_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Update');?>" />
         </form>
         
	 </div>


    <?php $notificationsSettings = (array)erLhcoreClassModelChatConfig::fetch('notifications_settings_op')->data; ?>

    <?php if ($notificationsSettings['enabled'] == 1) : ?>

    <hr>
    <h3>Persistent notifications</h3>

    <p>Those notifications are sent independently is browser closed or not.</p>

    <button class="btn btn-sm btn-primary" id="subscribe-persistent">Subscribe</button>

    <script>
        const publicKey = <?php echo json_encode($notificationsSettings['public_key']); ?>;

        if ('serviceWorker' in navigator && 'PushManager' in window) {
            // Register service worker
            let swRegistration;
            navigator.serviceWorker.register('<?php echo erLhcoreClassDesign::baseurl('notifications/serviceworkerop')?>')
                .then(registration => {
                    console.log('Service Worker registered');
                    swRegistration = registration;
                    return registration.pushManager.getSubscription();
                })
                .then(subscription => {
                    document.getElementById('subscribe-persistent').addEventListener('click', () => {
                        subscribeUser(swRegistration);
                    });
                });
        }

        async function subscribeUser(registration) {
            const subscriptionOptions = {
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(publicKey)
            };
            try {
                const subscription = await registration.pushManager.subscribe(subscriptionOptions);
                await fetch('<?php echo erLhcoreClassDesign::baseurl('notifications/subscribeop')?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(subscription),
                    credentials: 'same-origin'
                });

                alert('Subscribed successfully');
            } catch (error) {
                alert('Subscription failed:'+ JSON.stringify(error));
            }
        }

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding)
                .replace(/\-/g, '+')
                .replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }
    </script>

    <h5 class="mt-4">Your subscriptions</h5>

    <div id="subscriptions">
        <table class="table table-sm">
            <thead>
                <th>Device</th>
                <th>Registration date</th>
                <th>Update date</th>
                <th>Status</th>
            </thead>
            <?php foreach (\LiveHelperChat\Models\Notifications\OperatorSubscriber::getList(array('limit' => 100, 'filter' => array('user_id' => $user->id))) as $notificationSubscriber) : ?>
            <tr>
                <td><i class="material-icons" title="<?php echo htmlspecialchars($notificationSubscriber->uagent)?>"><?php echo ($notificationSubscriber->device_type == 0 ? 'computer' : ($notificationSubscriber->device_type == 1 ? 'smartphone' : 'tablet')) ?></i><?php echo ($notificationSubscriber->device_type == 0 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Computer') : ($notificationSubscriber->device_type == 1 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Smartphone') : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Tablet'))) ?></td>
                <td nowrap="nowrap">
                    <?php echo $notificationSubscriber->ctime_front?>
                </td>
                <td nowrap="nowrap">
                    <?php echo $notificationSubscriber->utime_front?>
                </td>
                <td>
                    <?php if ($notificationSubscriber->status == 0) : ?>
                        Active
                    <?php else : ?>
                        In-Active
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <?php endif; ?>

</div>





