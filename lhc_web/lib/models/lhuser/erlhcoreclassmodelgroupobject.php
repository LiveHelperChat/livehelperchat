<?php

class erLhcoreClassModelGroupObject
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_group_object';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassUser::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'group_id' => $this->group_id,
            'object_id' => $this->object_id,
            'type' => $this->type
        );
    }

    public function __get($param)
    {
        switch ($param) {

            case 'group':
                return $this->group = erLhcoreClassModelGroup::fetch($this->group_id);
                break;

            default:
                break;
        }
    }

    public static function getGroups($objectId, $type)
    {
        $db = ezcDbInstance::get();
        $stmt = $db->prepare("SELECT group_id FROM lh_group_object WHERE `object_id` = :object_id AND `type` = :type");
        $stmt->bindValue( ':object_id', $objectId);
        $stmt->bindValue( ':type', $type);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function getObjectsIdByUserId($userId, $type) {
        $db = ezcDbInstance::get();
        $stmt = $db->prepare("SELECT group_id FROM lh_groupuser WHERE `user_id` = :user_id");
        $stmt->bindValue( ':user_id', $userId);
        $stmt->execute();
        $groupId = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($groupId)){
            $stmt = $db->prepare("SELECT object_id FROM lh_group_object WHERE  `type` = :type AND `group_id` IN (". implode(',', $groupId) .")");
            $stmt->bindValue( ':type', $type);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        return array();
    }

    public $id = null;

    public $group_id = 0;

    public $object_id = 0;

    public $type = 0;
}

?>