<?php

erLhcoreClassRestAPIHandler::setHeaders();

if (isset($_POST['data'])) {
    $auditOptions = erLhcoreClassModelChatConfig::fetch('audit_configuration');
    $data = (array)$auditOptions->data;

    if (isset($data['log_js']) && $data['log_js'] == 1) {
        $dataLog = json_decode($_POST['data'], true);

        $referrer = '';
        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
            $referrer .= 'R: ' . $_SERVER['HTTP_REFERER'] . "\n";
        }

        if (isset($dataLog['location']) && !empty($dataLog['location'])) {
            $referrer .= 'L: ' . $dataLog['location'] . "\n";
        }

        if (session_status() == 2 && erLhcoreClassUser::instance()->isLogged()) {
            $referrer .= "User ID: " . erLhcoreClassUser::instance()->getUserID() . "\n";
        }

        $messageLog = $referrer . erLhcoreClassIPDetect::getIP() . "\n" . trim((isset($dataLog['message']) ? $dataLog['message'] : '') . "\n" . json_decode($dataLog['stack'],true));

        erLhcoreClassLog::write($messageLog,
            ezcLog::SUCCESS_AUDIT,
            array(
                'source' => 'lhc',
                'category' => 'js',
                'line' => (isset($dataLog['line']) ? (int)$dataLog['line'] : 0),
                'file' => (isset($dataLog['file']) ? $dataLog['file'] : ''),
                'object_id' => isset($dataLog['column']) ? (int)$dataLog['column'] : 0
            )
        );
    }
}

exit;
?>