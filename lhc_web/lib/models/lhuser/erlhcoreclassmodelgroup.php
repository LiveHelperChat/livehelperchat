<?php

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
            'name' => $this->name,
            'disabled' => $this->disabled,
            'required' => $this->required
        );
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

    public $name = '';

    public $disabled = 0;

    public $required = 0;
}

?>