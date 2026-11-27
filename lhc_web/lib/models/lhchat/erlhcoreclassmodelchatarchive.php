<?php

#[\AllowDynamicProperties]
class erLhcoreClassModelChatArchive extends erLhcoreClassModelChat
{
    use erLhcoreClassDBTrait;

    public static $dbTable = null;

    public function beforeRemove()
    {
        parent::beforeRemove();
    }

    public function removeThis()
    {
        parent::removeThis();
    }

    public function afterRemove()
    {
        parent::afterRemove();

        foreach ([
                     erLhcoreClassModelChatArchiveRange::$archiveMsgTable,
                     erLhcoreClassModelChatArchiveRange::$archiveChatActionsTable,
                     erLhcoreClassModelChatArchiveRange::$archiveChatParticipantTable,
                     erLhcoreClassModelChatArchiveRange::$archiveChatSubjectTable] as $table) {
            $q = ezcDbInstance::get()->createDeleteQuery();
            $q->deleteFrom($table)->where($q->expr->eq('chat_id', $this->id));
            $stmt = $q->prepare();
            $stmt->execute();
        }

        // Delete group chat
        $groupChat = erLhcoreClassModelGroupChatArchive::findOne(array('filter' => array('chat_id' => $this->id)));

        if ($groupChat instanceof erLhcoreClassModelGroupChatArchive) {
            $groupChat->removeThis();
        }
    }
}

?>