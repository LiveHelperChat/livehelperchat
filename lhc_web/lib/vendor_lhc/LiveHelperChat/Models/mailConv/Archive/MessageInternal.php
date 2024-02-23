<?php
namespace LiveHelperChat\Models\mailConv\Archive;
#[\AllowDynamicProperties]
class MessageInternal extends \erLhcoreClassModelMailconvMessageInternal
{
    use \erLhcoreClassDBTrait;

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbTable = null;

    public function beforeRemove()
    {
        parent::beforeRemove();
    }

    public function afterRemove()
    {
        parent::afterRemove();
    }

    public $is_archive = true;

}

?>