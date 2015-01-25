<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Mail settings');?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post" autocomplete="off">
		
	<div class="section-container auto" data-section="auto" id="tabs" data-options="deep_linking: true" ng-cloak>
		<section>
			<p class="title" data-section-title>
				<a href="#mailsettings" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Mail settings');?>">Mail settings</a>
			</p>
			<div class="content" data-section-content data-slug="mailsettings">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Sender address');?></label> 
				<input type="text" name="sender" value="<?php (isset($smtp_data['sender']) && $smtp_data['sender'] != '') ? print $smtp_data['sender'] : print '' ?>" /> 
				    
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Default from e-mail address');?></label> 
				<input type="text" name="default_from" value="<?php (isset($smtp_data['default_from']) && $smtp_data['default_from'] != '') ? print $smtp_data['default_from'] : print '' ?>" /> 
			    
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Default from name');?></label> 
				<input type="text" name="default_from_name" value="<?php (isset($smtp_data['default_from_name']) && $smtp_data['default_from_name'] != '') ? print $smtp_data['default_from_name'] : print '' ?>" /> 
				    				            
                <ul class="button-group round">
					<li><input type="submit" class="button small" name="StoreMailSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" /></li>
					<li><input type="submit" class="button small" name="StoreMailSettingsTest" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Test'); ?>" /></li>
				</ul>
			</div>
		</section>
		
		<section>
			<p class="title" data-section-title>
				<a href="#smtp" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','SMTP');?>">SMTP</a>
			</p>
			<div class="content" data-section-content data-slug="smtp">

				<label><input type="checkbox" name="use_smtp" value="1" <?php isset($smtp_data['use_smtp']) && ($smtp_data['use_smtp'] == '1') ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','SMTP enabled'); ?></label> 
				
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Login');?></label> 
				<input type="text" name="username" value="<?php (isset($smtp_data['username']) && $smtp_data['username'] != '') ? print $smtp_data['username'] : print '' ?>" /> 
				
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Password');?></label> 
				<input type="password" name="password" value="<?php (isset($smtp_data['password']) && $smtp_data['password'] != '') ? print $smtp_data['password'] : print '' ?>" /> 
				
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Host');?>*</label>
				
				<input type="text" name="host" value="<?php (isset($smtp_data['host']) && $smtp_data['host'] != '') ? print $smtp_data['host'] : print '' ?>" /> <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Port');?>*</label> <input type="text" name="port" value="<?php (isset($smtp_data['port']) && $smtp_data['port'] != '') ? print $smtp_data['port'] : print '25' ?>" />
            
                <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
            
                <ul class="button-group round">
					<li><input type="submit" class="button small" name="StoreSMTPSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" /></li>
					<li><input type="submit" class="button small" name="StoreSMTPSettingsTest" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Test'); ?>" /></li>
				</ul>
			</div>
		</section>
	</div>
	
	
</form>