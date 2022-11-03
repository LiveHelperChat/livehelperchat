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

    public static function exportXLSList($items, $survey)
    {
        include 'lib/core/lhform/PHPExcel.php';
        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array( 'memoryCacheSize ' => '64MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        include(erLhcoreClassDesign::designtpl('lhsurvey/forms/fields_names.tpl.php'));
        include(erLhcoreClassDesign::designtpl('lhsurvey/forms/fields_names_enabled.tpl.php'));

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getStyle('A1:AW1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setTitle('Report');

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Survey ID'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Chat'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Department name'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Operator'));

        $column = 5;

        foreach ($starFields as $starField) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $starField);
            $column++;
        }

        foreach ($enabledOptions as $optionField) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $optionField);
            $column++;
        }

        foreach ($enabledOptionsPlain as $optionFieldPlain) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $optionFieldPlain);
            $column++;
        }

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Time'));
        $column++;

        $rows = 2;
        foreach ($items as $item) {

            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rows, $survey->id);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rows, $item->chat_id);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rows, $item->department_name);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rows, $item->user->name_official);

            $column = 5;

            foreach ($enabledStars as $n) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rows, $item->{'max_stars_' . $n});
                $column++;
            }

            foreach ($enabledFields as $enabledField) {
                $options = $survey->{'question_options_' . $enabledField . '_items_front'};
                if (isset($options[$item->{'question_options_' . $enabledField}-1])) {
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rows, strip_tags(erLhcoreClassSurveyValidator::parseAnswer($options[$item->{'question_options_' . $enabledField}-1]['option'])));
                } else {
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rows, strip_tags(erLhcoreClassSurveyValidator::parseAnswer($item->{'question_options_' . $enabledField})));
                }
                $column++;
            }

            foreach ($enabledFieldsPlain as $enabledFieldPlain) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rows, $item->{'question_plain_' . $enabledFieldPlain});
                $column++;
            }

            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, $rows, $item->ftime_front);
            $rows++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        // We'll be outputting an excel file
        header('Content-type: application/vnd.ms-excel');

        // It will be called file.xls
        header('Content-Disposition: attachment; filename="report.xlsx"');

        // Write file to the browser
        $objWriter->save('php://output');
    }

    /**
     * Export raw chat data
     */
    public static function exportRAW($items, $survey)
    {
        include(erLhcoreClassDesign::designtpl('lhsurvey/forms/fields_names.tpl.php'));
        include(erLhcoreClassDesign::designtpl('lhsurvey/forms/fields_names_enabled.tpl.php'));

        $dataValue = array();
        $namesValue = array();

        foreach ($starFields as $starField) {
            $namesValue[] = $starField;
        }

        foreach ($enabledOptions as $optionField) {
            $namesValue[] = $optionField;
        }

        foreach ($enabledOptionsPlain as $optionFieldPlain) {
            $namesValue[] = $optionFieldPlain;
        }


        foreach ($items as $item) {
            $valueItem = array();
            foreach ($enabledStars as $n) {
                $valueItem[] = $item->{'max_stars_' . $n};
            }

            foreach ($enabledFields as $enabledField) {
                $options = $survey->{'question_options_' . $enabledField . '_items_front'};
                if (isset($options[$item->{'question_options_' . $enabledField}-1])) {
                    $valueItem[] = strip_tags(erLhcoreClassSurveyValidator::parseAnswer($options[$item->{'question_options_' . $enabledField}-1]['option']));
                } else {
                    $valueItem[] = strip_tags(erLhcoreClassSurveyValidator::parseAnswer($item->{'question_options_' . $enabledField}));
                }
            }

            foreach ($enabledFieldsPlain as $enabledFieldPlain) {
                $valueItem[] = $item->{'question_plain_' . $enabledFieldPlain};
            }

            $dataValue[$item->chat_id] = $valueItem;
        }

        return array(
            'title' => $namesValue,
            'value' => $dataValue,
        );
    }

    public static function exportJSON($items, $survey, $format = null) {

	    $rows = array();

        include(erLhcoreClassDesign::designtpl('lhsurvey/forms/fields_names.tpl.php'));
        include(erLhcoreClassDesign::designtpl('lhsurvey/forms/fields_names_enabled.tpl.php'));

        $header = array();
        foreach ($starFields as $key => $starField) {
            $header['max_stars_' . ($key + 1)] = $starField;
        }

        foreach ($enabledOptions as $key => $optionField) {
            $header['question_options_' . ($key + 1)] = $optionField;
        }

        foreach ($enabledOptionsPlain as $key => $optionFieldPlain) {
            $header['question_plain_' .  ($key + 1)] = $optionFieldPlain;
        }

        $header['survey'] = $survey->name;
        $header['survey_id'] = $survey->id;

        foreach ($items as $item) {
            $row = array();
            $row['chat'] = $item->chat_id;
            $row['department'] = $item->department_name;
            $row['department_id'] = $item->dep_id;
            $row['user'] = $item->user->name_official;
            $row['user_id'] = $item->user_id;

            foreach ($enabledStars as $n) {
                $row['max_stars_' . $n] = $item->{'max_stars_' . $n};
            }

            foreach ($enabledFields as $enabledField) {
                $options = $survey->{'question_options_' . $enabledField . '_items_front'};
                if (isset($options[$item->{'question_options_' . $enabledField}-1])) {
                    $row['question_options_' . $enabledField] = strip_tags(erLhcoreClassSurveyValidator::parseAnswer($options[$item->{'question_options_' . $enabledField}-1]['option']));
                } else {
                    $row['question_options_' . $enabledField] = strip_tags(erLhcoreClassSurveyValidator::parseAnswer($item->{'question_options_' . $enabledField}));
                }
            }

            foreach ($enabledFieldsPlain as $enabledFieldPlain) {
                $row['question_plain_' . $enabledFieldPlain] = $item->{'question_plain_' . $enabledFieldPlain};
            }

            $row['time'] = $item->ftime;

            $rows[] = $row;
        }

        if ($format === null || $format === 'json') {
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="report.json"');
            erLhcoreClassRestAPIHandler::outputResponse(array('header' => $header, 'items' => $rows), $format);
        } elseif ($format === 'raw') {
            return $rows;
        } else {
            header('Content-Type: text/xml');
            header('Content-Disposition: attachment; filename="report.xml"');
            erLhcoreClassRestAPIHandler::outputResponse(array('header' => $header, 'items' => $rows), $format);
        }
    }

    public static function exportCSV($filter, $survey) {

        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename=report.csv");
        header("Content-Transfer-Encoding: binary");

        $df = fopen("php://output", 'w');

        include(erLhcoreClassDesign::designtpl('lhsurvey/forms/fields_names.tpl.php'));
        include(erLhcoreClassDesign::designtpl('lhsurvey/forms/fields_names_enabled.tpl.php'));

        $firstRow = [
            erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Survey ID'),
            erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Chat'),
            erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Department name'),
            erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Operator')
        ];

        foreach ($starFields as $starField) {
            $firstRow[] = $starField;
        }

        foreach ($enabledOptions as $optionField) {
            $firstRow[] = $optionField;
        }

        foreach ($enabledOptionsPlain as $optionFieldPlain) {
            $firstRow[] = $optionFieldPlain;
        }

        $firstRow[] = erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Time');
        
        // First row
        fputcsv($df, $firstRow);

        $chunks = ceil(erLhAbstractModelSurveyItem::getCount($filter)/300);

        for($i = 0; $i < $chunks; $i ++) {
            $filterChunk = $filter;
            $filterChunk['offset'] = $i * 300;
            $filterChunk['limit'] = 300;

            foreach (erLhAbstractModelSurveyItem::getList($filterChunk) as $item) {
                $row = [];
                $row[] = $survey->id;
                $row[] = $item->chat_id;
                $row[] = $item->department_name;
                $row[] = $item->user->name_official;

                foreach ($enabledStars as $n) {
                    $row[] = $item->{'max_stars_' . $n};
                }

                foreach ($enabledFields as $enabledField) {
                    $options = $survey->{'question_options_' . $enabledField . '_items_front'};
                    if (isset($options[$item->{'question_options_' . $enabledField}-1])) {
                        $row[] = strip_tags(erLhcoreClassSurveyValidator::parseAnswer($options[$item->{'question_options_' . $enabledField}-1]['option']));
                    } else {
                        $row[] = strip_tags(erLhcoreClassSurveyValidator::parseAnswer($item->{'question_options_' . $enabledField}));
                    }
                }

                foreach ($enabledFieldsPlain as $enabledFieldPlain) {
                    $row[] = $item->{'question_plain_' . $enabledFieldPlain};
                }

                $row[] = $item->ftime_front;

                fputcsv($df, $row);
            }
        }

        fclose($df);

    }
}

?>