<?php

class erLhcoreClassModelChatConfig {

   public static $disableCache = false;

   public $prevAttributes = [];

   public function getState()
   {
       return array(
               'identifier'    => $this->identifier,
               'value'         => $this->value,
               'type'          => $this->type,
               'hidden'        => $this->hidden,
               'explain'       => $this->explain
       );
   }

   public function afterSetState($attr) {
       $this->prevAttributes = $attr;
   }

   public static function fetchException($identifier)
   {
       $GLOBALS['lhc_erLhcoreClassModelChatConfig'.$identifier] = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChatConfig', $identifier );
       return $GLOBALS['lhc_erLhcoreClassModelChatConfig'.$identifier];
   }

   public static function fetch($identifier, $throwException = false)
   {
       if (self::$disableCache == false && isset($GLOBALS['lhc_erLhcoreClassModelChatConfig'.$identifier])) {
           return $GLOBALS['lhc_erLhcoreClassModelChatConfig'.$identifier];
       }
       try {
       $GLOBALS['lhc_erLhcoreClassModelChatConfig'.$identifier] = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChatConfig', $identifier );
       } catch (Exception $e) {

           if ($throwException === true) {
               throw $e;
           }

           // Record still does not exists, this happens during install
           $GLOBALS['lhc_erLhcoreClassModelChatConfig'.$identifier] = new erLhcoreClassModelChatConfig();
       }

       return $GLOBALS['lhc_erLhcoreClassModelChatConfig'.$identifier];
   }

   public static function fetchCache($identifier)
   {              
       if (self::$disableCache == false && isset($GLOBALS['lhc_erLhcoreClassModelChatConfig'.$identifier])) {
           return $GLOBALS['lhc_erLhcoreClassModelChatConfig'.$identifier];
       }
       
       $cache = CSCacheAPC::getMem();
       $configArray = $cache->getArray('lhc_chat_config');
       if (isset($configArray[$identifier])) {          
           return $configArray[$identifier];
       } else {
          $_SESSION['lhc_chat_config'][$identifier] = self::fetch($identifier);
          return $_SESSION['lhc_chat_config'][$identifier];
       }
   }
   
   public function saveThis()
   {
   	   if (isset($GLOBALS['lhc_erLhcoreClassModelChatConfig'.$this->identifier])){
   	   		unset($GLOBALS['lhc_erLhcoreClassModelChatConfig'.$this->identifier]);
   	   }
   	   
   	   if (isset($_SESSION['lhc_chat_config'][$this->identifier])) {
   	   		unset($_SESSION['lhc_chat_config'][$this->identifier]);
   	   }

       if ($this->explain != 'ignore' && json_encode($this->getState()) != json_encode($this->prevAttributes)) {
           erLhcoreClassLog::logObjectChange(array(
               'object' => $this,
               'check_log' => true,
               'msg' => array(
                   'curr' => $this->getState(),
                   'prev' => $this->prevAttributes,
                   'url' => (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''),
                   'user_id' => (PHP_SAPI !== 'cli' && erLhcoreClassUser::instance()->isLogged() ? erLhcoreClassUser::instance()->getUserID() : 0)
               )
           ));
       }

       erLhcoreClassChat::getSession()->saveOrUpdate( $this );
   }

   public function setState( array $properties )
   {
       foreach ( $properties as $key => $val )
       {
           $this->$key = $val;
       }

       $this->afterSetState($properties);
   }

   public function __get($variable)
   {
   		switch ($variable) {
   			case 'data':
   			        if ($this->value != '' && $this->value != '0'){
                        $this->data = unserialize($this->value);
                    } else {
                        $this->data = array();
                    }
   					return $this->data;
   				break;
   				
   			case 'data_value':
   					$this->data_value = $this->data;
   					return $this->data_value;
   				break;

   			case 'current_value':
   					switch ($this->type) {
   						case self::SITE_ACCESS_PARAM_ON:
   							$this->current_value = null;
   							if ($this->value != '')
   							{
   								$this->current_value = isset($this->data[erLhcoreClassSystem::instance()->SiteAccess]) ? $this->data[erLhcoreClassSystem::instance()->SiteAccess] : null;
   							}
   							return $this->current_value;
   							break;

   						case self::SITE_ACCESS_PARAM_OFF:
   								$this->current_value = $this->value;
   								return $this->current_value;
   							break;

   						default:
   							break;
   					}
   					$this->data = unserialize($this->value);
   					return $this->data;
   				break;

   			default:
   				break;
   		}
   }

   public static function getItems($paramsSearch = array())
   {
       $paramsDefault = array('limit' => 100, 'offset' => 0);

       $params = array_merge($paramsDefault,$paramsSearch);

       $session = erLhcoreClassChat::getSession();
       $q = $session->createFindQuery( 'erLhcoreClassModelChatConfig' );

       $conditions = array();
       $conditions[] = $q->expr->eq( 'hidden', 0 );

       if (isset($params['filter']) && count($params['filter']) > 0)
       {
           foreach ($params['filter'] as $field => $fieldValue)
           {
               $conditions[] = $q->expr->eq( $field, $q->bindValue($fieldValue) );
           }
      }

      if (isset($params['filterin']) && count($params['filterin']) > 0)
       {
           foreach ($params['filterin'] as $field => $fieldValue)
           {
               $conditions[] = $q->expr->in( $field, $fieldValue );
           }
      }

      if (isset($params['filterlt']) && count($params['filterlt']) > 0)
       {
           foreach ($params['filterlt'] as $field => $fieldValue)
           {
               $conditions[] = $q->expr->lt( $field, $q->bindValue($fieldValue) );
           }
      }

      if (isset($params['filtergt']) && count($params['filtergt']) > 0)
       {
           foreach ($params['filtergt'] as $field => $fieldValue)
           {
               $conditions[] = $q->expr->gt( $field, $q->bindValue($fieldValue) );
           }
      }

      if (count($conditions) > 0)
      {
          $q->where(
                     $conditions
          );
      }

      $q->limit($params['limit'],$params['offset']);
      $q->orderBy(isset($params['sort']) ? $params['sort'] : 'identifier DESC' );

      $objects = $session->find( $q );

      return $objects;
   }

   public $identifier = null;
   public $value = null;
   public $explain = null;
   public $hidden = 0;

   const SITE_ACCESS_PARAM_ON = 1;
   const SITE_ACCESS_PARAM_OFF = 0;

}


?>