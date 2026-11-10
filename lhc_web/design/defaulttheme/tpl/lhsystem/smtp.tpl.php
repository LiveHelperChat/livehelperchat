<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Mail settings');?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post" autocomplete="new-password" ng-non-bindable>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

	<div role="tabpanel">

		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist" data-remember="true">
			<li role="presentation" class="nav-item"><a class="<?php echo (isset($tab) && $tab == 'mailsettings') ? 'active ' : ''; ?>nav-link" href="#mailsettings" aria-controls="mailsettings" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','Mail settings');?></a></li>
			<li role="presentation" class="nav-item"><a class="<?php echo (isset($tab) && $tab == 'SMTP') ? 'active ' : ''; ?>nav-link" href="#SMTP" aria-controls="SMTP" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default','SMTP');?></a></li>
		</ul>


      
		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane <?php echo (isset($tab) && $tab == 'mailsettings') ? 'active' : ''; ?>" id="mailsettings">
				<div class="form-group">
					<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Sender address');?></label> <input type="text" class="form-control" name="sender" value="<?php (isset($smtp_data['sender']) && $smtp_data['sender'] != '') ? print htmlspecialchars($smtp_data['sender']) : print '' ?>" />
				</div>

				<div class="form-group">
					<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Default from e-mail address');?></label> <input type="text" class="form-control" name="default_from" value="<?php (isset($smtp_data['default_from']) && $smtp_data['default_from'] != '') ? print htmlspecialchars($smtp_data['default_from']) : print '' ?>" />
				</div>

				<div class="form-group">
					<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Default from name');?></label> <input type="text" class="form-control" name="default_from_name" value="<?php (isset($smtp_data['default_from_name']) && $smtp_data['default_from_name'] != '') ? print htmlspecialchars($smtp_data['default_from_name']) : print '' ?>" />
				</div>

				<div class="btn-group" role="group" aria-label="...">
					<input type="submit" class="btn btn-secondary" name="StoreMailSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" /> <input type="submit" class="btn btn-secondary" name="StoreMailSettingsTest" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Test'); ?>" />
				</div>
			</div>

			<div role="tabpanel" class="tab-pane <?php echo (isset($tab) && $tab == 'SMTP') ? 'active' : ''; ?>" id="SMTP">
				<label><input type="checkbox" name="use_smtp" value="1" <?php isset($smtp_data['use_smtp']) && ($smtp_data['use_smtp'] == '1') ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','SMTP enabled'); ?></label>

				<div class="form-group">
					<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Login');?></label> <input type="text" class="form-control" name="username" autocomplete="new-password" value="<?php (isset($smtp_data['username']) && $smtp_data['username'] != '') ? print htmlspecialchars($smtp_data['username']) : print '' ?>" />
				</div>

				<div class="form-group">
					<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Password');?></label> <input type="password" class="form-control" name="password" autocomplete="new-password" value="<?php (isset($smtp_data['password']) && $smtp_data['password'] != '') ? print htmlspecialchars($smtp_data['password']) : print '' ?>" />
				</div>

				<div class="form-group">
					<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Host');?>*</label> <input type="text" class="form-control" name="host" value="<?php (isset($smtp_data['host']) && $smtp_data['host'] != '') ? print htmlspecialchars($smtp_data['host']) : print '' ?>" />
					<div class="alert alert-info p-2 fs12 mt-2 mb-0">
						<div class="fw-bold mb-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Host prefix overrides the encryption setting'); ?></div>
						<div><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','If Host begins with ssl:// or tls://, PHPMailer ignores the Encryption selection and the automatic TLS upgrade - ssl://host means implicit SSL, tls://host means mandatory STARTTLS. The prefix must be lowercase, TLS://host is rejected as an invalid hostname.'); ?></div>
					</div>
				</div>

				<div class="form-group">
					<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Port');?>*</label> <input type="text" class="form-control" name="port" value="<?php (isset($smtp_data['port']) && $smtp_data['port'] != '') ? print htmlspecialchars($smtp_data['port']) : print '25' ?>" />
				</div>

				<div class="form-group">
					<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Encryption');?></label>
					<select class="form-control" name="smtp_secure">
						<option value="" <?php echo (isset($smtp_data['smtp_secure']) && $smtp_data['smtp_secure'] == '') ? 'selected="selected"' : '' ?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','None - plain connection');?></option>
						<option value="tls" <?php echo (isset($smtp_data['smtp_secure']) && $smtp_data['smtp_secure'] == 'tls') ? 'selected="selected"' : '' ?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','STARTTLS, usually port 587');?></option>
						<option value="ssl" <?php echo (isset($smtp_data['smtp_secure']) && $smtp_data['smtp_secure'] == 'ssl') ? 'selected="selected"' : '' ?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','SSL/TLS, usually port 465');?></option>
					</select>
				</div>

				<div class="form-group">
					<label><input type="checkbox" name="smtp_auto_tls" value="1" <?php echo (!isset($smtp_data['smtp_auto_tls']) || $smtp_data['smtp_auto_tls'] == '1') ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Automatically upgrade to TLS if SMTP server supports it (SMTPAutoTLS)'); ?></label>
					<div class="fs12 text-muted"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Uncheck for a plain local relay. Otherwise PHPMailer will attempt STARTTLS and sending will fail if the server certificate or TLS version is not acceptable. Has no effect when encryption above is set to STARTTLS or SSL/TLS, or when Host has a tls:// or ssl:// prefix.'); ?></div>
				</div>

				<details class="my-2">
					<summary class="fs14 text-primary" style="cursor:pointer"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Recommended settings per scenario'); ?></summary>
					<table class="table table-sm fs12 mt-2 mb-0">
						<thead>
							<tr>
								<th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Scenario'); ?></th>
								<th>Host</th>
								<th>Port</th>
								<th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Encryption'); ?></th>
								<th>Auto TLS</th>
								<th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Login / Password'); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','External SMTP, submission port (Gmail, Microsoft 365, SES, Mailgun, SendGrid)'); ?></td>
								<td>smtp.example.com</td>
								<td>587</td>
								<td>STARTTLS, usually port 587</td>
								<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','unchecked'); ?></td>
								<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','required'); ?></td>
							</tr>
							<tr>
								<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','External SMTP, implicit SSL (old providers)'); ?></td>
								<td>smtp.example.com</td>
								<td>465</td>
								<td>SSL/TLS, usually port 465</td>
								<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','unchecked'); ?></td>
								<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','required'); ?></td>
							</tr>
							<tr>
								<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','External SMTP, STARTTLS defined by tls:// prefix in Host'); ?></td>
								<td>tls://smtp.example.com</td>
								<td>587</td>
								<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','ignored, the prefix forces STARTTLS'); ?></td>
								<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','ignored'); ?></td>
								<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','required'); ?></td>
							</tr>
							<tr>
								<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','External SMTP, implicit SSL defined by ssl:// prefix in Host'); ?></td>
								<td>ssl://smtp.example.com</td>
								<td>465</td>
								<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','ignored, the prefix forces SSL'); ?></td>
								<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','ignored'); ?></td>
								<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','required'); ?></td>
							</tr>
							<tr>
								<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Local relay, TLS not available (no certificate)'); ?></td>
								<td>127.0.0.1</td>
								<td>25</td>
								<td>None - plain connection</td>
								<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','unchecked'); ?></td>
								<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','not needed'); ?></td>
							</tr>
							<tr>
								<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Local relay, TLS optional'); ?></td>
								<td>127.0.0.1</td>
								<td>25</td>
								<td>None - plain connection</td>
								<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','checked'); ?></td>
								<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','not needed'); ?></td>
							</tr>
							<tr>
								<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Local relay, STARTTLS required'); ?></td>
								<td>127.0.0.1</td>
								<td>587</td>
								<td>STARTTLS, usually port 587</td>
								<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','unchecked'); ?></td>
								<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','as configured'); ?></td>
							</tr>
						</tbody>
					</table>
					<div class="fs12 text-muted mt-2">
						<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','External SMTP: Login is usually the full e-mail address, for Gmail and Microsoft 365 use an application password and set Sender / Default from to the same mailbox, otherwise SPF and DKIM will reject the mail.'); ?><br>
						<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Host prefix: a lowercase tls:// or ssl:// in Host wins over the Encryption and Auto TLS columns above, so the same host can be written either way - keep only one of the two styles, do not combine a prefix with a conflicting Encryption value.'); ?><br>
						<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Local relay without TLS: choose None and leave the automatic upgrade unchecked, otherwise PHPMailer attempts STARTTLS and sending fails when the relay certificate or TLS version is not acceptable.'); ?><br>
						<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','After saving use the Test button to verify the connection and the authentication.'); ?>
					</div>
				</details>

                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Bind IP, multiple IP can be separated by comma. Random IP will be chosen.');?></label> <input type="text" class="form-control" name="bindip" value="<?php (isset($smtp_data['bindip']) && $smtp_data['bindip'] != '') ? print htmlspecialchars($smtp_data['bindip']) : print '' ?>" />
                </div>
            
                <div class="btn-group" role="group" aria-label="...">
					<input type="submit" class="btn btn-secondary" name="StoreSMTPSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" /> <input type="submit" class="btn btn-secondary" name="StoreSMTPSettingsTest" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Test'); ?>" />
				</div>
			</div>
		</div>
	</div>
</form>

<?php if (isset($content_connection)) : ?>
    <div class="my-2 fs14 text-muted"><?php echo $content_connection?></div>
<?php endif; ?>