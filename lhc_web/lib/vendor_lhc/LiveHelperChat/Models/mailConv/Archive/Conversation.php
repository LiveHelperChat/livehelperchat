<?php
namespace LiveHelperChat\Models\mailConv\Archive;
#[\AllowDynamicProperties]
class Conversation extends \erLhcoreClassModelMailconvConversation
{
    use \erLhcoreClassDBTrait;

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';
    
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

        $q = \ezcDbInstance::get()->createDeleteQuery();

        // Messages
        $q->deleteFrom(Range::$archiveConversationMsgTable)->where($q->expr->eq('conversation_id', $this->id));
        $stmt = $q->prepare();
        $stmt->execute();
    }

}

?>