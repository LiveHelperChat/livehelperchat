<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhgenericbot/botimportgroup.tpl.php' );

$bot = erLhcoreClassModelGenericBotBot::fetch((int)$Params['user_parameters']['id']);

if (ezcInputForm::hasPostData()) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('genericbot/import');
        exit;
    }

    if (erLhcoreClassSearchHandler::isFile('botfile',array('json'))) {

        $dir = 'var/tmpfiles/';
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('theme.temppath',array('dir' => & $dir));

        erLhcoreClassFileUpload::mkdirRecursive( $dir );

        $filename = erLhcoreClassSearchHandler::moveUploadedFile('botfile',$dir);
        $content = file_get_contents($dir . $filename);
        unlink($dir . $filename);
        $data = json_decode($content,true);

        if ($data !== null) {

            $replaceTriggerIds = array();
            $triggersArray = array();

            foreach ($data['groups'] as $group) {
                $groupObj = new erLhcoreClassModelGenericBotGroup();
                $groupObj->bot_id = $bot->id;
                $groupObj->name = $group['group']['name'];
                $groupObj->saveThis();

                foreach ($group['triggers'] as $trigger) {

                    $triggerObj = new erLhcoreClassModelGenericBotTrigger();
                    $triggerObj->bot_id = $bot->id;
                    $triggerObj->group_id = $groupObj->id;
                    $triggerObj->name = $trigger['trigger']['name'];
                    $triggerObj->default = $trigger['trigger']['default'];
                    $triggerObj->default_unknown = $trigger['trigger']['default_unknown'];
                    $triggerObj->actions = $trigger['trigger']['actions'];
                    $triggerObj->saveThis();

                    $triggersArray[] = $triggerObj;
                    $replaceTriggerIds[$trigger['trigger']['id']] = $triggerObj->id;

                    foreach ($trigger['events'] as $event) {
                        $eventObj = new erLhcoreClassModelGenericBotTriggerEvent();
                        $eventObj->trigger_id = $triggerObj->id;
                        $eventObj->bot_id = $bot->id;
                        $eventObj->pattern = $event['pattern'];
                        $eventObj->pattern_exc = $event['pattern_exc'];
                        $eventObj->configuration = $event['configuration'];
                        $eventObj->type = $event['type'];
                        $eventObj->on_start_type = $event['on_start_type'];
                        $eventObj->priority = $event['priority'];
                        $eventObj->saveThis();
                    }
                }
            }

            $replaceArraySearch = array();
            $replaceArrayReplace = array();
            foreach ($replaceTriggerIds as $oldTriggerId => $newTriggerId){

                $replaceArraySearch[] = '"payload":"' . $oldTriggerId . '"';
                $replaceArrayReplace[] = '"payload":"' . $newTriggerId . '"';

                $replaceArraySearch[] = '"collection_callback_pattern":"' . $oldTriggerId . '"';
                $replaceArrayReplace[] = '"collection_callback_pattern":"' . $newTriggerId . '"';

                $replaceArraySearch[] = '"alternative_callback":"' . $oldTriggerId . '"';
                $replaceArrayReplace[] = '"alternative_callback":"' . $newTriggerId . '"';

                $replaceArraySearch[] = '"collection_callback_alternative":"' . $oldTriggerId . '"';
                $replaceArrayReplace[] = '"collection_callback_alternative":"' . $newTriggerId . '"';

                $replaceArraySearch[] = '"collection_callback_format":"' . $oldTriggerId . '"';
                $replaceArrayReplace[] = '"collection_callback_format":"' . $newTriggerId . '"';

                $replaceArraySearch[] = '"collection_callback_match":"' . $oldTriggerId . '"';
                $replaceArrayReplace[] = '"collection_callback_match":"' . $newTriggerId . '"';

                $replaceArraySearch[] = '"collection_callback_cancel":"' . $oldTriggerId . '"';
                $replaceArrayReplace[] = '"collection_callback_cancel":"' . $newTriggerId . '"';

                $replaceArraySearch[] = '"payload_online":"' . $oldTriggerId . '"';
                $replaceArrayReplace[] = '"payload_online":"' . $newTriggerId . '"';

                $replaceArraySearch[] = '"callback_reschedule":"' . $oldTriggerId . '"';
                $replaceArrayReplace[] = '"callback_reschedule":"' . $newTriggerId . '"';

                $replaceArraySearch[] = '"callback_match":"' . $oldTriggerId . '"';
                $replaceArrayReplace[] = '"callback_match":"' . $newTriggerId . '"';

                $replaceArraySearch[] = '"trigger_id":"' . $oldTriggerId . '"';
                $replaceArrayReplace[] = '"trigger_id":"' . $newTriggerId . '"';

            }

            foreach ($triggersArray as $trigger) {
                $trigger->actions = str_replace($replaceArraySearch,$replaceArrayReplace,$trigger->actions);
                $trigger->saveThis();
            }
        }

        $tpl->set('updated',true);
    } else {
        $tpl->set('errors',array(erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/import','Invalid file!')));
    }
}

$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('genericbot/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Bots')),array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Import')));
$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'popup';

?>