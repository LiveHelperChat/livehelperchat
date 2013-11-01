<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Languages configuration')?></h1>
<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>


<div class="section-container auto" data-section>

  <?php if ($currentUser->hasAccessTo('lhsystem','changelanguage')) : ?>
  <section <?php if ($tab == '') : ?>class="active"<?php endif;?>>
	<p class="title" data-section-title><a href="#panel1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Your language');?></a></p>
	<div class="content" data-section-content>
		<div>
			<form action="<?php echo erLhcoreClassDesign::baseurl('system/languages')?>" method="post">
				<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Language')?></label>
				<select name="language">
					<?php
					$userLanguage = erLhcoreClassModelUserSetting::getSetting('user_language',erLhcoreClassSystem::instance()->Language);
					foreach (erLhcoreClassSiteaccessGenerator::getLanguages() as $language) : ?>
						<option value="<?php echo $language['locale']?>" <?php $userLanguage == $language['locale'] ? print 'selected="selected"' : ''?>><?php echo $language['locale']?></option>
					<?php endforeach;?>
				</select>

				<input type="hidden" name="StoreUserSettings" value="1" />
				<input type="submit" class="button small round" name="StoreUserSettingsAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />
			</form>
		</div>
	</div>
  </section>
  <?php endif; ?>

  <?php if ($currentUser->hasAccessTo('lhsystem','configurelanguages')) : ?>
  <section <?php if ($tab == 'generalsettings') : ?>class="active"<?php endif;?>>
    <p class="title" data-section-title><a href="#panel1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','General language settings');?></a></p>
    <div class="content" data-section-content>

		<form action="<?php echo erLhcoreClassDesign::baseurl('system/languages')?>" method="post" name="siteaccess_change">
			<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Current site access')?> - <strong><?php echo $current_site_access ?></strong></p>
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Site access')?></label>
			<select id="LocaleID" name="siteaccess" onchange="document.siteaccess_change.submit()">
				<?php foreach ($locales as $locale) : ?>
				      <option value="<?php echo $locale?>" <?php $input->siteaccess == $locale ? print 'selected="selected"' : ''?>><?php echo $locale?></option>
				<?php endforeach; ?>
			</select>

			<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

			<input type="hidden" name="changeSiteAccess" value="1" />
		</form>

		<form action="<?php echo erLhcoreClassDesign::baseurl('system/languages')?>" method="post" name="siteaccess">

		<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

		<input type="hidden" name="siteaccess" value="<?php echo $input->siteaccess?>" />

		<?php $siteAccessOptions = erConfigClassLhConfig::getInstance()->getSetting( 'site_access_options', $input->siteaccess ); ?>
		<fieldset><legend><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','options for')?> (<?php echo $input->siteaccess?>)</legend>
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Language')?></label>
		<select name="language">
			<?php foreach (erLhcoreClassSiteaccessGenerator::getLanguages() as $language) : ?>
				<option value="<?php echo $language['locale']?>" <?php $siteAccessOptions['locale'] == $language['locale'] ? print 'selected="selected"' : ''?>><?php echo $language['locale']?></option>
			<?php endforeach;?>
		</select>

		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Theme, separate themes by new line')?></label>
		<textarea name="theme" class="small-12"><?php echo implode("\n", $siteAccessOptions['theme'])?></textarea>

		<div class="row">
			<div class="columns small-6">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Default module')?></label>
				<input type="text" name="module" value="<?php echo $siteAccessOptions['default_url']['module']?>" />
			</div>
			<div class="columns small-6">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Default view')?></label>
				<input type="text" name="view" value="<?php echo $siteAccessOptions['default_url']['view']?>" />
			</div>
		</div>
		</fieldset>

		<input type="hidden" name="StoreLanguageSettings" value="1" />
		<input type="submit" class="button small round" name="StoreLanguageSettingsAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

		</form>

	</div>
</section>
<?php endif;?>

</div>

