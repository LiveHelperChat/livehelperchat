<?php

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

        $q = ezcDbInstance::get()->createDeleteQuery();

        // Messages
        $q->deleteFrom(erLhcoreClassModelChatArchiveRange::$archiveMsgTable)->where($q->expr->eq('chat_id', $this->id));
        $stmt = $q->prepare();
        $stmt->execute();
    }

}

?>