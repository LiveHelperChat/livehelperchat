<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Languages configuration')?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>



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