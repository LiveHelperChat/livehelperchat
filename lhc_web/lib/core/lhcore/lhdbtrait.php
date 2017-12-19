<?php

trait erLhcoreClassDBTrait {
		
	public function setState( array $properties ) {
		foreach ( $properties as $key => $val ) {
			$this->$key = $val;
		}
	}
	
	public function saveThis() {
		$this->beforeSave();
		self::getSession()->saveOrUpdate($this);
		$this->afterSave();
		$this->clearCache();
	}
	
	public function saveOrUpdate() {
		$this->beforeSave();
		self::getSession()->saveOrUpdate($this);
		$this->afterSave();
		$this->clearCache();
	}
	
	public function updateThis() {
		$this->beforeUpdate();
		self::getSession()->update($this);	
		$this->afterUpdate();
		$this->clearCache();		
	}
		
	public function removeThis() {
		$this->beforeRemove();				
		self::getSession()->delete($this);
		$this->afterRemove();
		$this->clearCache();
	}

	public function syncAndLock() {
	    
	    $db = ezcDbInstance::get();
	    
	    $stmt = $db->prepare('SELECT * FROM ' . self::$dbTable . ' WHERE id = :id FOR UPDATE;');
	    $stmt->bindValue(':id',$this->id);
	    $stmt->execute();
	    
	    $data = $stmt->fetch(PDO::FETCH_ASSOC);
	    
	    $this->setState($data);	
	}
		
	public function beforeSave() {
	
	}
	
	public function beforeUpdate() {
	
	}
	
	public function beforeRemove() {
	
	}
	
	public function afterSave() {
	
	}
	
	public function afterUpdate() {
	
	}
	
	public function afterRemove() {
	
	}

	public function refreshThis() {
		self::getSession()->refresh($this);
	}
	
	public function clearCache() {
		
		$cache = CSCacheAPC::getMem ();
		$cache->increaseCacheVersion('site_attributes_version_'.strtolower(__CLASS__));
		$cache->delete('object_'.strtolower(__CLASS__).'_'.$this->id);
		
		if (isset($GLOBALS[__CLASS__.$this->id])) {
			unset($GLOBALS[__CLASS__.$this->id]);
		}
		
		$this->clearCacheClassLevel();
		
	}
	
	public function clearCacheClassLevel() {
		
	}
		
	public function getFields() {
		 return include 'lib/core/lhabstract/fields/'.strtolower(__CLASS__).'.php';
	}
	
	public static function getSession()	{
		
		static $dbHandler = false;
		
		$url = './';
		if (isset(self::$dbSessionHandlerUrl) && self::$dbSessionHandlerUrl != '') {
		    $url = self::$dbSessionHandlerUrl;
		} 
		
		if ($dbHandler === false) {
			$dbHandler = call_user_func(self::$dbSessionHandler, $url);
		}
		
		return $dbHandler;
		
	}
	
	public static function fetch($id, $useCache = true) {
	
		if (isset($GLOBALS[__CLASS__.$id]) && $useCache == true) return $GLOBALS[__CLASS__.$id];

		try {
			$GLOBALS[__CLASS__.$id] = self::getSession()->load( __CLASS__, $id );
		} catch (Exception $e) {
			$GLOBALS[__CLASS__.$id] = false;
		}
	
		return $GLOBALS[__CLASS__.$id];
		
	}
	
	public static function fetchAndLock($id, $useCache = true)
	{
	    if (isset($GLOBALS[__CLASS__.$id]) && $useCache == true) return $GLOBALS[__CLASS__.$id];
	    
	    try {
	        $GLOBALS[__CLASS__.$id] = self::getSession()->loadAndLock( __CLASS__, $id );
	    } catch (Exception $e) {
	        $GLOBALS[__CLASS__.$id] = false;
	    }
	    
	    return $GLOBALS[__CLASS__.$id];
	}
	
	/**
	 * Similar to above just uses memcache if available
	 * */
	public static function fetchCache($id) {
		
		$cache = CSCacheAPC::getMem();
		$cacheKey = 'object_'.strtolower(__CLASS__).'_'.$id;
			
		if (($object = $cache->restore($cacheKey)) === false) {	
			$object = self::fetch($id,true);
			$cache->store($cacheKey,$object);			
		}		
		
		return $object;
		
	}
		
	public static function isOwner($id, $skipChecking = false) {
		
		$obj = self::fetch($id);
	
		if ($skipChecking == true)
			return $obj;
	
		$currentUser = erLhcoreClassUser::instance();
		if ($obj->user_id == $currentUser->getUserID())
			return $obj;
	
		return false;
		
	}
	
	public static function findOne($paramsSearch = array()) {
				
		$paramsSearch['limit'] = 1;
		$list = self::getList($paramsSearch);
		if (!empty($list)){
			reset($list);
			return current($list);
		}
			
		return false;	
			
	}
	
	public static function getCount($params = array(), $operation = 'COUNT', $field = false, $rawSelect = false) {
	
		if (isset($params['enable_sql_cache']) && $params['enable_sql_cache'] == true) {
			$sql = erLhcoreClassModuleFunctions::multi_implode(',',$params);
		
			$cache = CSCacheAPC::getMem();
			$cacheKey = isset($params['cache_key']) ? md5($operation.$field.$sql.$params['cache_key']) : md5('objects_count_'.strtolower(__CLASS__).'_v_'.$cache->getCacheVersion('site_attributes_version_'.strtolower(__CLASS__)).$sql.$operation.$field);
		
			if (($result = $cache->restore($cacheKey)) !== false)
			{
				return $result;
			}
		}
				
		$session = self::getSession();
	
		$q = $session->database->createSelectQuery();

        if ($rawSelect === false) {
            $q->select($operation . "(" . self::$dbTable . "." . ($field === false ? self::$dbTableId : $field) . ")")->from(self::$dbTable);
        } else {
            $q->select($rawSelect)->from(self::$dbTable);
        }

		$conditions = self::getConditions($params, $q);
	
		if (count($conditions) > 0) {
			$q->where( $conditions );
		}

		$stmt = $q->prepare();
	
		$stmt->execute();
	
		$result = $stmt->fetchColumn();
		 
		if (isset($params['enable_sql_cache']) && $params['enable_sql_cache'] == true) {
			$cache->store($cacheKey,$result);
		}
		
		return $result;
		
	}
					
	public static function getList($paramsSearch = array()) {
	
		$paramsDefault = array('limit' => 500, 'offset' => 0);
			
		$params = array_merge($paramsDefault,$paramsSearch);
		
		if (isset($params['enable_sql_cache']) && $params['enable_sql_cache'] == true)
		{
			$sql = self::multi_implode(',',$params);

			$cache = CSCacheAPC::getMem();
			$cacheKey = isset($params['cache_key']) ? md5($sql.$params['cache_key']) : md5('objects_list_'.strtolower(__CLASS__).'_v_'.$cache->getCacheVersion('site_attributes_version_'.strtolower(__CLASS__)).$sql);

			if (($result = $cache->restore($cacheKey)) !== false)
			{
				return $result;
			}
		}
				
		$session = self::getSession();
				
		$q = $session->createFindQuery( __CLASS__, isset($params['ignore_fields']) ? $params['ignore_fields'] : array() );

		$conditions = self::getConditions($params, $q);
	
		if (count($conditions) > 0) {
			$q->where( $conditions );
		}
	
		if (isset($params['lock']) && $params['lock'] == true)
		{
		    $q->doLock();
		}
		
		if ($params['limit'] !== false) {
			$q->limit($params['limit'],$params['offset']);
		}
	
		if (!isset($params['sort']) || $params['sort'] !== false)
		{
			if (isset(self::$dbDefaultSort)){
				$q->orderBy(isset($params['sort']) ? $params['sort'] : self::$dbDefaultSort );
			} else {
				$q->orderBy(isset($params['sort']) ? $params['sort'] : self::$dbTable. "." . self::$dbTableId . " " . self::$dbSortOrder );
			}
		}
		
		$objects = $session->find( $q );
			
		if (isset($params['prefill_attributes'])) {
			foreach ($params['prefill_attributes'] as $attr => $prefillOptions)
			{
				$teamsId = array();
				foreach ($objects as $object) {
					$teamsId[] = $object->$prefillOptions['attr_id'];
				}
				
				if (!empty($teamsId)) {
					$teams = call_user_func($object->$prefillOptions['function'],array('limit' => false, 'sort' => false, 'filterin' => array('id' =>$teamsId)));
					foreach ($objects as & $object) {
						if (isset($teams[$object->$prefillOptions['attr_id']])){
							$object->$prefillOptions['attr_name'] = $teams[$object->$prefillOptions['attr_id']];
						}
						
					}
				}
				
			}
		}
		
		if (isset($params['enable_sql_cache']) && $params['enable_sql_cache'] == true) {
			if (isset($params['sql_cache_timeout'])) {
				$cache->store($cacheKey,$objects,$params['sql_cache_timeout']);
			} else {
				$cache->store($cacheKey,$objects);
			}			
		}
		
		return $objects;
	}

	public static function getConditions($params, $q) {
	
	    $conditions = array();
	
	    if (isset($params['filter']) && count($params['filter']) > 0) {
	        foreach ($params['filter'] as $field => $fieldValue) {
	            $conditions[] = $q->expr->eq($field, $q->bindValue($fieldValue));
	        }
	    }
	
	    if (isset($params['filterin']) && count($params['filterin']) > 0) {
	        foreach ($params['filterin'] as $field => $fieldValue) {
	            if (empty($fieldValue)) {
	                return 0;
	                break;
	            }
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
	
	    if (isset($params['filtergte']) && count($params['filtergte']) > 0) {
	        foreach ($params['filtergte'] as $field => $fieldValue) {
	            $conditions[] = $q->expr->gte($field, $fieldValue);
	        }
	    }
	
	    if (isset($params['filterlte']) && count($params['filterlte']) > 0) {
	        foreach ($params['filterlte'] as $field => $fieldValue) {
	            $conditions[] = $q->expr->lte($field, $fieldValue);
	        }
	    }
	
	    if (isset($params['filternot']) && count($params['filternot']) > 0) {
	        foreach ($params['filternot'] as $field => $fieldValue) {
	            $conditions[] = $q->expr->neq($field, $q->bindValue($fieldValue));
	        }
	    }
	
	    if (isset($params['filterall']) && count($params['filterall']) > 0) {
	        foreach ($params['filterall'] as $field => $fieldValue) {
	            $conditions[] = $q->expr->allin($field, $fieldValue);
	        }
	    }
	
	    if (isset($params['filterlike']) && count($params['filterlike']) > 0) {
	        foreach ($params['filterlike'] as $field => $fieldValue) {
	            $conditions[] = $q->expr->like($field, $q->bindValue('%' . $fieldValue . '%'));
	        }
	    }
	
	    if (isset($params['filterlikeright']) && count($params['filterlikeright']) > 0) {
	        foreach ($params['filterlikeright'] as $field => $fieldValue) {
	            $conditions[] = $q->expr->like($field, $q->bindValue($fieldValue . '%'));
	        }
	    }
	
	    if (isset($params['leftjoin']) && count($params['leftjoin']) > 0) {
	        foreach ($params['leftjoin'] as $table => $joinOn) {
	            $q->leftJoin($table, $q->expr->eq($joinOn[0], $joinOn[1]));
	        }
	    }
	
	    if (isset($params['innerjoinsame']) && count($params['innerjoinsame']) > 0) {
	        foreach ($params['innerjoinsame'] as $table => $joinOn) {
	            $q->innerJoin($q->alias($table, 't2'), $q->expr->eq($joinOn[0], $joinOn[1]));
	        }
	    }
	
	    if (isset($params['filterlor']) && count($params['filterlor']) > 0) {
	        	
	        $conditionsLor = array();
	        	
	        foreach ($params['filterlor'] as $field => $fieldValue) {
	            foreach ($fieldValue as $fv) {
	                $conditionsLor[] = $q->expr->eq($field, $q->bindValue($fv));
	            }
	        }
	        	
	        $conditions[] = $q->expr->lOr($conditionsLor);
	
	    }
	
	    if (isset($params['filterlorf']) && count($params['filterlorf']) > 0) {
	        	
	        $conditionsLor = array();
	        	
	        foreach ($params['filterlorf'] as $field => $fieldValue) {
	            foreach ($fieldValue as $fv) {
	                $conditionsLor[] = $q->expr->eq($fv, $q->bindValue($field));
	            }
	        }
	        	
	        $conditions[] = $q->expr->lOr($conditionsLor);
	
	    }
	
	    if (isset($params['filternotin']) && count($params['filternotin']) > 0) {
	        	
	        foreach ($params['filternotin'] as $field => $fieldValue) {
	            if (! empty($fieldValue)) {
	                $conditions[] = $q->expr->not($q->expr->in($field, $fieldValue));
	            }
	        }
	
	    }
	
	    if (isset($params['filter_custom']) && count($params['filter_custom']) > 0) {
	        foreach ($params['filter_custom'] as $fieldValue) {
	            $conditions[] = $fieldValue;
	        }
	    }
	    
	    if (isset($params['customfilter']) && count($params['customfilter']) > 0)
	    {
	        foreach ($params['customfilter'] as $fieldValue)
	        {
	            $conditions[] = $fieldValue;
	        }
	    }
	    
	    if (isset($params['innerjoin']) && count($params['innerjoin']) > 0) {
	        foreach ($params['innerjoin'] as $table => $joinOn) {
	            $q->innerJoin($table, $q->expr->eq($joinOn[0], $joinOn[1]));
	        }
	    }
	    
	    if(isset($params['group']) && $params['group'] != '') {
	        $q->groupBy($params['group']);
	    }
	    
	    if (isset($params['having']) && $params['having'] != '') {
	        $q->having($params['having']);
	    }
	    
	    if (isset($params['use_index'])) {
	        $q->useIndex( $params['use_index'] );
	    }
	    
	    if (isset($params['select_columns']) && !empty($params['select_columns'])) {
	        $q->select($params['select_columns']);
	    }
	    
	    return $conditions;
	}
	
	public static function multi_implode($glue, $pieces, $key = null) {
	
	    $string = '';
	
	    if (is_array($pieces)) {
	        reset($pieces);
	        while (list ($key, $value) = each($pieces)) {
	            $string .= $key.'_'.$glue . self::multi_implode($glue, $value, $key);
	        }
	    } else {
	        return "{$key}_{$pieces}";
	    }
	
	    return trim($string, $glue);
	}		
}

?>