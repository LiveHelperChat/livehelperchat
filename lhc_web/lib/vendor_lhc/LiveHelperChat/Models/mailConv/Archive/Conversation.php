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
    }

    public $is_archive = true;

}

?>