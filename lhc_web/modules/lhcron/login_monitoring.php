<?php

/**
 * php cron.php -s site_admin -c cron/login_monitoring
 *
 * Run every minute. This cronjob send's push notification to visitors.
 *
 * */

$passwordData = (array)erLhcoreClassModelChatConfig::fetch('password_data')->data;

// Disable user automatically if from last login passed X number of days
if (isset($passwordData['disable_after']) && is_numeric($passwordData['disable_after']) && $passwordData['disable_after'] > 0) {

    $filter = [
        'filtergt' => [
            'llogin' => 0
        ],
        'filterlt' => [
            'llogin' => time() - ((int)$passwordData['disable_after'] * 24 * 3600)
        ],
        'filter' => [
            'disabled' => 0
        ]
    ];

    $q = ezcDbInstance::get()->createUpdateQuery();
    $conditions = erLhcoreClassModelUser::getConditions($filter, $q);

    $q->update( 'lh_users' )
        ->set( 'disabled',  1)
        ->set( 'force_logout',  1)
        ->where(
            $conditions
        );
    $stmt = $q->prepare();
    $stmt->execute();

    echo $stmt->rowCount()," operators were disabled\n";
}

// Force user logout if last login was X hours ago
if (isset($passwordData['logout_after']) && is_numeric($passwordData['logout_after']) && $passwordData['logout_after'] > 0) {

    $filter = [
        'filtergt' => [
            'llogin' => 0
        ],
        'filterlt' => [
            'llogin' => time() - ((int)$passwordData['logout_after'] * 3600)
        ],
        'filter' => [
            'force_logout' => 0,
            'disabled' => 0
        ]
    ];

    $q = ezcDbInstance::get()->createUpdateQuery();
    $conditions = erLhcoreClassModelUser::getConditions($filter, $q);
    $q->update( 'lh_users' )
        ->set( 'force_logout',  1)
        ->where(
            $conditions
        );
    $stmt = $q->prepare();
    $stmt->execute();

    echo $stmt->rowCount()," operators were forced to logout\n";
}