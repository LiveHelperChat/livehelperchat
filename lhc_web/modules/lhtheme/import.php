<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhtheme/import.tpl.php' );

if (ezcInputForm::hasPostData()) {
		
	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('theme/import');
		exit;
	}
	
	if (erLhcoreClassSearchHandler::isFile('themefile',array('json'))) {

		$dir = 'var/tmpfiles/';
		erLhcoreClassChatEventDispatcher::getInstance()->dispatch('theme.temppath',array('dir' => & $dir));
		
		erLhcoreClassFileUpload::mkdirRecursive( $dir );
		
		$filename = erLhcoreClassSearchHandler::moveUploadedFile('themefile',$dir);
		$content = file_get_contents($dir . $filename);
		unlink($dir . $filename);	
		$data = json_decode($content);		
		if ($data !== null) {
			
			$widgetTheme = new erLhAbstractModelWidgetTheme();
			
			$data = (array)$data;
			$imgData = array();
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
				$widgetTheme->setState($data);
				$widgetTheme->saveThis();
	
				foreach ($imgData as $attr => $dataImage) {
					$imgDataItem = base64_decode($dataImage);
					if ($imgDataItem !== false) {		
									
						$dir = 'var/tmpfiles/';
						$fileName = 'data.'.$data[$attr.'_data_ext'];
												
						erLhcoreClassChatEventDispatcher::getInstance()->dispatch('theme.temppath',array('dir' => & $dir));

						erLhcoreClassFileUpload::mkdirRecursive( $dir );
						
						$imgPath = $dir . $fileName;
						file_put_contents($imgPath, $imgDataItem);
						
						if (erLhcoreClassImageConverter::isPhotoLocal($imgPath)){
							$widgetTheme->movePhoto($attr,true,$imgPath);	
						}				
					}
				}	
	
				$widgetTheme->updateThis();
			} catch (Exception $e) {
				$tpl->set('errors',array(erTranslationClassLhTranslation::getInstance()->getTranslation('theme/import','Could not import a new theme!')));
			}			
		}
		
		$tpl->set('updated',true);
	} else {		
		$tpl->set('errors',array(erTranslationClassLhTranslation::getInstance()->getTranslation('theme/import','Invalid file!')));		
	}
}

$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('theme/index'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Themes')),array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Import theme')));
$Result['content'] = $tpl->fetch();