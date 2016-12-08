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

<div class="row form-group">
	<div class="col-xs-6">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','XMPP Message content');?></label>
		<textarea class="form-control" name="XMPMessage" style="height:100px;"><?php echo htmlspecialchars($xmp_data['xmp_message'])?></textarea>
	</div>
	<div class="col-xs-6">
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','XMPP Message about accepted chat');?></label>
		<textarea class="form-control" name="XMPAcceptedMessage" style="height:100px;"><?php echo htmlspecialchars($xmp_data['xmp_accepted_message'])?></textarea>
	</div>
</div>


<div role="tabpanel">
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="<?php if ( (isset($xmp_data['use_standard_xmp']) && $xmp_data['use_standard_xmp'] == '0') || !isset($xmp_data['use_standard_xmp']) ) : ?>active<?php endif;?>"><a href="#xmp" aria-controls="xmp" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','XMPP');?></a></li>
		<li role="presentation" <?php if (isset($xmp_data['use_standard_xmp']) && $xmp_data['use_standard_xmp'] == '1' ) : ?>class="active"<?php endif;?>><a href="#gtalk" aria-controls="gtalk" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','GTalk');?></a></li>
	</ul>

	<!-- Tab panes -->
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane <?php if ( (isset($xmp_data['use_standard_xmp']) && $xmp_data['use_standard_xmp'] == '0') || !isset($xmp_data['use_standard_xmp']) ) : ?>active<?php endif;?>" id="xmp">
		        <label><input type="radio" name="use_standard_xmp" value="0" <?php ( (isset($xmp_data['use_standard_xmp']) && $xmp_data['use_standard_xmp'] == '0') || !isset($xmp_data['use_standard_xmp']) ) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Use standard XMPP service'); ?></label>
		
				<div class="row form-group">
					<div class="col-xs-6">
						<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Host');?></label>
						<input class="form-control" type="text" name="host" placeholder="talk.google.com" value="<?php (isset($xmp_data['host']) && $xmp_data['host'] != '') ? print $xmp_data['host'] : print '' ?>" />
					</div>
					<div class="col-xs-6">
						<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Port');?></label>
						<input class="form-control" type="text" name="port" value="<?php (isset($xmp_data['port']) && $xmp_data['port'] != '') ? print $xmp_data['port'] : print '5222' ?>" />
					</div>
				</div>
				
				<div class="row form-group">
					<div class="col-xs-6">			
						<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Login');?></label>
						<input class="form-control" type="text" name="username" autocomplete="new-password" value="<?php (isset($xmp_data['username']) && $xmp_data['username'] != '') ? print $xmp_data['username'] : print '' ?>" />
					</div>
					<div class="col-xs-6">
						<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Password');?></label>
						<input class="form-control" type="password" name="password" autocomplete="new-password" value="" />
					</div>
				</div>
				
				<div class="row form-group">
					<div class="col-xs-6">
						<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Resource');?></label>
						<input class="form-control" type="text" name="resource" placeholder="xmpphp" value="<?php (isset($xmp_data['resource']) && $xmp_data['resource'] != '') ? print $xmp_data['resource'] : print '' ?>" />
					</div>
					<div class="col-xs-6">
						<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Server');?></label>
						<input class="form-control" type="text" name="server" placeholder="gmail.com" value="<?php (isset($xmp_data['server']) && $xmp_data['server'] != '') ? print $xmp_data['server'] : print '' ?>" />
					</div>
				</div>
				
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Recipients');?></label>
				<input class="form-control" type="text" name="recipients" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Default recipients'); ?>" value="<?php (isset($xmp_data['recipients']) && $xmp_data['recipients'] != '') ? print $xmp_data['recipients'] : print '' ?>" />
								
				<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
				
				<h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Test recipients');?></h3>
				
				<div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Individual recipients');?></label>
				<input class="form-control" type="text" name="test_recipients" value="<?php (isset($xmp_data['test_recipients']) && $xmp_data['test_recipients'] != '') ? print $xmp_data['test_recipients'] : print '' ?>" />
				</div>
				
				<div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Test group recipients');?></label>
				<input class="form-control" type="text" name="test_group_recipients" value="<?php (isset($xmp_data['test_group_recipients']) && $xmp_data['test_group_recipients'] != '') ? print $xmp_data['test_group_recipients'] : print '' ?>" />
				</div>
				
				<div class="btn-group" role="group" aria-label="...">
				  <input type="submit" class="btn btn-default" name="StoreXMPSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />
				  <input type="submit" class="btn btn-default" name="StoreXMPSettingsTest" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Test message will be send to your account e-mail'); ?>" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Send test message'); ?>" />
				</div>
				
		</div>
		<div role="tabpanel" class="tab-pane <?php if (isset($xmp_data['use_standard_xmp']) && $xmp_data['use_standard_xmp'] == '1' ) : ?>active<?php endif;?>" id="gtalk">
		        <label><input type="radio" name="use_standard_xmp" value="1" <?php isset($xmp_data['use_standard_xmp']) && ($xmp_data['use_standard_xmp'] == '1') ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Use GTalk for messaging'); ?></label>
				<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Information for your google app')?></h4>	
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Redirect URL, this url you will have to enter in your google app configuration')?></label>
				<input class="form-control" type="text" value="<?php echo erLhcoreClassXMP::getBaseHost(),$_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('xmp/configuration')?>/(gtalkoauth)/true" />
				
				
				<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Enter your app information bellow')?></h4>						
				<div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','OAuth 2.0 Client ID');?></label>
				<input class="form-control" type="text" name="gtalk_client_id" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Please enter your Client ID')?>" value="<?php (isset($xmp_data['gtalk_client_id']) && $xmp_data['gtalk_client_id'] != '') ? print $xmp_data['gtalk_client_id'] : print '' ?>" />
				</div>
				
				<div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Client secret');?></label>
				<input class="form-control" type="text" name="gtalk_client_secret" value="<?php (isset($xmp_data['gtalk_client_secret']) && $xmp_data['gtalk_client_secret'] != '') ? print $xmp_data['gtalk_client_secret'] : print '' ?>" />
				</div>
								
				<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
				
				<?php if (erLhcoreClassXMP::getAccessToken() !== false) : ?>
				<h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Test recipients');?></h3>
			    
			    <div class="form-group">
			    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Please enter to what gmail address test message should be send?');?></label>
			    <input class="form-control" type="text" name="test_recipients_gtalk" value="<?php (isset($test_gmail_email) && $test_gmail_email != '') ? print htmlspecialchars($test_gmail_email) : print '' ?>" />
				</div>			
				<input type="submit" class="btn btn-primary pull-right" name="StoreXMPGTalkSendeMessage" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','This message will be send to test e-mail'); ?>" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Send test message'); ?>" />
				<?php endif;?>
									
				<div class="btn-group" role="group" aria-label="...">
				  <input type="submit" class="btn btn-default" name="StoreXMPGTalkSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />
				  					  
				  <?php if (erLhcoreClassXMP::getAccessToken() !== false) : ?>					  
				  	<input type="submit" class="btn btn-default" name="StoreXMPGTalkRevokeToken" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Revoke access token'); ?>" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Revoke permission to send a message'); ?>" />
				  <?php else : ?>
					  <?php if (isset($xmp_data['gtalk_client_secret']) && (!empty($xmp_data['gtalk_client_secret']))) : ?>
					  		<input type="submit" class="btn btn-default" name="StoreXMPGTalkSettingsTest" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Authentificate and grant permission to send a message'); ?>" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmpp','Grant permission to send a message'); ?>" />
					  <?php endif;?>
				  <?php endif;?>
				</div>
		</div>
	</div>
</div>

	
	

</form>