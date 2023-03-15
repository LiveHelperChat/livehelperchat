<?php

class erLhcoreClassGenericBotActionSurvey {

    public static function process($chat, $action, $trigger, $params)
    {
        $msg = new erLhcoreClassModelmsg();

        $metaMessage = array();

        if (
            isset($action['content']['survey_options']['survey_id']) &&
            ((is_numeric($action['content']['survey_options']['survey_id']) && $action['content']['survey_options']['survey_id'] > 0) || ($action['content']['survey_options']['survey_id'] != ''))
        ) {
            $surveyIds = [];

            if (is_numeric($action['content']['survey_options']['survey_id']) && $action['content']['survey_options']['survey_id'] > 0) {
                $surveyIds[] = (int)$action['content']['survey_options']['survey_id'];
            } else {
                $surveys = erLhAbstractModelSurvey::getList(['limit' => false, 'filter' => ['identifier' => $action['content']['survey_options']['survey_id']]]);
                $surveyIds = array_keys($surveys);
            }

            if ($chat->online_user_id > 0 && isset($action['content']['survey_options']['unique_vote']) && $action['content']['survey_options']['unique_vote'] == true && !empty($surveyIds)) {
                $surveyFilledIds = [];
                $surveysFilled = erLhAbstractModelSurveyItem::getList(['limit' => false, 'filterin' => ['survey_id' => $surveyIds], 'filter' => ['online_user_id' => $chat->online_user_id]]);
                foreach ($surveysFilled as $surveyFilled) {
                    if (!isset($action['content']['expires_vote']) || (int)$action['content']['expires_vote'] == 0 || $surveyFilled->ftime > (time() - (int)$action['content']['expires_vote'] * 24 * 3600)) {
                        $surveyFilledIds[] = $surveyFilled->survey_id;
                    }
                }
                $surveyIds = array_diff($surveyIds, $surveyFilledIds);
            }

            if (!empty($surveyIds) && isset($action['content']['survey_options']['unique_per_chat']) && $action['content']['survey_options']['unique_per_chat'] == true) {
                $surveyFilledIds = [];
                foreach (erLhcoreClassModelmsg::getList(['filterlike' => ['meta_msg' => 'survey_id'], 'filter' => ['chat_id' => $chat->id]]) as $msgPrevious) {
                    $metaData = $msgPrevious->meta_msg_array;
                    if (isset($metaData['content']['survey']['survey_id'])){
                        $surveyFilledIds[] = $metaData['content']['survey']['survey_id'];
                    }
                }
                $surveyIds = array_diff($surveyIds, $surveyFilledIds);
            }

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_survey_trigger', array(
                'action' => $action,
                'chat' => & $chat,
                'survey_id' => & $surveyIds
            ));

            $msg->msg = "";

            // No survey to fill
            if (empty($surveyIds)) {

                if (isset($action['content']['payload']) && is_numeric($action['content']['payload'])) {

                    $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_chat_predefined', array(
                        'action' => $action,
                        'chat' => & $chat,
                    ));

                    $params['predefined_trigger'] = $params['current_trigger'] = $trigger;

                    if (!isset($params['first_trigger'])) {
                        $params['first_trigger'] = $params['current_trigger'];
                    }

                    if ($handler !== false) {
                        $trigger = $handler['trigger'];
                    } else {
                        $trigger = erLhcoreClassModelGenericBotTrigger::fetch($action['content']['payload']);
                    }

                    if (($trigger instanceof erLhcoreClassModelGenericBotTrigger)){
                        if (!isset($params['do_not_save']) || $params['do_not_save'] == false) {
                            return erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true, array('args' => $params));
                        } else {
                            return erLhcoreClassGenericBotWorkflow::processTriggerPreview($chat, $trigger, array('args' => $params));
                        }
                    }
                }

                return ;

            } else {
                $action['content']['survey_options']['survey_id'] = $surveyIds[array_rand($surveyIds)];
                $metaMessage['content']['survey'] = $action['content']['survey_options'];
                $msg->meta_msg = !empty($metaMessage) ? json_encode($metaMessage) : '';
            }

            $msg->chat_id = $chat->id;

            if (isset($params['override_nick']) && !empty($params['override_nick'])) {
                $msg->name_support = (string)$params['override_nick'];
            } else {
                $msg->name_support = erLhcoreClassGenericBotWorkflow::getDefaultNick($chat);
            }

            $msg->user_id = isset($params['override_user_id']) && $params['override_user_id'] > 0 ? (int)$params['override_user_id'] : -2;
            $msg->time = time() + 1;

            if (!isset($params['do_not_save']) || $params['do_not_save'] == false) {
                erLhcoreClassChat::getSession()->save($msg);
            }
        }

        return $msg;
    }
}

?>