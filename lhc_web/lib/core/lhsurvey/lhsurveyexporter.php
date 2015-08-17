<?php

/**
 * Class used for survey exporter
 * 
 * */
class erLhcoreClassSurveyExporter {

	public static function exportXLS($items)
	{
	    include 'lib/core/lhform/PHPExcel.php';
	    $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
	    $cacheSettings = array( 'memoryCacheSize ' => '64MB');
	    PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
	    
	    $objPHPExcel = new PHPExcel();
	    $objPHPExcel->setActiveSheetIndex(0);
	    $objPHPExcel->getActiveSheet()->getStyle('A1:AW1')->getFont()->setBold(true);
	    $objPHPExcel->getActiveSheet()->setTitle('Report');
	     
	    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1,  erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Chats'));
	    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Department name'));
	    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Operator'));
	    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Stars'));
	    
	    $attributes = array(
	        'virtual_chats_number',
	        'department_name',
	        'user',
	        'average_stars'
	    );
	    
	    $i = 2;
	    foreach ($items as $item) {
	        foreach ($attributes as $key => $attr) {
	            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, (string)$item->{$attr});
	        }
	        $i++;
	    }
	     
	    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	     
	    // We'll be outputting an excel file
	    header('Content-type: application/vnd.ms-excel');
	     
	    // It will be called file.xls
	    header('Content-Disposition: attachment; filename="report.xlsx"');
	     
	    // Write file to the browser
	    $objWriter->save('php://output');
	    
	}	
}

?>