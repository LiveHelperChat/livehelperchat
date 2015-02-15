<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Languages configuration')?></h1>
<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<div role="tabpanel">

	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">	
	    <?php if ($currentUser->hasAccessTo('lhsystem','changelanguage')) : ?>
		<li role="presentation" <?php if ($tab == '') : ?> class="active" <?php endif;?>><a href="#yourlanguage" aria-controls="yourlanguage" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Your language');?></a></li>
		<?php endif;?>
				
		<?php if ($currentUser->hasAccessTo('lhsystem','configurelanguages')) : ?>
		<li role="presentation" <?php if ($tab == 'generalsettings') : ?> class="active" <?php endif;?>><a href="#generalsettings" aria-controls="generalsettings" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','General language settings');?></a></li>
		<?php endif;?>		
	</ul>

	<div class="tab-content">
	  <?php if ($currentUser->hasAccessTo('lhsystem','changelanguage')) : ?>
	  <div role="tabpanel" class="tab-pane active" id="yourlanguage">
			<form action="<?php echo erLhcoreClassDesign::baseurl('system/languages')?>" method="post">
    				<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
    				
    				<div class="form-group">
        				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Language')?></label> 
        				<select name="language" class="form-control">
        					<?php
                                $userLanguage = erLhcoreClassModelUserSetting::getSetting('user_language', erLhcoreClassSystem::instance()->Language);
                                foreach (erLhcoreClassSiteaccessGenerator::getLanguages() as $language) :
                            ?>
        						<option value="<?php echo $language['locale']?>" <?php $userLanguage == $language['locale'] ? print 'selected="selected"' : ''?>><?php echo $language['locale']?></option>
        					<?php endforeach;?>
        				</select> 
    				</div>
    				
    				<input type="hidden" name="StoreUserSettings" value="1" /> 
    				<input type="submit" class="btn btn-default" name="StoreUserSettingsAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />
			</form>
		</div>
	  <?php endif;?>
	  
	  
	  
	  <?php if ($currentUser->hasAccessTo('lhsystem','configurelanguages')) : ?>
	       <div role="tabpanel" class="tab-pane" id="generalsettings">
			<form action="<?php echo erLhcoreClassDesign::baseurl('system/languages')?>" method="post" name="siteaccess_change">
				<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Current site access')?> - <strong><?php echo $current_site_access ?></strong>
				</p>

				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Site access')?></label> <select id="LocaleID" name="siteaccess" onchange="document.siteaccess_change.submit()" class="form-control">
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
		          <h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','options for')?> (<?php echo $input->siteaccess?>)</h2>
					
					<div class="form-group">
    					<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Language')?></label> 
    					<select name="language" class="form-control">
                			<?php foreach (erLhcoreClassSiteaccessGenerator::getLanguages() as $language) : ?>
                				<option value="<?php echo $language['locale']?>" <?php $siteAccessOptions['locale'] == $language['locale'] ? print 'selected="selected"' : ''?>><?php echo $language['locale']?></option>
                			<?php endforeach;?>
                		</select>
                    </div>
                    
					<div class="form-group">
						<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Theme, separate themes by new line')?></label>
						<textarea class="form-control" name="theme"><?php echo implode("\n", $siteAccessOptions['theme'])?></textarea>
					</div>

					<div class="row form-group">
						<div class="col-md-3">
							<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Default module')?></label> <input type="text" class="form-control" name="module" value="<?php echo $siteAccessOptions['default_url']['module']?>" />
						</div>
						<div class="col-md-3">
							<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/languages','Default view')?></label> <input type="text" class="form-control" name="view" value="<?php echo $siteAccessOptions['default_url']['view']?>" />
						</div>
					</div>
				
				<input type="hidden" name="StoreLanguageSettings" value="1" /> <input type="submit" class="btn btn-default" name="StoreLanguageSettingsAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

			</form>

		</div>
	  <?php endif;?>	  
	</div>

</div>