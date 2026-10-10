<?php

$tpl = erLhcoreClassTemplate::getInstance('lhtheme/import.tpl.php');

if (ezcInputForm::hasPostData()) {
		
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('theme/import');
		exit;
	}
	
	if (erLhcoreClassSearchHandler::isFile('themefile', ['json'])) {

		$dir = 'var/tmpfiles/';
		erLhcoreClassChatEventDispatcher::getInstance()->dispatch('theme.temppath', ['dir' => & $dir]);
		
		erLhcoreClassFileUpload::mkdirRecursive($dir);
		
		$filename = erLhcoreClassSearchHandler::moveUploadedFile('themefile', $dir);
		$content = file_get_contents("{$dir}{$filename}");
		unlink("{$dir}{$filename}");	
		$data = json_decode($content);		
		if ($data !== null) {
			
			$widgetTheme = new erLhAbstractModelWidgetTheme();
			
			$data = (array) $data;
			$imgData = [];
			if (isset($data['logo_image_data'])){
				$imgData['logo_image'] = $data['logo_image_data'];
				unset($data['logo_image_data']);
			}
			
			if (isset($data['need_help_image_data'])){
				$imgData['need_help_image'] = $data['need_help_image_data'];
				unset($data['need_help_image_data']);
			}
			
			if (isset($data['online_image_data'])){
				$imgData['online_image'] = $data['online_image_data'];
				unset($data['online_image_data']);
			}
			
			if (isset($data['offline_image_data'])){
				$imgData['offline_image'] = $data['offline_image_data'];
				unset($data['offline_image_data']);
			}
			
			if (isset($data['copyright_image_data'])){
				$imgData['copyright_image'] = $data['copyright_image_data'];
				unset($data['copyright_image_data']);
			}
			
			if (isset($data['operator_image_data'])){
				$imgData['operator_image'] = $data['operator_image_data'];
				unset($data['operator_image_data']);
			}
			
			if (isset($data['popup_image_data'])){
				$imgData['popup_image'] = $data['popup_image_data'];
				unset($data['popup_image_data']);
			}
			
			if (isset($data['close_image_data'])){
				$imgData['close_image'] = $data['close_image_data'];
				unset($data['close_image_data']);
			}
			
			if (isset($data['restore_image_data'])){
				$imgData['restore_image'] = $data['restore_image_data'];
				unset($data['restore_image_data']);
			}
			
			if (isset($data['minimize_image_data'])){
				$imgData['minimize_image'] = $data['minimize_image_data'];
				unset($data['minimize_image_data']);
			}
			
			try {
				/*
				 * Only attributes defined on the model are importable. Filesystem
				 * paths, image names and pending upload flags are managed solely by
				 * movePhoto()/deletePhoto() and must never come from the uploaded file.
				 */
				$importableAttributes = array_flip(array_keys($widgetTheme->getState()));

				unset(
					$importableAttributes['id'],
					$importableAttributes['modified'],
					$importableAttributes['online_image'],
					$importableAttributes['offline_image'],
					$importableAttributes['logo_image'],
					$importableAttributes['need_help_image'],
					$importableAttributes['copyright_image'],
					$importableAttributes['operator_image'],
					$importableAttributes['minimize_image'],
					$importableAttributes['restore_image'],
					$importableAttributes['close_image'],
					$importableAttributes['popup_image'],
					$importableAttributes['notification_icon']
				);

				foreach (array_keys($importableAttributes) as $importableAttribute) {
					if (substr($importableAttribute, -5) === '_path') {
						unset($importableAttributes[$importableAttribute]);
					}
				}

				$widgetTheme->setState(array_intersect_key($data, $importableAttributes));
				$widgetTheme->saveThis();
	
				foreach ($imgData as $attr => $dataImage) {

					$imgDataItem = base64_decode($dataImage);

					if ($imgDataItem !== false) {

					    /*
					     * Allow upload only images
					     * Security report by https://sentry.co.com
					     */
					    if (in_array($data["{$attr}_data_ext"], ['gif','jpg','jpeg','png','bmp']))
                        {
                            $dir = 'var/tmpfiles/';
                            $fileName = "data.{$data["{$attr}_data_ext"]}";

                            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('theme.temppath', ['dir' => & $dir]);

                            erLhcoreClassFileUpload::mkdirRecursive($dir);

                            $imgPath = "{$dir}{$fileName}";
                            file_put_contents($imgPath, $imgDataItem);

                            if (erLhcoreClassImageConverter::isPhotoLocal($imgPath)){
                                $widgetTheme->movePhoto($attr,true,$imgPath);
                            } else {
                                unlink($imgPath);
                            }
                        }
					}
				}	
	
				$widgetTheme->updateThis();
			} catch (Exception $e) {
				$tpl->set('errors', [erTranslationClassLhTranslation::getInstance()->getTranslation('theme/import','Could not import a new theme!')]);
			}			
		}
		
		$tpl->set('updated',true);
	} else {		
		$tpl->set('errors', [erTranslationClassLhTranslation::getInstance()->getTranslation('theme/import','Invalid file!')]);		
	}
}

$Result['path'] = [
	['url' => erLhcoreClassDesign::baseurl('system/configuration'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit', 'System configuration')],
	['url' => erLhcoreClassDesign::baseurl('theme/index'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index', 'Themes')],
	['title' => erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index', 'Import theme')]
];
$Result['content'] = $tpl->fetch();
