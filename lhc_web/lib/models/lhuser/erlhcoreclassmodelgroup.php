<?php
#[\AllowDynamicProperties]
class erLhcoreClassModelGroup
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_group';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassUser::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'muser_id' => $this->muser_id,
            'name' => $this->name,
            'disabled' => $this->disabled,
            'required' => $this->required
        );
    }

    public function __toString()
    {
        return $this->name;
    }

    public function afterRemove()
    {
        $q = ezcDbInstance::get()->createDeleteQuery();

        // Transfered chats to user
        $q->deleteFrom('lh_groupuser')->where($q->expr->eq('group_id', $this->id));
        $stmt = $q->prepare();
        $stmt->execute();

        // Transfered chats to user
        $q->deleteFrom('lh_grouprole')->where($q->expr->eq('group_id', $this->id));
        $stmt = $q->prepare();
        $stmt->execute();
    }

    public $id = null;
    public $created_at = 0;
    public $updated_at = 0;
    public $muser_id = 0;

    public $name = '';

    public $disabled = 0;

    public $required = 0;
}

?>