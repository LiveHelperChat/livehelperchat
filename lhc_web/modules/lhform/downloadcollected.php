<?php

$form = erLhAbstractModelForm::fetch((int)$Params['user_parameters']['form_id']);
$items = erLhAbstractModelFormCollected::getList(array('filter' => array('form_id' => $form->id),'offset' => 0, 'limit' => 100000,'sort' => 'id ASC'));

include 'lib/core/lhform/PHPExcel.php';

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
foreach ($items as $item) {	
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
	
	$y++;	
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($y, $i, $item->identifier);
	
	$i++;		
}

$objPHPExcel->getActiveSheet()->setTitle('Report');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

// We'll be outputting an excel file
header('Content-type: application/vnd.ms-excel');

// It will be called file.xls
header('Content-Disposition: attachment; filename="report.xlsx"');

// Write file to the browser
$objWriter->save('php://output');
exit;