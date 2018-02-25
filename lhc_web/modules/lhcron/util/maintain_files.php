<?php

/**
 * php cron.php -s site_admin -c cron/util/maintain_files
 */

echo "Starting Automatic files removing workflow for operators files\n";

$fileData = erLhcoreClassModelChatConfig::fetch('file_configuration');
$data = (array)$fileData->data;

if (isset($data['mdays_older']) && $data['mdays_older'] > 0 && isset($data['mtype_delete']) && !empty($data['mtype_delete']) && in_array('operators', $data['mtype_delete'])) {

    $filter = array('limit' => 500, 'filter' => array('persistent' => 0), 'filtergt' => array('user_id' => 0), 'filterlt' => array('date' => (time() - $data['mdays_older']*24*3600)));

    if (isset($data['mtype_cdelete']) && !empty($data['mtype_cdelete'])) {
        $cType = array();
        if (in_array('unassigned', $data['mtype_cdelete'])) {
            $cType[] ='(chat_id = 0)';
        }

        if (in_array('assigned', $data['mtype_cdelete'])) {
            $cType[] ='(chat_id != 0)';
        }

        $filter['customfilter'][] = '( ' . implode(' OR ', $cType) . ' )';
    }

    $files = erLhcoreClassModelChatFile::getList($filter);

    $filesRemoved = 0;
    foreach ($files as $file) {
        $file->removeThis();
        $filesRemoved++;
    }

    echo "Files removed - ",$filesRemoved,"\n";

} else {
    echo "Automatic files maintenance is not setup for operators\n";
}

echo "Starting Automatic files removing workflow for visitors files\n";

if (isset($data['mdays_older_visitor']) && $data['mdays_older_visitor'] > 0 && isset($data['mtype_delete']) && !empty($data['mtype_delete']) && in_array('visitors', $data['mtype_delete'])) {

    $filter = array('limit' => 500,'filter' => array('user_id' => 0, 'persistent' => 0), 'filterlt' => array('date' => (time() - $data['mdays_older_visitor']*24*3600)));

    if (isset($data['mtype_cdelete']) && !empty($data['mtype_cdelete'])) {
        $cType = array();
        if (in_array('unassigned', $data['mtype_cdelete'])) {
            $cType[] ='(chat_id = 0)';
        }

        if (in_array('assigned', $data['mtype_cdelete'])) {
            $cType[] ='(chat_id != 0)';
        }

        $filter['customfilter'][] = '( ' . implode(' OR ', $cType) . ' )';
    }

    $files = erLhcoreClassModelChatFile::getList($filter);

    $filesRemoved = 0;
    foreach ($files as $file) {
        $file->removeThis();
        $filesRemoved++;
    }

    echo "Files removed - ",$filesRemoved,"\n";

} else {
    echo "Automatic files maintenance is not for visitors\n";
}