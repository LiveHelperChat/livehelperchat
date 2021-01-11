<?php

/**
 *
 * php cron.php -s site_admin -c cron/stats/department_load
 *
 * */

echo "-=Starting departments live stats aggregation=-\n";

foreach (erLhcoreClassModelDepartament::getList(array('filter' => array('archive' => 0))) as $department) {
    echo "Updating department - ",$department->id,"\n";
    erLhcoreClassChatStatsResque::updateStats($department);
}

foreach (erLhcoreClassModelDepartamentGroup::getList() as $departmentGroup) {
    echo "Updating department group - ",$departmentGroup->id,"\n";
    erLhcoreClassChatStatsResque::updateDepartmentGroupStats($departmentGroup);
}

?>