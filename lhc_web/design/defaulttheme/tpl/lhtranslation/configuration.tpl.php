<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Translation settings'); ?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php')); ?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php')); ?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('translation/configuration') ?>" method="post" autocomplete="off" ng-show="enable_translations">

	<div class="row">
		<div class="col-4">
			<div class="form-group" ng-non-bindable>
				<textarea class="form-control" name="DetectLanguageText" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Enter text for testing purposes') ?>"><?php isset($_POST['DetectLanguageText']) ? print htmlspecialchars($_POST['DetectLanguageText']) : '' ?></textarea>
			</div>
		</div>
		<div class="col-4">
			<?php echo erLhcoreClassRenderHelper::renderCombobox(array(
				'input_name'     => 'LanguageTo',
				'css_class'      => 'form-control',
				'selected_id'    => '',
				'list_function'  => 'erLhcoreClassTranslate::getSupportedLanguages'
			)); ?>

		</div>
		<div class="col-4" ng-non-bindable>
			<?php if (isset($translated_text)) : ?>
				<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Translated text') ?></h5>
				<p><b><?php echo htmlspecialchars($translated_text) ?></b></p>
			<?php endif; ?>

			<?php if (isset($detected_language)) : ?>
				<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Detected language') ?> - <b><?php echo htmlspecialchars($detected_language) ?></b></p>
			<?php endif; ?>
		</div>
	</div>

	<div class="form-group">
		<div class="btn-group" role="group" aria-label="...">
			<input type="submit" class="btn btn-secondary" name="DetectLanguage" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Detect language'); ?>" />
			<input type="submit" class="btn btn-secondary" name="TranslateToLanguage" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Translate'); ?>" />
		</div>
	</div>

</form>

<form action="<?php echo erLhcoreClassDesign::baseurl('translation/configuration') ?>" method="post" autocomplete="off">

	<div class="row">
		<div class="col-6">

			<div class="form-group">
				<label><input <?php if (isset($translation_data['use_cache']) && $translation_data['use_cache'] == true) : ?>checked="checked" <?php endif; ?> type="checkbox" name="use_cache" value="on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Enable translation response cache'); ?>
					<i>
						(<?php echo erLhcoreClassModelGenericBotRestAPICache::getCount(['filter' => ['rest_api_id' => 0]]) ?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'cache items'); ?>)</i>
					<a class="csfr-required csfr-post btn btn-xs btn-danger" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('translation/configuration') ?>/(action)/clearcache"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Clear cache'); ?></a>
				</label>
			</div>

			<div class="form-group">
				<label><input ng-init="enable_translations=<?php if (isset($translation_data['enable_translations']) && $translation_data['enable_translations'] == true) : ?>true<?php else : ?>false<?php endif; ?>" type="checkbox" ng-model="enable_translations" name="enable_translations" value="on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Enable translation service'); ?></label>
			</div>



			<input type="submit" ng-show="!enable_translations" class="btn btn-secondary" name="StoreLanguageSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons', 'Save'); ?>" />

			<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php')); ?>

			<div role="tabpanel" ng-show="enable_translations">
				<ul class="nav nav-tabs" role="tablist" data-remember="true">
					<li role="presentation" class="nav-item"><a class="nav-link <?php if ((isset($translation_data['translation_handler']) && $translation_data['translation_handler'] == 'bing')) : ?>active<?php endif; ?>" href="#bing" aria-controls="bing" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Bing'); ?></a></li>
					<li role="presentation" class="nav-item"><a class="nav-link <?php if (isset($translation_data['translation_handler']) && $translation_data['translation_handler'] == 'google') : ?>active<?php endif; ?>" href="#google" aria-controls="google" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Google'); ?></a></li>
					<li role="presentation" class="nav-item"><a class="nav-link <?php if (isset($translation_data['translation_handler']) && $translation_data['translation_handler'] == 'yandex') : ?>active<?php endif; ?>" href="#yandex" aria-controls="yandex" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Yandex'); ?></a></li>
					<li role="presentation" class="nav-item"><a class="nav-link <?php if (isset($translation_data['translation_handler']) && $translation_data['translation_handler'] == 'aws') : ?>active<?php endif; ?>" href="#aws" aria-controls="aws" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'AWS'); ?></a></li>
					<li role="presentation" class="nav-item"><a class="nav-link <?php if (isset($translation_data['translation_handler']) && $translation_data['translation_handler'] == 'deepl') : ?>active<?php endif; ?>" href="#deepl" aria-controls="deepl" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'DeepL'); ?></a></li>
				</ul>

				<div class="tab-content">
					<div role="tabpanel" class="tab-pane pt-2 <?php if ((isset($translation_data['translation_handler']) && $translation_data['translation_handler'] == 'bing')) : ?>active<?php endif; ?>" id="bing">
						<label><input type="radio" name="translation_handler" value="bing" <?php ((isset($translation_data['translation_handler']) && $translation_data['translation_handler'] == 'bing')) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Use Bing service'); ?></label>

						<?php
						$regions = array(
							"australiaeast",
							"brazilsouth",
							"canadacentral",
							"centralindia",
							"centralus",
							"centraluseuap",
							"eastasia",
							"eastus",
							"eastus2",
							"francecentral",
							"japaneast",
							"japanwest",
							"koreacentral",
							"northcentralus",
							"northeurope",
							"southcentralus",
							"southeastasia",
							"uksouth",
							"westcentralus",
							"westeurope",
							"westus",
							"westus2",
							"southafricanorth"
						);
						?>
						<div class="form-group">
							<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Select Region'); ?></label>
							<select ng-non-bindable name="bing_region" class="form-control">
								<?php
								if (!isset($translation_data['bing_region']))
									echo '<option value="0" selected="selected">Select Region</option>';
								foreach ($regions as $region) {
									if (isset($translation_data['bing_region']) && $region === $translation_data['bing_region'])
										echo "<option value=\"$region\" selected=\"selected\">$region</option>";
									else
										echo "<option value=\"$region\">$region</option>";
								}
								?>
							</select>
						</div>

						<div class="form-group" ng-non-bindable>
							<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Bing client secret, key is never shown for security purposes'); ?></label>
							<input class="form-control" type="text" name="bing_client_secret" value="" />
						</div>

						<div class="form-group" ng-non-bindable>
							<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Bing access token expires'); ?></label>
							<input class="form-control" disabled type="text" value="<?php (isset($translation_data['bing_access_token_expire']) && $translation_data['bing_access_token_expire'] != '') ? print htmlspecialchars(date('H:i:s', $translation_data['bing_access_token_expire'])) : print '' ?>" />
						</div>

						<div class="btn-group" role="group" aria-label="..." ng-non-bindable>
							<input type="submit" class="btn btn-secondary" name="StoreLanguageSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons', 'Save'); ?>" />
						</div>
					</div>
					<div role="tabpanel" class="tab-pane pt-2 <?php if (isset($translation_data['translation_handler']) && $translation_data['translation_handler'] == 'google') : ?>active<?php endif; ?>" id="google">
						<label><input type="radio" name="translation_handler" value="google" <?php ((isset($translation_data['translation_handler']) && $translation_data['translation_handler'] == 'google')) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Use Google service'); ?></label>

						<div class="form-group">
							<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'API key, key is never shown for security purposes'); ?></label>
							<input class="form-control" type="text" name="google_api_key" value="" />
						</div>

						<div class="form-group">
							<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Set referrer, usefull if want to limit calls to specific referrer.'); ?></label>
							<input ng-non-bindable class="form-control" type="text" name="google_referrer" value="<?php (isset($translation_data['google_referrer']) && $translation_data['google_referrer'] != '') ? print htmlspecialchars($translation_data['google_referrer']) : print '' ?>" />
						</div>

						<div class="btn-group" role="group" aria-label="...">
							<input type="submit" class="btn btn-secondary" name="StoreLanguageSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons', 'Save'); ?>" />
						</div>
					</div>
					<div role="tabpanel" class="tab-pane pt-2 <?php if (isset($translation_data['translation_handler']) && $translation_data['translation_handler'] == 'aws') : ?>active<?php endif; ?>" id="aws">
						<label><input type="radio" name="translation_handler" value="aws" <?php ((isset($translation_data['translation_handler']) && $translation_data['translation_handler'] == 'aws')) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Use AWS'); ?></label>

						<div class="form-group">
							<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'AWS Region'); ?></label>
							<input class="form-control" type="text" name="aws_region" value="<?php (isset($translation_data['aws_region']) && $translation_data['aws_region'] != '') ? print htmlspecialchars($translation_data['aws_region']) : print 'eu-central-1' ?>" />
						</div>

						<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons', 'Below saved data is not shown.'); ?></p>

						<div class="form-group">
							<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'AWS Access Key'); ?></label>
							<input class="form-control" type="text" name="aws_access_key" value="" />
						</div>

						<div class="form-group">
							<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'AWS Secret Key'); ?></label>
							<input ng-non-bindable class="form-control" type="text" name="aws_secret_key" value="" />
						</div>

						<div class="btn-group" role="group" aria-label="...">
							<input type="submit" class="btn btn-secondary" name="StoreLanguageSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons', 'Save'); ?>" />
						</div>
					</div>
					<div role="tabpanel" class="tab-pane pt-2 <?php if (isset($translation_data['translation_handler']) && $translation_data['translation_handler'] == 'yandex') : ?>active<?php endif; ?>" id="yandex">
						<label><input type="radio" name="translation_handler" value="yandex" <?php ((isset($translation_data['translation_handler']) && $translation_data['translation_handler'] == 'yandex')) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Use Yandex service'); ?></label>

						<div class="form-group">
							<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'API key, key is never shown for security purposes'); ?></label>
							<input ng-non-bindable class="form-control" type="text" name="yandex_api_key" value="" />
						</div>

						<div class="btn-group" role="group" aria-label="...">
							<input type="submit" class="btn btn-secondary" name="StoreLanguageSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons', 'Save'); ?>" />
						</div>
					</div>
					<div role="tabpanel" class="tab-pane pt-2 <?php if (isset($translation_data['translation_handler']) && $translation_data['translation_handler'] == 'deepl') : ?>active<?php endif; ?>" id="deepl">
						<label><input type="radio" name="translation_handler" value="deepl" <?php ((isset($translation_data['translation_handler']) && $translation_data['translation_handler'] == 'deepl')) ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Use DeepL service'); ?></label>

						<div class="form-group">
							<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'API key, key is never shown for security purposes'); ?></label>
							<input ng-non-bindable class="form-control" type="text" name="deepl_api_key" value="" />
						</div>

						<div class="btn-group" role="group" aria-label="...">
							<input type="submit" class="btn btn-secondary" name="StoreLanguageSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons', 'Save'); ?>" />
						</div>
					</div>
				</div>
			</div>


		</div>
		<div class="col-6">

			<div class="form-group">
				<label><input <?php if (isset($translation_data['hide_translate_button']) && $translation_data['hide_translate_button'] == true) : ?>checked="checked" <?php endif; ?> type="checkbox" name="hide_translate_button" value="on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Hide translate single message button.'); ?> <i class="material-icons me-0">language</i></label><br>
			</div>

			<div class="form-group">
				<label><input <?php if (isset($translation_data['show_auto_translate']) && $translation_data['show_auto_translate'] == true) : ?>checked="checked" <?php endif; ?> type="checkbox" name="show_auto_translate" value="on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation', 'Show start translations button'); ?> <i class="material-icons me-0">translate</i></label><br>
			</div>

			<h6 class="ps-4"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Action options');?></h4>
			<div class="form-group ps-4">
				<label><input <?php if (isset($translation_data['translate_old_msg']) && $translation_data['translate_old_msg'] == true) : ?>checked="checked" <?php endif; ?> type="checkbox" name="translate_old_msg" value="on"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','On save automatically translate old chat messages. If not checked only new messages will be translated.');?> </label>
			</div>

		</div>
	</div>



</form>