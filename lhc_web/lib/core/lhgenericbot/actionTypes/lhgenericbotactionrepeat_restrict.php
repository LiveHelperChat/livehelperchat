<?php

class erLhcoreClassGenericBotActionRepeat_restrict {

    public static function process($chat, $action, $trigger, $params)
    {
        if (isset($action['content']['repeat_count']) && is_numeric($action['content']['repeat_count']) && $action['content']['repeat_count'] > 0) {

            $filterId = 'trigger_id';
            $filterValue = $trigger->id;

            if (isset($action['content']['identifier']) && $action['content']['identifier'] != '') {
                $filterId = 'identifier';
                $filterValue = $action['content']['identifier'];
            }

            $restrict = erLhcoreClassModelGenericBotRepeatRestrict::findOne(array('filter' => array($filterId => $filterValue, 'chat_id' => $chat->id)));

            if (!($restrict instanceof erLhcoreClassModelGenericBotRepeatRestrict)) {
                $restrict = new erLhcoreClassModelGenericBotRepeatRestrict();
                $restrict->chat_id = $chat->id;
                $restrict->{$filterId} = $filterValue;
            }

            $restrict->counter++;
            $restrict->saveThis();

            if ($restrict->counter <= $action['content']['repeat_count'])
            {
                return null;
            }

            if (is_numeric($action['content']['alternative_callback']) && $action['content']['alternative_callback'] > 0){
                return array(
                    'status' => 'stop',
                    'trigger_id' => $action['content']['alternative_callback']
                );
            } else {
                return array(
                    'status' => 'stop',
                    'ignore_trigger' => true
                );
            }
        }

        return null;
    }
}

?>