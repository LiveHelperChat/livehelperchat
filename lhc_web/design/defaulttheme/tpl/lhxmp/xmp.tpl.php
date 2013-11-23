<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','XMPP settings');?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if (isset($token_received) && $token_received == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Succesfully authorised, now you can try to send a message'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if (isset($token_revoked) && $token_revoked == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Token was revoked'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if (isset($message_send)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','XMPP message was sent succesfuly'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('xmp/configuration')?>" method="post" autocomplete="off">

<label><input type="checkbox" name="use_xmp" value="1" <?php isset($xmp_data['use_xmp']) && ($xmp_data['use_xmp'] == '1') ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','XMPP active'); ?></label>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','XMPP Message content');?></label>
<textarea name="XMPMessage" style="height:100px;"><?php echo htmlspecialchars($xmp_data['xmp_message'])?></textarea>
				
<div class="section-container auto" data-section>
  <section <?php if ( (isset($xmp_data['use_standard_xmp']) && $xmp_data['use_standard_xmp'] == '0') || !isset($xmp_data['use_standard_xmp']) ) : ?>class="active"<?php endif;?> >
    <p class="title" data-section-title><a href="#"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','XMPP');?></a></p>
    <div class="content" data-section-content>
		<div>		
				<label><input type="radio" name="use_standard_xmp" value="0" <?php ( (isset($xmp_data['use_standard_xmp']) && $xmp_data['use_standard_xmp'] == '0') || !isset($xmp_data['use_standard_xmp']) ) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','User standard XMPP service'); ?></label>
		
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Host');?></label>
				<input type="text" name="host" placeholder="talk.google.com" value="<?php (isset($xmp_data['host']) && $xmp_data['host'] != '') ? print $xmp_data['host'] : print '' ?>" />
				
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Port');?></label>
				<input type="text" name="port" value="<?php (isset($xmp_data['port']) && $xmp_data['port'] != '') ? print $xmp_data['port'] : print '5222' ?>" />
				
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Login');?></label>
				<input type="text" name="username" value="<?php (isset($xmp_data['username']) && $xmp_data['username'] != '') ? print $xmp_data['username'] : print '' ?>" />
				
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Password');?></label>
				<input type="password" name="password" value="" />
				
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Resource');?></label>
				<input type="text" name="resource" placeholder="xmpphp" value="<?php (isset($xmp_data['resource']) && $xmp_data['resource'] != '') ? print $xmp_data['resource'] : print '' ?>" />
				
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Server');?></label>
				<input type="text" name="server" placeholder="gmail.com" value="<?php (isset($xmp_data['server']) && $xmp_data['server'] != '') ? print $xmp_data['server'] : print '' ?>" />
				
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Recipients');?></label>
				<input type="text" name="recipients" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Default recipients'); ?>" value="<?php (isset($xmp_data['recipients']) && $xmp_data['recipients'] != '') ? print $xmp_data['recipients'] : print '' ?>" />
								
				<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
				
				<ul class="button-group round">
				  <li><input type="submit" class="button small" name="StoreXMPSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" /></li>
				  <li><input type="submit" class="button small" name="StoreXMPSettingsTest" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Test message will be send to your account e-mail'); ?>" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Send test message'); ?>" /></li>
				</ul>
		</div>
	</div>
	</section>
    <section <?php if (isset($xmp_data['use_standard_xmp']) && $xmp_data['use_standard_xmp'] == '1' ) : ?>class="active"<?php endif;?>>
	    <p class="title" data-section-title><a href="#"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','GTalk');?></a></p>
	    <div class="content" data-section-content>
			<div>
			
					<label><input type="radio" name="use_standard_xmp" value="1" <?php isset($xmp_data['use_standard_xmp']) && ($xmp_data['use_standard_xmp'] == '1') ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Use GTalk for messaging'); ?></label>
					<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Information for your google app')?></h4>	
					<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Redirect URL, this url you will have to enter in your google app configuration')?></label>
					<input type="text" value="http://<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('xmp/configuration')?>/(gtalkoauth)/true" />
					
					<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Enter your app information bellow')?></h4>						
					<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','OAuth 2.0 Client ID');?></label>
					<input type="text" name="gtalk_client_id" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Please enter your Client ID')?>" value="<?php (isset($xmp_data['gtalk_client_id']) && $xmp_data['gtalk_client_id'] != '') ? print $xmp_data['gtalk_client_id'] : print '' ?>" />
					
					<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Client secret');?></label>
					<input type="text" name="gtalk_client_secret" value="<?php (isset($xmp_data['gtalk_client_secret']) && $xmp_data['gtalk_client_secret'] != '') ? print $xmp_data['gtalk_client_secret'] : print '' ?>" />
										
					<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
					
					<?php if (erLhcoreClassXMP::getAccessToken() !== false) : ?>
					<input type="submit" class="button small right success round" name="StoreXMPGTalkSendeMessage" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','This message will be send to your e-mail'); ?>" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Send test message'); ?>" />
					<?php endif;?>
										
					<ul class="button-group round">
					  <li><input type="submit" class="button small" name="StoreXMPGTalkSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" /></li>
					  					  
					  <?php if (erLhcoreClassXMP::getAccessToken() !== false) : ?>					  
					  	<li><input type="submit" class="button small" name="StoreXMPGTalkRevokeToken" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Revoke access token'); ?>" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Revoke permission to send a message'); ?>" /></li>
					  <?php else : ?>
						  <?php if (isset($xmp_data['gtalk_client_secret']) && (!empty($xmp_data['gtalk_client_secret']))) : ?>
						  		<li><input type="submit" class="button small" name="StoreXMPGTalkSettingsTest" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Authentificate and grant permission to send a message'); ?>" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Grant permission to send a message'); ?>" /></li>
						  <?php endif;?>
					  <?php endif;?>
					</ul>	
			</div>
		</div>
	</section>
	
	
	
</div>
</form>