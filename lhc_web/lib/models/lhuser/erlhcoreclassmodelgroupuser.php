<?php

class erLhcoreClassModelGroupUser
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_groupuser';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassUser::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'group_id' => $this->group_id,
            'user_id' => $this->user_id
        );
    }

    public static function removeUserFromGroups($user_id)
    {
        $session = erLhcoreClassUser::getSession();
        $q = $session->database->createDeleteQuery();
        $q->deleteFrom('lh_groupuser')
            ->where($q->expr->eq('user_id', $q->bindValue($user_id)));
        $stmt = $q->prepare();
        $stmt->execute();
    }

    public function __get($var)
    {
        switch ($var) {
            case 'user':
                try {
                    $this->user = erLhcoreClassModelUser::fetch($this->user_id);
                } catch (Exception $e) {
                    $this->user = 'Not exist';
                }
                return $this->user;
                break;

            default:
                break;
        }
    }

    public $id = null;
    public $group_id = '';
    public $user_id = '';
}


?>