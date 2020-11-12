<?php

erLhcoreClassRestAPIHandler::setHeaders();

if (isset($_POST['data'])) {
    $auditOptions = erLhcoreClassModelChatConfig::fetch('audit_configuration');
    $data = (array)$auditOptions->data;

    if (isset($data['log_js']) && $data['log_js'] == 1) {
        $dataLog = json_decode($_POST['data'], true);

        $referrer = '';
        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
            $referrer .= $_SERVER['HTTP_REFERER'] . "\n";
        }
        
        $messageLog = erLhcoreClassIPDetect::getIP() . "\n" . trim((isset($dataLog['message']) ? $dataLog['message'] : '') . "\n" . json_decode($dataLog['stack'],true));

        erLhcoreClassLog::write($messageLog,
            ezcLog::SUCCESS_AUDIT,
            array(
                'source' => 'lhc',
                'category' => 'js',
                'line' => (int)$dataLog['line'],
                'file' => $dataLog['file'],
                'object_id' => isset($dataLog['column']) ? (int)$dataLog['column'] : 0
            )
        );
    }
}

exit;
?>