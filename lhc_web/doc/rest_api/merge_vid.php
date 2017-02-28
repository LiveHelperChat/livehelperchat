<?php
include 'lhrestapi.php';
$settings = include 'settings.ini.php';

$LHCRestAPI = new LHCRestAPI($settings['host'], $settings['user'], $settings['key']);

$response = $LHCRestAPI->execute('setnewvid', array(
        'vid' => '42rsfye2cwolnu3vst', // Old VID
        'new' => 'lbv6mnwc2jg4mmxmx5w', // New VID
     ), array(
), 'POST');

if ($response->error == false) {
    // All ok, merge succeded
} else {
    // Merge failed
}