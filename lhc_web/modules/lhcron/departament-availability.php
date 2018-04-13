<?php


/**
 * php cron.php -s site_admin -c cron/departament-availability
 *
 * Run every 1 minit.
 *
 * */
echo "Department monitoring\n";

$departmentList = erLhcoreClassModelDepartament::getList(array('limit' => false));

$ts = time();

// Store hour as UTC for easier grouping
$date_utc = new \DateTime(null, new \DateTimeZone("UTC"));
$minuteData = $date_utc->format('YmdHi');
$ymd = $date_utc->format('Ymd');

foreach ($departmentList as $department) {

    if (erLhcoreClassModelDepartamentAvailability::getCount(array('filter' => array('dep_id' => $department->id, 'ymdhi' => $minuteData))) == 0)
    {
        $availability = new erLhcoreClassModelDepartamentAvailability();
        $availability->dep_id = $department->id;
        $availability->time = $ts;
        $availability->hour = $date_utc->format("H");
        $availability->minute = $date_utc->format("i");
        $availability->ymdhi = $minuteData;
        $availability->ymd = $ymd;

        $isOnline = erLhcoreClassChat::isOnline($department->id);

        if ($isOnline == true && $department->disabled == 0) {
            $availability->status = erLhcoreClassModelDepartamentAvailability::STATUS_ONLINE;
        } elseif ($department->disabled == 1) {
            $availability->status = erLhcoreClassModelDepartamentAvailability::STATUS_DISABLED;
        } elseif ($department->is_overloaded == 1) {
            $availability->status = erLhcoreClassModelDepartamentAvailability::STATUS_OVERLOADED;
        } else {
            $availability->status = erLhcoreClassModelDepartamentAvailability::STATUS_OFFLINE;
        }

        $availability->saveThis();
    } else {
        echo "Already indexed - {$department->name} at {$minuteData}\n";
    }
}

?>