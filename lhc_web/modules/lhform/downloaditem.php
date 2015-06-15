<?php

try {
	$item = erLhAbstractModelFormCollected::fetch((int)$Params['user_parameters']['collected_id']);
			
	$form = $item->form;
	
	include 'lib/core/lhform/PHPExcel.php';


	$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
	$cacheSettings = array( 'memoryCacheSize ' => '64MB');
	PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

	$objPHPExcel = new PHPExcel();
	
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->getStyle('A1:AW1')->getFont()->setBold(true);
	
	// Set width
	foreach ($form->xls_columns_data as $key => $data) {
		if (isset($data['width'])) {
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($key)->setWidth($data['width']);
		}
	}
			
	// Set header
	$i = 0;
	foreach ($form->xls_columns_data as $data) {
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, 1, $data['name']);
		$i++;
	}
	
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('form/index','Date'));
	$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, 1, 'IP');
	
	$i++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('form/index','Identifier'));
		
	// Set data
	$i = 2;	
	$y = 0;
	foreach ($form->xls_columns_data as $data) {
		if (isset($item->content_array[$data['attr_name']]['definition']['type']) && $item->content_array[$data['attr_name']]['definition']['type'] == 'file') {
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($y, $i, erLhcoreClassXMP::getBaseHost() . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurldirect('user/login').'/(r)/'.rawurlencode(base64_encode('form/download/'.$item->id.'/'.$data['attr_name'])));
		} else {
			if (strpos($data['attr_name'], ',') === false){
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($y, $i, $item->content_array[$data['attr_name']]['value']);
			} else {
				$nameParts = explode(',', $data['attr_name']);
				$valuesPart = array();
				foreach ($nameParts as $part) {
					$valuesPart[] = isset($item->content_array[$part]['value']) ? $item->content_array[$part]['value'] : $part;
				}
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($y, $i, implode('', $valuesPart));
			}
		}
		$y++;
	}

	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($y, $i, $item->ctime_full_front);
	$y++;
	
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($y, $i, $item->ip);	
	$i++;
	
	$y++;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($y, $i, $item->identifier);	
	$i++;
	
	$objPHPExcel->getActiveSheet()->setTitle('Report');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			
	$objWriter->save('var/storageform/'.$item->id.'-report.xlsx');
			
	$zip = new ZipArchive();
	$zip->open('var/storageform/'.$item->id.'-temp.zip',ZIPARCHIVE::OVERWRITE);
	$zip->addFile('var/storageform/'.$item->id.'-report.xlsx','report.xlsx');
			
	foreach ($item->content_array as $key => $content) {
		if ($content['definition']['type'] == 'file') {				
			$array = explode('.',$content['value']['name']);
			$ext = end($array);

			$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('form.file.download', array('filename' => $content['filename']));
			
			// There was no callbacks or file not found etc, we try to download from standard location
			if ($response === false) {
				$zip->addFile( $content['filepath'] . $content['filename'],$key.'.'.$ext);
			} else {
				$zip->addFromString($key.'.'.$ext, $response['filedata']);				
			}
		}			
	} 
		
	$zip->close();
			
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=\"export-{$item->id}.zip\"");
	echo file_get_contents('var/storageform/'.$item->id.'-temp.zip');
	unlink('var/storageform/'.$item->id.'-temp.zip');
	unlink('var/storageform/'.$item->id.'-report.xlsx');
	exit;	

} catch (Exception $e) {
	header('Location: /');
	exit;
}
exit;

?>