<?php
namespace LiveHelperChat\Models\mailConv\Archive;
#[\AllowDynamicProperties]
class File extends \erLhcoreClassModelMailconvFile
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