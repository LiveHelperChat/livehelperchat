<?php

class erLhcoreClassModelCannedMsgTagLink
{
    use erLhcoreClassDBTrait;
    
    public static $dbTable = 'lh_canned_msg_tag_link';
    
    public static $dbTableId = 'id';
    
    public static $dbSessionHandler = 'erLhcoreClassChat::getSession';
    
    public static $dbSortOrder = 'DESC';
    
    public function getState()
    {
        return array(
            'id' => $this->id,
            'tag_id' => $this->tag_id,
            'canned_id' => $this->canned_id,
        );
    }

    public function __get($var)
    {
        switch ($var) {
             
            default:
                break;
        }
    }

    public static function formatSuggester($tags, $paramsExecution)
    {
        if (empty($tags)) {
            return array();
        }

        $tagLinks = self::getList(array('limit' => false,'filterin' => array('tag_id' => array_keys($tags))));

        $cannedMessagesIds = array();
        foreach ($tagLinks as $tagLink) {
            $cannedMessagesIds[] = $tagLink->canned_id;
        }

        $cannedMessagesAll = erLhcoreClassModelCannedMsg::getCannedMessages($paramsExecution['chat']->dep_id, $paramsExecution['user']->id, array('id' => $cannedMessagesIds));

        if (count($cannedMessagesAll) > 50) {
            $cannedMessagesAll = array_splice($cannedMessagesAll,0,50);
        }

        $chat = $paramsExecution['chat'];
        $user = $paramsExecution['user'];

        $replaceArray = array(
            '{nick}' => $chat->nick,
            '{email}' => $chat->email,
            '{phone}' => $chat->phone,
            '{operator}' => $user->name_support
        );

        $additionalData = $chat->additional_data_array;

        if (is_array($additionalData)) {
            foreach ($additionalData as $row) {
                if (isset($row['identifier']) && $row['identifier'] != '') {
                    $replaceArray['{' . $row['identifier'] . '}'] = $row['value'];
                }
            }
        }

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.workflow.canned_message_replace', array(
            'chat' => $chat,
            'replace_array' => & $replaceArray,
            'user' => $user,
            'items' => & $cannedMessagesAll
        ));

        foreach ($cannedMessagesAll as $item) {
            $item->setReplaceData($replaceArray);
        }

        $returnArray = array();

        $index = 0;
        foreach ($cannedMessagesAll as & $cannedMessage) {
            $cannedMessage->priority_index = $index;
            $index++;
        }

        $tagLinkGroups = array();
        foreach ($tagLinks as $tagLink) {
            if (isset($cannedMessagesAll[$tagLink->canned_id])) {
                $tagLinkGroups[$tagLink->tag_id][$cannedMessagesAll[$tagLink->canned_id]->priority_index] = $cannedMessagesAll[$tagLink->canned_id];
            }
        }

        foreach ($tagLinkGroups as $tagId => $cannedMessages) {
            ksort($cannedMessages); // Sort by canned message priority and title
            $tag =  $tags[$tagId];
            $tag->cnt = count($cannedMessages);
            $returnArray[$tags[$tagId]->tag] = array(
                'tag' => $tags[$tagId],
                'messages' => $cannedMessages
            );
        }

        // Sort by tag title
        ksort($returnArray);

        return $returnArray;
    }
    
    private $replaceData = array();

    public $id = null;

    public $tag_id = 0;    
    public $canned_id = 0;    
}

?>