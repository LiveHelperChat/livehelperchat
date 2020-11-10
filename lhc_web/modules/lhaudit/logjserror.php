<?php

if (isset($_POST['data'])) {
    $dataLog = json_decode($_POST['data'], true);

    erLhcoreClassLog::write(trim($dataLog['message'] . "\n" . json_decode($dataLog['stack'],true)),
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

exit;
?>