<?php

class erLhcoreClassMailconvExport {

    public static function export($filter, $params) {
        if ($params['csv'] == true){
            self::exportCSV($filter, $params);
        } else {
            self::exportXLS(erLhcoreClassModelMailconvConversation::getList($filter));
        }
    }

    public static function exportCSV($filter, $params) {
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename=mail-report.csv");
        header("Content-Transfer-Encoding: binary");

        $df = fopen("php://output", 'w');

        $firstRow = [
            'ID',
            erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Date'),
            erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Minutes'),
            erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Department'),
            erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Department ID'),
            erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Operator'),
            erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Operator ID'),
            erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','From name'),
            erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','From address'),
            erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mail subject'),
            erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Priority'),
            erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Started by')
        ];

        if (in_array(2, $params['type'])) {
            $firstRow[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Subjects');
        }

        if (in_array(1, $params['type'])) {
            $firstRow[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Total messages');
            $firstRow[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Visitor messages number');
            $firstRow[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','No response required');
            $firstRow[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Responded');
            $firstRow[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Operator messages send');
        }

        $firstRow[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Additional variables');

        if (in_array(3, $params['type'])) {
            $firstRow[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Messages');
        }

        // First row
        fputcsv($df, $firstRow);

        $attributes = array(
            'department',
            'dep_id',
            'user',
            'user_id',
            'from_name',
            'from_address',
            'subject',
            'priority'
        );

        $chunks = ceil(erLhcoreClassModelMailconvConversation::getCount($filter)/300);
        for($i = 0; $i < $chunks; $i ++) {
            $filterChunk = $filter;
            $filterChunk['offset'] = $i * 300;
            $filterChunk['limit'] = 300;

            foreach (erLhcoreClassModelMailconvConversation::getList($filterChunk) as $item) {
                $itemCSV = [];

                $date = date(erLhcoreClassModule::$dateFormat,$item->ctime);
                $minutes = date('H:i:s',$item->ctime);

                $itemCSV[] = $item->id;
                $itemCSV[] = $date;
                $itemCSV[] = $minutes;
                
                foreach ($attributes as $attr) {
                    if ($attr == 'user') {
                        if (is_object($item->user)) {
                            $itemCSV[] = $item->user->name_official;
                        } else {
                            $itemCSV[] = 'N/A';
                        }
                    } else {
                        $itemCSV[] = (string)$item->{$attr};
                    }

                }

                $itemCSV[] = $item->start_type == erLhcoreClassModelMailconvConversation::START_OUT ? 'OPERATOR' : 'VISITOR';

                if (in_array(2, $params['type'])) {
                    $itemCSV[] = implode(', ',erLhcoreClassModelMailconvMessageSubject::getList(['filter' => ['conversation_id' => $item->id]]));
                }

                if (in_array(1, $params['type'])) {
                    $itemCSV[] = erLhcoreClassModelMailconvMessage::getCount(['filter' => ['conversation_id' => $item->id]]);
                    $itemCSV[] = erLhcoreClassModelMailconvMessage::getCount(['filter' => ['response_type' => [
                        erLhcoreClassModelMailconvMessage::RESPONSE_UNRESPONDED,
                        erLhcoreClassModelMailconvMessage::RESPONSE_NOT_REQUIRED,
                        erLhcoreClassModelMailconvMessage::RESPONSE_NORMAL
                    ], 'conversation_id' => $item->id]]);
                    $itemCSV[] = erLhcoreClassModelMailconvMessage::getCount(['filter' => ['response_type' => erLhcoreClassModelMailconvMessage::RESPONSE_NOT_REQUIRED, 'conversation_id' => $item->id]]);
                    $itemCSV[] = erLhcoreClassModelMailconvMessage::getCount(['filter' => ['response_type' => erLhcoreClassModelMailconvMessage::RESPONSE_NORMAL, 'conversation_id' => $item->id]]);
                    $itemCSV[] = erLhcoreClassModelMailconvMessage::getCount(['filter' => ['response_type' => erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL, 'conversation_id' => $item->id]]);
                }

                $itemCSV[] = $item->mail_variables;

                // Messages content
                if (in_array(3, $params['type'])) {
                    $messages = erLhcoreClassModelMailconvMessage::getList(['filter' => ['conversation_id' => $item->id]]);

                    $messagesBody = [];

                    foreach ($messages as $message) {
                        $messagesBody[] = $message->alt_body;
                    }

                    $itemCSV[] = implode("\n\n===========================\n\n", $messagesBody);
                }



                fputcsv($df, $itemCSV);
            }
        }

        fclose($df);

    }

    public static function exportXLS($items, $params) {
        include 'lib/core/lhform/PHPExcel.php';
        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array( 'memoryCacheSize ' => '64MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getStyle('A1:AW1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setTitle('Report');

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, "ID");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Department'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','From name'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','From address'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mail subject'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Priority'));

        $attributes = array(
            'id',
            'department',
            'from_name',
            'from_address',
            'subject',
            'priority',
        );

        $i = 2;
        foreach ($items as $item) {
            foreach ($attributes as $key => $attr) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, (string)$item->{$attr});
            }
           
            $messages = erLhcoreClassModelMailconvMessage::getList(['filter' => ['conversation_id' => $item->id]]);

            $messagesBody = [];

            foreach ($messages as $message) {
                $messagesBody[] = $message->alt_body;
            }

            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key++, $i, (string)implode("\n\n===========================\n\n",$messagesBody));

            $i++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        // We'll be outputting an excel file
        header('Content-type: application/vnd.ms-excel');

        // It will be called file.xls
        header('Content-Disposition: attachment; filename="export_mails.xlsx"');

        // Write file to the browser
        $objWriter->save('php://output');
    }

}

?>