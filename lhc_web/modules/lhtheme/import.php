<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhtheme/import.tpl.php' );

if (ezcInputForm::hasPostData()) {
		
	if (erLhcoreClassSearchHandler::isFile('themefile',array('json'))) {

		$dir = 'var/tmpfiles/';
		erLhcoreClassChatEventDispatcher::getInstance()->dispatch('theme.temppath',array('dir' => & $dir));
		
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
			
			try {
				$widgetTheme->setState($data);
				$widgetTheme->saveThis();
	
				foreach ($imgData as $attr => $dataImage) {
					$imgDataItem = base64_decode($dataImage);
					if ($imgDataItem !== false) {		
									
						$dir = 'var/tmpfiles/';
						$fileName = 'data.'.$data[$attr.'_data_ext'];
												
						erLhcoreClassChatEventDispatcher::getInstance()->dispatch('theme.temppath',array('dir' => & $dir));

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