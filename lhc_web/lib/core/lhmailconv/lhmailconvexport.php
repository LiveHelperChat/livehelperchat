<?php

class erLhcoreClassMailconvExport {

    public static function export($filter, $params) {
        if ($params['csv'] == true){
            self::exportCSV($filter, $params);
        } else {
            self::exportXLS((isset($params['is_archive']) ? \LiveHelperChat\Models\mailConv\Archive\Conversation::getList($filter) : erLhcoreClassModelMailconvConversation::getList($filter)),$params);
        }
    }

    public static function exportCampaignRecipientCSV($filter, $params) {
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename=campaign-recipient-" . $params['campaign']->id . ".csv");
        header("Content-Transfer-Encoding: binary");

        $df = fopen("php://output", 'w');

        $firstRow = [
            'email',
            'mailbox',
            'name',
            'attr_str_1',
            'attr_str_2',
            'attr_str_3',
            'attr_str_4',
            'attr_str_5',
            'attr_str_6',
            'status',
            'send_at',
            'opened_at',
            'message_id',
            'conversation_id',
            'type',
            'log',
        ];

        fputcsv($df, $firstRow);

        $chunks = ceil(erLhcoreClassModelMailconvMailingCampaignRecipient::getCount($filter)/300);

        $status = [
            erLhcoreClassModelMailconvMailingCampaignRecipient::PENDING => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Pending'),
            erLhcoreClassModelMailconvMailingCampaignRecipient::IN_PROGRESS => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','In progress'),
            erLhcoreClassModelMailconvMailingCampaignRecipient::FAILED => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Failed'),
            erLhcoreClassModelMailconvMailingCampaignRecipient::SEND => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Send')
        ];

        for($i = 0; $i < $chunks; $i ++) {
            $filterChunk = $filter;
            $filterChunk['offset'] = $i * 300;
            $filterChunk['limit'] = 300;

            foreach (erLhcoreClassModelMailconvMailingCampaignRecipient::getList($filterChunk) as $item) {
                $itemCSV = [];
                $itemCSV[] = (string)$item->recipient;
                $itemCSV[] = (string)$item->mailbox_front;
                $itemCSV[] = (string)$item->name;
                $itemCSV[] = (string)$item->attr_str_1;
                $itemCSV[] = (string)$item->attr_str_2;
                $itemCSV[] = (string)$item->attr_str_3;
                $itemCSV[] = (string)$item->attr_str_4;
                $itemCSV[] = (string)$item->attr_str_5;
                $itemCSV[] = (string)$item->attr_str_6;
                $itemCSV[] = $status[$item->status];
                $itemCSV[] = $item->send_at > 0 ? date(erLhcoreClassModule::$dateFormat, $item->send_at) : 'n/a';
                $itemCSV[] = $item->opened_at > 0 ? date(erLhcoreClassModule::$dateFormat, $item->opened_at) : 'n/a';
                $itemCSV[] = (string)$item->message_id;
                $itemCSV[] = (string)$item->conversation_id;
                $itemCSV[] = (string)$item->type == 1 ? 'manual' : 'list';
                $itemCSV[] = (string)$item->log;
                fputcsv($df, $itemCSV);
            }
        }

        fclose($df);
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
            erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Lang'),
            erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','From name'),
            erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','From address'),
            erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Phone'),
            erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mail subject'),
            erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Priority'),
            erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Started by'),
            erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Opened At'),
            erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Undelivered'),
            erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Undelivered error'),
            erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Undelivered Status'),
            erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Undelivered report')
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
            $firstRow[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Messages Plain');
            $firstRow[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Messages HTML');
        }

        // First row
        fputcsv($df, $firstRow);

        $attributes = array(
            'department',
            'dep_id',
            'user',
            'user_id',
            'lang',
            'from_name',
            'from_address',
            'phone',
            'subject',
            'priority'
        );

        if (isset($params['conversation_id'])) {
            $chunks = ceil( count($params['conversation_id'])/300);
        } else {
            $chunks = ceil( (isset($params['is_archive']) ? \LiveHelperChat\Models\mailConv\Archive\Conversation::getCount($filter) : erLhcoreClassModelMailconvConversation::getCount($filter))/300);
        }

        for($i = 0; $i < $chunks; $i ++) {
            $filterChunk = $filter;
            $filterChunk['offset'] = $i * 300;
            $filterChunk['limit'] = 300;

            if (isset($params['conversation_id'])) {
                $itemsIds = array_splice($params['conversation_id'],0,300);

                if (empty($itemsIds)) {
                    continue;
                }

                $items = erLhcoreClassModelMailconvConversation::getList(array('filterin' => ['id' => $itemsIds]));

                $liveIds = array_keys($items);

                $missingIds = array_diff($itemsIds, $liveIds); // Those conversations are in archives most likely. Unless completely missing.

                foreach ($missingIds as $itemId) {
                   $mailData = \LiveHelperChat\mailConv\Archive\Archive::fetchMailById($itemId);
                    if (isset($mailData['mail'])) {
                        $items[] = $mailData['mail'];
                    }
                }

            } else {
                $items = isset($params['is_archive']) ? \LiveHelperChat\Models\mailConv\Archive\Conversation::getList($filterChunk) : erLhcoreClassModelMailconvConversation::getList($filterChunk);
            }

            $emailVisible = erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','mail_see_unhidden_email') && erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','mail_export');
            $phoneVisible = erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','phone_see_unhidden') && erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','phone_export');

            foreach ($items as $item) {
                $itemCSV = [];
                $is_live_archive = false;

                // In export mails can be in separate archives if export is not directly from archive
                if (isset($params['conversation_id']) && $item instanceof \LiveHelperChat\Models\mailConv\Archive\Conversation) {
                    $item->archive->setTables();
                    $is_live_archive = true;
                }

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
                    } else if ($attr == 'phone' || $attr == 'from_address') {
                        $itemCSV[] = ($attr == 'phone' && $phoneVisible) || ($attr == 'from_address' && $emailVisible) ? (string)$item->{$attr} : '';
                    } else {
                        $itemCSV[] = (string)$item->{$attr};
                    }

                }

                $itemCSV[] = $item->start_type == erLhcoreClassModelMailconvConversation::START_OUT ? 'OPERATOR' : 'VISITOR';
                $itemCSV[] = $item->opened_at > 0 ? $item->opened_at_front : 0;
                $itemCSV[] = $item->undelivered;

                $undeliverReport = $undeliverStatus = $undeliverCode = '';
                if ($item->undelivered == 1) {

                    $lastUndeliveredMessage = $is_live_archive === true || isset($params['is_archive']) ? \LiveHelperChat\Models\mailConv\Archive\Message::fetch($item->last_message_id) : erLhcoreClassModelMailconvMessage::fetch($item->last_message_id);

                    if ($lastUndeliveredMessage instanceof erLhcoreClassModelMailconvMessage || $lastUndeliveredMessage instanceof \LiveHelperChat\Models\mailConv\Archive\Message) {
                        $undeliverReport = $lastUndeliveredMessage->delivery_status;
                        if (isset($lastUndeliveredMessage->delivery_status_keyed['Diagnostic_Code'])){
                            $undeliverCode = $lastUndeliveredMessage->delivery_status_keyed['Diagnostic_Code'];
                        }
                        if (isset($lastUndeliveredMessage->delivery_status_keyed['Status'])){
                            $undeliverStatus = $lastUndeliveredMessage->delivery_status_keyed['Status'];
                        }
                    }
                }

                $itemCSV[] = $undeliverCode;
                $itemCSV[] = $undeliverStatus;
                $itemCSV[] = $undeliverReport;

                if (in_array(2, $params['type'])) {
                    $itemCSV[] = implode(', ',$is_live_archive === true || isset($params['is_archive']) ? \LiveHelperChat\Models\mailConv\Archive\MessageSubject::getList(['filter' => ['conversation_id' => $item->id]]) : erLhcoreClassModelMailconvMessageSubject::getList(['filter' => ['conversation_id' => $item->id]]));
                }

                if (in_array(1, $params['type'])) {

                    if ($is_live_archive === true || isset($params['is_archive'])){
                        $itemCSV[] = \LiveHelperChat\Models\mailConv\Archive\Message::getCount(['filter' => ['conversation_id' => $item->id]]);
                        $itemCSV[] = \LiveHelperChat\Models\mailConv\Archive\Message::getCount(['filter' => ['response_type' => [
                            erLhcoreClassModelMailconvMessage::RESPONSE_UNRESPONDED,
                            erLhcoreClassModelMailconvMessage::RESPONSE_NOT_REQUIRED,
                            erLhcoreClassModelMailconvMessage::RESPONSE_NORMAL
                        ], 'conversation_id' => $item->id]]);
                        $itemCSV[] = \LiveHelperChat\Models\mailConv\Archive\Message::getCount(['filter' => ['response_type' => erLhcoreClassModelMailconvMessage::RESPONSE_NOT_REQUIRED, 'conversation_id' => $item->id]]);
                        $itemCSV[] = \LiveHelperChat\Models\mailConv\Archive\Message::getCount(['filter' => ['response_type' => erLhcoreClassModelMailconvMessage::RESPONSE_NORMAL, 'conversation_id' => $item->id]]);
                        $itemCSV[] = \LiveHelperChat\Models\mailConv\Archive\Message::getCount(['filter' => ['response_type' => erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL, 'conversation_id' => $item->id]]);
                    } else {
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
                }

                $itemCSV[] = $item->mail_variables;

                // Messages content
                if (in_array(3, $params['type'])) {

                    $messages = $is_live_archive === true || isset($params['is_archive']) ? \LiveHelperChat\Models\mailConv\Archive\Message::getList(['filter' => ['conversation_id' => $item->id]]) : erLhcoreClassModelMailconvMessage::getList(['filter' => ['conversation_id' => $item->id]]);

                    $messagesBodyHTML = $messagesBody = [];

                    foreach ($messages as $message) {
                        $messagesBody[] = $message->alt_body;
                        $messagesBodyHTML[] = $message->body;
                    }

                    $itemCSV[] = implode("\n\n===========================\n\n", $messagesBody);
                    $itemCSV[] = implode("\n\n===========================\n\n", $messagesBodyHTML);
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
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Phone'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mail subject'));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Priority'));

        $attributes = array(
            'id',
            'department',
            'from_name',
            'from_address',
            'phone',
            'subject',
            'priority',
        );

        $emailVisible = erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','mail_see_unhidden_email') && erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','mail_export');
        $phoneVisible = erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','phone_see_unhidden') && erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','phone_export');

        $i = 2;
        foreach ($items as $item) {
            foreach ($attributes as $key => $attr) {
                if ($attr == 'phone' || $attr == 'from_address') {
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i,($attr == 'phone' && $phoneVisible) || ($attr == 'from_address' && $emailVisible) ? (string)$item->{$attr} : '');
                } else {
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, (string)$item->{$attr});
                }

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