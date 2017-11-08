<?php

/**
 * Status -
 * 0 - Pending
 * 1 - Active
 * 2 - Closed
 * 3 - Blocked
 * */
class erLhcoreClassRestAPIHandler
{

    public static function getHeaders()
    {
        if (! function_exists('getallheaders')) {
            if (! is_array($_SERVER)) {
                return array();
            }
            
            $headers = array();
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
            
            return $headers;
        } else {
            return getallheaders();
        }
    }

    public static function validateRequest()
    {
        $headers = self::getHeaders();
        
        if (isset($headers['Authorization'])) {
            
            $dataAuthorisation = explode(' ', $headers['Authorization']);
            $apiData = explode(':', base64_decode($dataAuthorisation[1]));
            
            if (count($apiData) != 2) {
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('lhrestapi/validation', 'Authorization failed!'));
            }
            
            $apiKey = erLhAbstractModelRestAPIKey::findOne(array(
                'enable_sql_cache' => true,
                'filter' => array(
                    'active' => 1,
                    'api_key' => $apiData[1]
                )
            ));
            
            if (! ($apiKey instanceof erLhAbstractModelRestAPIKey)) {
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('lhrestapi/validation', 'Authorization failed!'));
            }
            
            if ($apiKey->user->username != $apiData[0]) {
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('lhrestapi/validation', 'Authorization failed!'));
            }
            
            // API Key
            self::$apiKey = $apiKey;
            
            if (isset($_GET['update_activity'])) {
                erLhcoreClassUserDep::updateLastActivityByUser(self::$apiKey->user->id, time());
            }

        } else {
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('lhrestapi/validation', 'Authorization header is missing!'));
        }
    }

    public static function formatFilter($validAttributes)
    {
        $definition = array();
        
        foreach ($validAttributes as $attributeType => $attributes) {
            foreach ($attributes as $userAttribute => $definitionField) {
                $definition[$userAttribute] = $definitionField['validator'];
            }
        }
        
        $form = new ezcInputForm(INPUT_GET, $definition);
        $filter = array();
        
        foreach ($validAttributes as $attributeType => $attributes) {
            foreach ($attributes as $userAttribute => $definitionField) {
                if ($form->hasValidData($userAttribute)) {
                    if ($definitionField['type'] == 'filter') {
                        $filter['filter'][$definitionField['field']] = $form->$userAttribute;
                    } elseif ($definitionField['type'] == 'general') {
                        $filter[$definitionField['field']] = $form->$userAttribute;
                    }
                }
            }
        }
        
        $filter['limit'] = isset($filter['limit']) ? $filter['limit'] : 20;
        $filter['offset'] = isset($filter['offset']) ? $filter['offset'] : 0;
        $filter['smart_select'] = true;
        
        return $filter;
    }

    /**
     * Chat's list
     */
    public static function validateChatList()
    {
        $validAttributes = array(
            'int' => array(
                'departament_id' => array(
                    'type' => 'filter',
                    'field' => 'dep_id',
                    'validator' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', array(
                        'min_range' => 1
                    ))
                ),
                'user_id' => array(
                    'type' => 'filter',
                    'field' => 'user_id',
                    'validator' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', array(
                        'min_range' => 1
                    ))
                ),
                'status' => array(
                    'type' => 'filter',
                    'field' => 'status',
                    'validator' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', array(
                        'min_range' => 1
                    ))
                ),
                'limit' => array(
                    'type' => 'general',
                    'field' => 'limit',
                    'validator' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', array(
                        'min_range' => 1
                    ))
                ),
                'offset' => array(
                    'type' => 'general',
                    'field' => 'offset',
                    'validator' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', array(
                        'min_range' => 1
                    ))
                )
            )
        );
        
        $filterlt = array('id');
        
        $filter = self::formatFilter($validAttributes);
        
        if (isset($_GET['filtergt']['id']) && is_numeric($_GET['filtergt']['id'])){
            $filter['filtergt']['id'] = (int)$_GET['filtergt']['id'];
        }
        
        $limitation = self::getLimitation();
        
        // Does not have any assigned department
        if ($limitation === false) {
            return array(
                'list' => array(),
                'list_count' => 0
            );
        }
                
        if ($limitation !== true) {
            $filter['customfilter'][] = $limitation;
        }
        
        // Get chats list
        $chats = erLhcoreClassChat::getList($filter);
        
        // Get chats count
        $chatsCount = erLhcoreClassChat::getCount($filter);
        
        // Chats list
        return array(
            'list' => array_values($chats),
            'list_count' => $chatsCount,
            'error' => false
        );
    }

    public static function getLimitation($tableName = 'lh_chat')
    {
        if (self::$apiKey->user->all_departments == 0) {
            $userDepartaments = erLhcoreClassUserDep::getUserDepartaments(self::$apiKey->user->id);
            
            if (count($userDepartaments) == 0)
                return false;
            
            $LimitationDepartament = '(' . $tableName . '.dep_id IN (' . implode(',', $userDepartaments) . '))';
            
            return $LimitationDepartament;
        }
        
        return true;
    }

    /**
     * 
     * @param erLhcoreClassModelChat $chat
     * 
     * @return boolean
     */
    public static function hasAccessToRead(erLhcoreClassModelChat $chat)
    {            
        if ( self::$apiKey->user->all_departments == 0 ) {
      
            if ($chat->user_id == self::$apiKey->user->id) return true;
    
            $userDepartaments = erLhcoreClassUserDep::getUserDepartaments(self::$apiKey->user->id);
    
            if (count($userDepartaments) == 0) return false;
                
            if (in_array($chat->dep_id,$userDepartaments)) {
                                   
                if (self::hasAccessTo('lhchat','allowopenremotechat') == true || $chat->status == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT){
                    return true;
                } elseif ($chat->user_id == 0 || $chat->user_id == self::$apiKey->user->id) {
                    return true;
                }
    
                return false;
            }
    
            return false;
        }
    
        return true;
    }
    
    public static function hasAccessTo($module, $functions) 
    {
        $AccessArray = erLhcoreClassRole::accessArrayByUserID( self::$apiKey->user->id );
              
        // Global rights
        if (isset($AccessArray['*']['*']) || isset($AccessArray[$module]['*']))
        {
            return true;
        }
        
        // Provided rights have to be set
        if (is_array($functions))
        {
            foreach ($functions as $function)
            {
                // Missing one of provided right
                if (!isset($AccessArray[$module][$function])) return false;
            }
        } else {
            if (!isset($AccessArray[$module][$functions])) return false;
        }
        
        return true;
    }
    
    /*
     * Departaments
     */
    public static function validateDepartaments()
    {
        $departaments = erLhcoreClassModelDepartament::getList();
        $departamentsCount = erLhcoreClassModelDepartament::getCount();
        
        // Chats list
        return array(
            'list' => array_values($departaments),
            'list_count' => $departamentsCount,
            'error' => false
        );
    }

    /**
     * php array to xml conversion even for nested data
     *
     * @link http://stackoverflow.com/q/14136714/367456
     * @see http://stackoverflow.com/a/14143759/367456 for description
     * @author hakre <http://hakre.wordpress.com/credits>
     */
    public static function formatXML($data)
    {
        $createArrayImporter = function (SimpleXMLElement $subject) {
            $add = function (SimpleXMLElement $subject, $key, $value) use (&$add) {
                
                $addChildCdata = function ($name, $value = NULL, & $parent) {
                    $new_child = $parent->addChild($name);
                
                    if ($new_child !== NULL) {
                        $node = dom_import_simplexml($new_child);
                        $no   = $node->ownerDocument;
                        $node->appendChild($no->createCDATASection($value));
                    }
                
                    return $new_child;
                };
                
                $hasKey    = is_string($key);
                $isString  = is_string($value) || is_numeric($value);
                $isArray   = is_array($value);
                $count     = count($value);
                $isIndexed = $isArray && $count > 1 && array_keys($value) === range(0, $count - 1);
                $isKeyed   = $isArray && $count && !$isIndexed;
                switch (true) {
                    case $isString && $hasKey:
                                                
                        if (is_numeric($value) || empty($value)) {
                            return $subject->addChild($key, $value);
                        } else {
                            return $addChildCdata($key, $value, $subject);
                        }
                        
                    case $isIndexed && $hasKey:
                        foreach ($value as $oneof_value) {
                            $add($subject, $key, $oneof_value);
                        }
                        return $subject->$key;
                    case $isKeyed && $hasKey:
                        $subject = $subject->addChild($key);
                        // fall-through intended
                    case $isKeyed:
                        foreach ($value as $oneof_key => $oneof_value) {
                            $add($subject, $oneof_key, $oneof_value);
                        }
                        return true;
                    default:
                        //trigger_error('Unknown Nodetype ' . $key .print_r($value, 1));
                }
            };
            return function (Array $array) use ($subject, $add) {
                $add($subject, null, $array);
                return $subject;
            };
        };
        
        $xml      = new SimpleXMLElement('<root/>');
        $importer = $createArrayImporter($xml);
        
        $SimpleXML = $importer($data);
        
        $dom                     = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput       = true;
        $dom->loadXML($SimpleXML->asXML());
        
        return $dom->saveXML();
    }
    
    /**
     *
     * @param array $data            
     */
    public static function outputResponse($data)
    {
        if (isset($_GET['format']) && $_GET['format'] == 'xml') {
           echo self::formatXML(json_decode(json_encode($data),true));            
        } else {
        
            $json = json_encode($data, JSON_PRETTY_PRINT);
            
            if (isset($_GET['callback'])) {
                echo $_GET['callback'] . '(' . $json . ')';
            } else {
                echo $json;
            }
        }
    }

    private static $apiKey = null;
}

?>