<?php

class erLhcoreClassMailconvExport {

    public static function exportXLS($items) {
        include 'lib/core/lhform/PHPExcel.php';
        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array( 'memoryCacheSize ' => '64MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getStyle('A1:AW1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setTitle('Report');

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, "ID");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Department'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','From name'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','From address'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Subject'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Priority'));

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