<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post" autocomplete="off">

	<div role="tabpanel">

		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#autologinsettings" aria-controls="autologinsettings" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Auto login settings');?></a></li>	
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="autologinsettings">
			
			     <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','This module can be used if you are generating autologin link. See site for code examples')?></p>
			
				<div class="form-group">
					<label><input type="checkbox" name="enabled" value="on" <?php (isset($autologin_data['enabled']) && $autologin_data['enabled'] == 1) ? print 'checked="checked"' : print '' ?>" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Enabled');?></label> 
				</div>
				
				<div class="form-group">
					<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('users/autologin','Secret hash used for authentification token generation, min 10 characters');?></label> 
					<input type="text" class="form-control" name="secret_hash" value="<?php (isset($autologin_data['secret_hash']) && $autologin_data['secret_hash'] != '') ? print htmlspecialchars($autologin_data['secret_hash']) : print '' ?>" />
				</div>
				
				<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
				 
				<div class="btn-group" role="group" aria-label="...">
					<input type="submit" class="btn btn-default" name="StoreAutologinSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />
				</div>
			</div>

		</div>
	</div>
</form>