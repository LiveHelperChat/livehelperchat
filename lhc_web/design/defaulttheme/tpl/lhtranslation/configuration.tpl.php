<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Translation settings');?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('translation/configuration')?>" method="post" autocomplete="off" ng-show="enable_translations">
    
    <div class="row">
        <div class="col-xs-4">
            <div class="form-group">
                <textarea class="form-control" name="DetectLanguageText" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Enter text for testing purposes')?>"><?php isset($_POST['DetectLanguageText']) ? print htmlspecialchars($_POST['DetectLanguageText']) : ''?></textarea>
            </div>
        </div>
        <div class="col-xs-4">
            <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                    'input_name'     => 'LanguageTo',  
    	            'css_class'      => 'form-control',
                    'selected_id'    => '',
                    'list_function'  => 'erLhcoreClassTranslate::getSupportedLanguages'
            )); ?>
              
        </div>
        <div class="col-xs-4">
            <?php if (isset($translated_text)) : ?>
                <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Translated text')?></h5>
                <p><b><?php echo htmlspecialchars($translated_text)?></b></p>
            <?php endif;?>
            
            <?php if (isset($detected_language)) : ?>
                <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Detected language')?> - <b><?php echo htmlspecialchars($detected_language)?></b></p>
            <?php endif;?>
        </div>
    </div>
    
    <div class="form-group">
        <div class="btn-group" role="group" aria-label="...">
            <input type="submit" class="btn btn-default" name="DetectLanguage" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Detect language'); ?>" />
            <input type="submit" class="btn btn-default" name="TranslateToLanguage" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Translate'); ?>" />
        </div>
    </div>
    
</form>

<form action="<?php echo erLhcoreClassDesign::baseurl('translation/configuration')?>" method="post" autocomplete="off">

<div class="form-group">
    <label><input ng-init="enable_translations=<?php if (isset($translation_data['enable_translations']) && $translation_data['enable_translations'] == true ) : ?>true<?php else : ?>false<?php endif;?>" type="checkbox" ng-model="enable_translations" name="enable_translations" value="on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Enable translation service');?></label>
</div>
 
<input type="submit" ng-show="!enable_translations" class="btn btn-default" name="StoreLanguageSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

<div role="tabpanel" ng-show="enable_translations">
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="<?php if ( (isset($translation_data['translation_handler']) && $translation_data['translation_handler'] == 'bing') ) : ?>active<?php endif;?>"><a href="#bing" aria-controls="bing" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Bing');?></a></li>
		<li role="presentation" <?php if (isset($translation_data['translation_handler']) && $translation_data['translation_handler'] == 'google' ) : ?>class="active"<?php endif;?>><a href="#google" aria-controls="google" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Google');?></a></li>
	</ul>

	<div class="tab-content">
		<div role="tabpanel" class="tab-pane <?php if ( (isset($translation_data['translation_handler']) && $translation_data['translation_handler'] == 'bing') ) : ?>active<?php endif;?>" id="bing">
		        <label><input type="radio" name="translation_handler" value="bing" <?php ( (isset($translation_data['translation_handler']) && $translation_data['translation_handler'] == 'bing') ) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Use Bing service'); ?></label>
		              
				<div class="form-group">
					<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Bing client ID');?></label>
					<input class="form-control" type="text" name="bing_client_id" value="<?php (isset($translation_data['bing_client_id']) && $translation_data['bing_client_id'] != '') ? print htmlspecialchars($translation_data['bing_client_id']) : print '' ?>" />
				</div>
				
				<div class="form-group">
					<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Bing client secret, key is never shown for security purposes');?></label>
					<input class="form-control" type="text" name="bing_client_secret" value="" />
				</div>
				
				<div class="form-group">
					<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Bing access token expires');?></label>
					<input class="form-control" disabled type="text" value="<?php (isset($translation_data['bing_access_token_expire']) && $translation_data['bing_access_token_expire'] != '') ? print htmlspecialchars(date('H:i:s',$translation_data['bing_access_token_expire'])) : print '' ?>" />
				</div>
											
				<div class="btn-group" role="group" aria-label="...">
				  <input type="submit" class="btn btn-default" name="StoreLanguageSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />
				</div>				
		</div>
		<div role="tabpanel" class="tab-pane <?php if (isset($translation_data['translation_handler']) && $translation_data['translation_handler'] == 'google' ) : ?>active<?php endif;?>" id="google">
		        <label><input type="radio" name="translation_handler" value="google" <?php ( (isset($translation_data['translation_handler']) && $translation_data['translation_handler'] == 'google') ) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Use Google service'); ?></label>
								
				<div class="form-group">
				    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','API key, key is never shown for security purposes');?></label>
				    <input class="form-control" type="text" name="google_api_key" value="" />
				</div>
																
				<div class="btn-group" role="group" aria-label="...">
				    <input type="submit" class="btn btn-default" name="StoreLanguageSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />
				</div>
		</div>
	</div>
</div>
	
	

</form>