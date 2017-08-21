<?php

// php cron.php -s site_admin -c cron/util/test_antivirus -p <path_to_file>

$fileData = erLhcoreClassModelChatConfig::fetch('file_configuration');
$data = (array)$fileData->data;

$opts = array();

if (isset($data['clamd_sock']) && !empty($data['clamd_sock'])) {
    $opts['clamd_sock'] = $data['clamd_sock'];
}

if (isset($data['clamd_sock_len']) && !empty($data['clamd_sock_len'])) {
    $opts['clamd_sock_len'] = $data['clamd_sock_len'];
}

$clamav = new Clamav($opts);

echo "Scanning file - ",$cronjobPathOption->value,"\n";

if (file_exists($cronjobPathOption->value)) {
    if ($clamav->scan(realpath($cronjobPathOption->value))) {
        echo "File is safe\n";
    } else {
        echo "File is infected\n";
    }
} else {
    echo "File not found\n";
}

?>