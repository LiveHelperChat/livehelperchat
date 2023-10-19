<?php

namespace LiveHelperChat\Validators;

use LiveHelperChat\Models\Statistic\SavedReport;

class ReportValidator {

    public static function validateReport(SavedReport & $search, $params) {
        $definition = array(
            'name' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'description' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'days' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
            ),
            'days_end' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
            ),
            'position' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'timefrom_hours' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0,'max_range' => 23)
            ),
            'timefrom_minutes' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0,'max_range' => 59)
            ),
            'timefrom_seconds' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0,'max_range' => 59)
            ),
            'timeto_hours' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0, 'max_range' => 23)
            ),
            'timeto_minutes' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0, 'max_range' => 59)
            ),
            'timeto_seconds' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0, 'max_range' => 59)
            ),
            'date_type' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'string'
            ),
            // Recurring report option
            'send_to' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'send_daily_active' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1), FILTER_REQUIRE_ARRAY
            ),
            'send_daily' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', array(),FILTER_REQUIRE_ARRAY
            ),
            'send_weekly_time' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', array(),FILTER_REQUIRE_ARRAY
            ),
            'send_month_time' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', array(),FILTER_REQUIRE_ARRAY
            ),
            'send_weekly_active' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1),FILTER_REQUIRE_ARRAY
            ),
            'send_weekly_day' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1),FILTER_REQUIRE_ARRAY
            ),
            'send_monthly_active' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1),FILTER_REQUIRE_ARRAY
            ),
            'send_month_day' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1),FILTER_REQUIRE_ARRAY
            ),
        );

        $form = new \ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( !$form->hasValidData( 'name' ) || $form->name == '' ) {
            $Errors[] = \erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Please enter a name');
        } else {
            $search->name = $form->name;
        }

        if ( $form->hasValidData( 'description' ) ) {
            $search->description = $form->description;
        }

        if ($form->hasValidData( 'position' )) {
            $search->position = $form->position;
        } else {
            $search->position = 0;
        }

        if ($form->hasValidData( 'days' )) {
            $search->days = $form->days;
        } else {
            $search->days = 0;
        }

        if ($form->hasValidData( 'days_end' )) {
            $search->days_end = $form->days_end;
        } else {
            $search->days_end = 0;
        }

        if ($form->hasValidData( 'date_type' )) {
            $search->date_type = $form->date_type;
        } else {
            $search->date_type = 'ndays';
        }

        if ($form->hasValidData( 'timefrom_hours' )) {
            $params['input_form']->timefrom_hours = $form->timefrom_hours;
        } else {
            $params['input_form']->timefrom_hours = '';
        }

        if ($form->hasValidData( 'timefrom_minutes' )) {
            $params['input_form']->timefrom_minutes = $form->timefrom_minutes;
        } else {
            $params['input_form']->timefrom_minutes = '';
        }

        if ($form->hasValidData( 'timefrom_seconds' )) {
            $params['input_form']->timefrom_seconds = $form->timefrom_seconds;
        } else {
            $params['input_form']->timefrom_seconds = '';
        }

        if ($form->hasValidData( 'timeto_hours' )) {
            $params['input_form']->timeto_hours = $form->timeto_hours;
        } else {
            $params['input_form']->timeto_hours = '';
        }

        if ($form->hasValidData( 'timeto_minutes' )) {
            $params['input_form']->timeto_minutes = $form->timeto_minutes;
        } else {
            $params['input_form']->timeto_minutes = '';
        }

        if ($form->hasValidData( 'timeto_seconds' )) {
            $params['input_form']->timeto_seconds = $form->timeto_seconds;
        } else {
            $params['input_form']->timeto_seconds = '';
        }

        $paramsRecurring = $search->recurring_options_array;
        $paramsRecurring['send_to'] = $form->hasValidData( 'send_to' ) ? $form->send_to : '';

        if ($form->hasValidData( 'send_daily_active' )) {
            $paramsRecurring['send_daily_active'] = $form->send_daily_active;
        } else {
            $paramsRecurring['send_daily_active'] = [];
        }

        if ($form->hasValidData( 'send_weekly_active' )) {
            $paramsRecurring['send_weekly_active'] = $form->send_weekly_active;
        } else {
            $paramsRecurring['send_weekly_active'] = [];
        }

        if ($form->hasValidData( 'send_monthly_active' )) {
            $paramsRecurring['send_monthly_active'] = $form->send_monthly_active;
        } else {
            $paramsRecurring['send_monthly_active'] = [];
        }

        if ($form->hasValidData('send_daily')) {
            foreach ($form->send_daily as $key => $dailyTime) {
                $paramsRecurring['send_daily'][$key] = (int)str_replace(':','', $dailyTime);
            }
        } else {
            $paramsRecurring['send_daily'] = [];
        }

        if ($form->hasValidData('send_month_time')) {
            foreach ($form->send_month_time as $key => $dailyTime) {
                $paramsRecurring['send_month_time'][$key] = (int)str_replace(':','', $dailyTime);
            }
        } else {
            $paramsRecurring['send_month_time'] = [];
        }

        if ($form->hasValidData('send_weekly_time')) {
            foreach ($form->send_weekly_time as $key => $dailyTime) {
                $paramsRecurring['send_weekly_time'][$key] = (int)str_replace(':','', $dailyTime);
            }
        } else {
            $paramsRecurring['send_weekly_time'] = [];
        }

        if ($form->hasValidData('send_weekly_day')) {
            $paramsRecurring['send_weekly_day'] = $form->send_weekly_day;
        } else {
            $paramsRecurring['send_weekly_day'] = [];
        }

        if ($form->hasValidData('send_month_day')) {
            $paramsRecurring['send_month_day'] = $form->send_month_day;
        } else {
            $paramsRecurring['send_month_day'] = [];
        }

        $search->recurring_options = json_encode($paramsRecurring);
        $search->recurring_options_array = $paramsRecurring;

        $search->params = json_encode($params);

        return $Errors;
    }

    // php cron.php -s site_admin -c cron/report
    public static function sendReports() {

        $db = \ezcDbInstance::get();

        foreach (SavedReport::getList([/*'filter' => ['id' => 19],*/'limit' => false]) as $report) {
            $db->beginTransaction();
            $report = SavedReport::fetchAndLock($report->id);
            self::processReport($report);
            $db->commit();
        }
    }

    public static function processReport(SavedReport & $report) {

        if (!isset($report->recurring_options_array['send_to']) || $report->recurring_options_array['send_to'] == '') {
            return;
        }

        $user = $report->user;

        $cfg = \erConfigClassLhConfig::getInstance();

        if ($user->time_zone != '') {
            date_default_timezone_set($user->time_zone);
        } else {
            date_default_timezone_set($cfg->getSetting('site', 'time_zone', false));
        }

        try {
            $dt = new \DateTime();
            $offset = $dt->format("P");
            $db = \ezcDbInstance::get();
            $db->query("SET LOCAL time_zone='" . $offset ."'");
        } catch (\Exception $e) {
            // Ignore
        }

        $url = $report->generateURL(true);

        $sendLogArray = $report->send_log_array;

        $reportConsolidated = [];

        $paramsFormatted = $report->getParamsURL();

        $outputString = \erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel', 'From') . ' ' .$paramsFormatted['input_form']['timefrom']
            . ' ' . str_pad((int)$paramsFormatted['input_form']['timefrom_hours'],2, '0', STR_PAD_LEFT) .' h.'
            . ' ' . str_pad((int)$paramsFormatted['input_form']['timefrom_minutes'],2, '0', STR_PAD_LEFT) .' m.'
            . ' ' . str_pad((int)$paramsFormatted['input_form']['timefrom_seconds'],2, '0', STR_PAD_LEFT) .' s.';
        if ($paramsFormatted['input_form']['timeto'] != ''){
            $outputString .= ' ' . \erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel', 'Till') . ' ' . $paramsFormatted['input_form']['timeto']
                . ' ' . str_pad((int)$paramsFormatted['input_form']['timeto_hours'],2, '0', STR_PAD_LEFT) .' h.'
                . ' ' . str_pad((int)$paramsFormatted['input_form']['timeto_minutes'],2, '0', STR_PAD_LEFT) .' m.'
                . ' ' . str_pad((int)$paramsFormatted['input_form']['timeto_seconds'],2, '0', STR_PAD_LEFT) .' s.' ;
        } else {
            $outputString .= ' ' . \erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel', 'Till now');
        }

        // Daily reporting
        foreach ($report->recurring_options_array['send_daily_active'] as $indexDaily) {
            $timeDaily = $report->recurring_options_array['send_daily'][$indexDaily];

            // Report was already processed
            if (isset($sendLogArray['send_daily'][$indexDaily][$timeDaily]) && $sendLogArray['send_daily'][$indexDaily][$timeDaily] == date('Ymd')) {
                continue;
            }

            $minutesStart = str_pad(substr($timeDaily,-2),2,'0', STR_PAD_LEFT);
            $hoursStart = str_pad(substr($timeDaily,0,strlen($timeDaily) - 2), 2, '0', STR_PAD_LEFT);

            if (date('Hi') >= $timeDaily) {
                $sendLogArray['send_daily'][$indexDaily][$timeDaily] = date('Ymd');

                $reportConsolidated = [
                    'type' => 'daily',
                    'generated_at' => date('Y-m-d H:i:s'),
                    'at' => $hoursStart . ':' . $minutesStart,
                    'date' => date('Y-m-d'),
                    'date_range' => $outputString,
                    'name' => $report->name,
                    'desc' => $report->description,
                    'report_id' => $report->id,
                    'url' => $url,
                    'send_to' => $report->recurring_options_array['send_to'],
                ];
            }
        }

        foreach ($report->recurring_options_array['send_weekly_active'] as $indexDaily) {
            $weekDay = $report->recurring_options_array['send_weekly_day'][$indexDaily];

            if (date('N') != $weekDay) {
                continue;
            }

            $timeDaily = $report->recurring_options_array['send_weekly_time'][$indexDaily];

            // Report was already processed
            if (isset($sendLogArray['send_weekly'][$indexDaily][$timeDaily]) && $sendLogArray['send_weekly'][$indexDaily][$timeDaily] == date('Ymd')) {
                continue;
            }

            $minutesStart = str_pad(substr($timeDaily,-2),2,'0', STR_PAD_LEFT);
            $hoursStart = str_pad(substr($timeDaily,0,strlen($timeDaily) - 2), 2, '0', STR_PAD_LEFT);

            if (date('Hi') >= $timeDaily) {
                $sendLogArray['send_weekly'][$indexDaily][$timeDaily] = date('Ymd');

                $reportConsolidated = [
                    'type' => 'weekly',
                    'generated_at' => date('Y-m-d H:i:s'),
                    'at' => $hoursStart . ':' . $minutesStart,
                    'date' => date('Y-m-d'),
                    'date_range' => $outputString,
                    'name' => $report->name,
                    'desc' => $report->description,
                    'report_id' => $report->id,
                    'url' => $url,
                    'send_to' => $report->recurring_options_array['send_to'],
                ];
            }
        }

        foreach ($report->recurring_options_array['send_monthly_active'] as $indexDaily) {
            $monthDay = $report->recurring_options_array['send_month_day'][$indexDaily];

            if (date('d') != $monthDay) {
                continue;
            }

            $timeDaily = $report->recurring_options_array['send_month_time'][$indexDaily];

            // Report was already processed
            if (isset($sendLogArray['send_monthly'][$indexDaily][$timeDaily]) && $sendLogArray['send_monthly'][$indexDaily][$timeDaily] == date('Ymd')) {
                continue;
            }

            $minutesStart = str_pad(substr($timeDaily,-2),2,'0', STR_PAD_LEFT);
            $hoursStart = str_pad(substr($timeDaily,0,strlen($timeDaily) - 2), 2, '0', STR_PAD_LEFT);

            if (date('Hi') >= $timeDaily) {
                $sendLogArray['send_monthly'][$indexDaily][$timeDaily] = date('Ymd');

                $reportConsolidated = [
                    'type' => 'monthly',
                    'generated_at' => date('Y-m-d H:i:s'),
                    'at' => $hoursStart . ':' . $minutesStart,
                    'date' => date('Y-m-d'),
                    'date_range' => $outputString,
                    'name' => $report->name,
                    'desc' => $report->description,
                    'report_id' => $report->id,
                    'url' => $url,
                    'send_to' => $report->recurring_options_array['send_to'],
                ];
            }
        }

        if (!empty($reportConsolidated)) {
            $report->send_log_array = $sendLogArray;
            $report->send_log = json_encode($sendLogArray);
            $report->updateThis(['update' => ['send_log']]);

            self::sendReport($reportConsolidated);
        }
    }

    public static function sendReport($reportConsolidated) {
        $sendMail = \erLhAbstractModelEmailTemplate::fetch(13);
        $sendMail->translate();

        $mail = new \PHPMailer();
        $mail->CharSet = "UTF-8";

        if ($sendMail->from_email != '') {
            $mail->Sender = $mail->From = $sendMail->from_email;
        }

        $mail->FromName = $sendMail->from_name;

        $mail->Subject = str_replace(array('{report_name}'), array($reportConsolidated['name']), $sendMail->subject);

        $emailRecipient = explode(',',str_replace(' ','',$reportConsolidated['send_to']));

        \erLhcoreClassChatMail::setupSMTP($mail);

        if ($sendMail->reply_to != '') {
            $mail->AddReplyTo($sendMail->reply_to, $sendMail->from_name);
        }

        $secretHash = \erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' );
        $ts = time();

        foreach ($emailRecipient as $receiver) {
            $mail->Body = str_replace(array(
                '{report_name}',
                '{report_description}',
                '{date_range}',
                '{url_report}',
                '{url_report_direct}'
            ), array(
                $reportConsolidated['name'],
                $reportConsolidated['desc'],
                $reportConsolidated['date_range'],
                \erLhcoreClassSystem::getHost() . \erLhcoreClassDesign::baseurl('user/login').'/(r)/'.rawurlencode(base64_encode( $reportConsolidated['url'] )),
                \erLhcoreClassSystem::getHost() . \erLhcoreClassDesign::baseurl('statistic/statistic') .'/(report)/' . $reportConsolidated['report_id']  . '/(reportts)/' . $ts . '/(reporthash)/' . md5($reportConsolidated['url'] . $reportConsolidated['report_id'] . $ts . $secretHash) . '/(r)/' . rawurlencode(base64_encode( $reportConsolidated['url'] )),
            ),
            $sendMail->content);

            $mail->AddAddress( $receiver );
            $mail->Send();
            $mail->ClearAddresses();
        }
    }
}