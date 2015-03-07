<?php

class erLhcoreClassModelGroup
{

    public function getState()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'disabled' => $this->disabled
        );
    }

    public function setState(array $properties)
    {
        foreach ($properties as $key => $val) {
            $this->$key = $val;
        }
    }

    public static function fetch($group_id)
    {
        $group = erLhcoreClassUser::getSession('slave')->load('erLhcoreClassModelGroup', (int) $group_id);
        return $group;
    }

    public function removeThis()
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
        
        erLhcoreClassUser::getSession()->delete($this);
    }

    public static function getList($paramsSearch = array())
    {
        $paramsDefault = array(
            'limit' => 32,
            'offset' => 0
        );
        
        $params = array_merge($paramsDefault, $paramsSearch);
        
        $session = erLhcoreClassUser::getSession('slave');
        $q = $session->createFindQuery('erLhcoreClassModelGroup');
        
        $conditions = array();
        
        if (isset($params['filter']) && count($params['filter']) > 0) {
            foreach ($params['filter'] as $field => $fieldValue) {
                $conditions[] = $q->expr->eq($field, $q->bindValue($fieldValue));
            }
        }
        
        if (isset($params['filternot']) && count($params['filternot']) > 0) {
            foreach ($params['filternot'] as $field => $fieldValue) {
                $conditions[] = $q->expr->neq($field, $q->bindValue($fieldValue));
            }
        }
        
        if (isset($params['filterin']) && count($params['filterin']) > 0) {
            foreach ($params['filterin'] as $field => $fieldValue) {
                $conditions[] = $q->expr->in($field, $fieldValue);
            }
        }
        
        if (isset($params['filterlt']) && count($params['filterlt']) > 0) {
            foreach ($params['filterlt'] as $field => $fieldValue) {
                $conditions[] = $q->expr->lt($field, $q->bindValue($fieldValue));
            }
        }
        
        if (isset($params['filtergt']) && count($params['filtergt']) > 0) {
            foreach ($params['filtergt'] as $field => $fieldValue) {
                $conditions[] = $q->expr->gt($field, $q->bindValue($fieldValue));
            }
        }
        
        if (count($conditions) > 0) {
            $q->where($conditions);
        }
        
        $q->limit($params['limit'], $params['offset']);
        
        $q->orderBy(isset($params['sort']) ? $params['sort'] : 'id DESC');
        
        $objects = $session->find($q);
        
        return $objects;
    }

    public static function getCount($params = array())
    {
        $session = erLhcoreClassUser::getSession('slave');
        $q = $session->database->createSelectQuery();
        $q->select("COUNT(id)")->from("lh_group");
        
        if (isset($params['filter']) && count($params['filter']) > 0) {
            $conditions = array();
            
            foreach ($params['filter'] as $field => $fieldValue) {
                $conditions[] = $q->expr->eq($field, $q->bindValue($fieldValue));
            }
            
            $q->where($conditions);
        }
        
        $stmt = $q->prepare();
        $stmt->execute();
        $result = $stmt->fetchColumn();
        
        return $result;
    }

    public $id = null;

    public $name = '';

    public $disabled = 0;
}

?>