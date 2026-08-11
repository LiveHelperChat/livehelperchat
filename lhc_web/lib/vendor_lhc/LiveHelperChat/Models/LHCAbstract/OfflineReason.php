<?php

namespace LiveHelperChat\Models\LHCAbstract;
#[\AllowDynamicProperties]
class OfflineReason
{
    use \erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_offline_reason';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public static $dbDefaultSort = '`pos` DESC, `name` ASC';

    public $has_filter = false;

    public $hide_add = false;

    public $hide_delete = true;

    public function getState()
    {
        return array(
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'icon'        => $this->icon,
            'pos'         => $this->pos,
        );
    }

    public function __toString()
    {
        return $this->name;
    }

    public function updateThis()
    {
        $this->saveThis();
    }

    public function getFields()
    {
        return include('lib/core/lhabstract/fields/erlhabstractmodelofflinereason.php');
    }

    public function getModuleTranslations()
    {
        return array(
            'permission_delete' => array('module' => 'lhabstract', 'function' => 'use'),
            'permission'        => array('module' => 'lhuser', 'function' => 'offlinereasons'),
            'name'              => \erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/offlinereason', 'Offline Reason'),
        );
    }

    public $id          = null;
    public $name        = '';
    public $description = '';
    public $icon        = '';
    public $pos         = 0;
}
