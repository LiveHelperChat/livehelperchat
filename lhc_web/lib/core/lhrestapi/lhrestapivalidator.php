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
    public static function executeRequest(erLhAbstractModelRestAPIKeyRemote $apiKey, $function, $params = array(), $uparams = array(), $method = 'GET', $manualAppend = '')
    {
        $ch = curl_init();
        $headers = array('Accept' => 'application/json');

        $uparamsArg = '';

        if (!empty($uparams) && is_array($uparams)) {
            $parts = array();
            foreach ($uparams as $param => $value) {
                $parts[] = '/('.$param .')/'.$value;
            }
            $uparamsArg = implode('', $parts);

        }

        $requestArgs = ($method == 'GET') ? '?' .http_build_query($params) : '';

        if ($method == 'POST') {
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$params);
        }

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $apiKey->username . ':' . $apiKey->api_key);
        curl_setopt($ch, CURLOPT_URL, $apiKey->host . $function . $manualAppend . $uparamsArg . $requestArgs);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $content = curl_exec($ch);

        return $content;
    }
    
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

    public static function getRequestMethod()
    {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] != '') {
            return $_SERVER['REQUEST_METHOD'];
        }

        return false;
    }

    public static function setHeaders($content = 'Content-Type: application/json')
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, API-Key, Authorization');
        header($content);
        self::setOptionHeaders();
    }

    public static function setOptionHeaders(){
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            header("Access-Control-Max-Age: 1728000");

            exit(0);
        }
    }

    public static function validateRequest()
    {
        self::setHeaders();

        $headers = self::getHeaders();

        if (isset($headers['Authorization'])) {
            
            $dataAuthorisation = explode(' ', $headers['Authorization']);
            $apiData = explode(':', base64_decode($dataAuthorisation[1]));
            
            if (count($apiData) != 2) {
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('lhrestapi/validation', 'Authorization failed!'));
            }

            // There is no current workflow in progress
            $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('rest_api.validate_request', array(
                'headers' => $headers,
            ));

            if ($handler !== false) {
                $apiKey = $handler['api_key'];
            } else {
                $apiKey = erLhAbstractModelRestAPIKey::findOne(array(
                    'enable_sql_cache' => true,
                    'filter' => array(
                        'active' => 1,
                        'api_key' => $apiData[1]
                    )
                ));
            }

            $authorised = false;
            $user = null;

            if (!($apiKey instanceof erLhAbstractModelRestAPIKey)) {
                $user = erLhcoreClassModelUser::findOne(array('filter' => array('username' => $apiData[0])));
                if (!($user instanceof erLhcoreClassModelUser) || !password_verify($apiData[1], $user->password)) {
                    throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('lhrestapi/validation', 'Authorization failed!'));
                } else {
                    if (!$user->hasAccessTo('lhrestapi','use_direct_logins')){
                        throw new Exception(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('lhrestapi/validation', 'You do not have permission to use REST API directly. "lhrestapi", "use_direct_logins" is missing!')));
                    } else {
                        $authorised = true;
                    }
                }
            }

            if ($authorised === false && $apiKey->user->username != $apiData[0]) {
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('lhrestapi/validation', 'Authorization failed!'));
            }

            if ($user instanceof erLhcoreClassModelUser){
                self::$apiKey = new erLhAbstractModelRestAPIKey();
                self::$apiKey->user = $user;
            } else {
                // API Key
                self::$apiKey = $apiKey;
            }

            if (isset($_GET['update_activity'])) {
                erLhcoreClassUserDep::updateLastActivityByUser(self::$apiKey->user->id, time());
            }

        } else {
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('lhrestapi/validation', 'Authorization header is missing!'));
        }

        return true;
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
                    } else if ($definitionField['type'] == 'filtergt') {
                        $filter['filtergt'][$definitionField['field']] = $form->$userAttribute;
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

    public static function validateCampaignConversionList()
    {
        $validAttributes = array(
            'int' => array(
                'department_id' => array(
                    'type' => 'filter',
                    'field' => 'department_id',
                    'validator' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', array(
                        'min_range' => 1
                    ))
                ),
                'campaign_id' => array(
                    'type' => 'filter',
                    'field' => 'campaign_id',
                    'validator' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', array(
                        'min_range' => 1
                    ))
                ),
                'invitation_id' => array(
                    'type' => 'filter',
                    'field' => 'invitation_id',
                    'validator' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', array(
                        'min_range' => 1
                    ))
                ),
                'chat_id' => array(
                    'type' => 'filter',
                    'field' => 'chat_id',
                    'validator' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', array(
                        'min_range' => 1
                    ))
                ),
                'ctime' => array(
                    'type' => 'filtergt',
                    'field' => 'ctime',
                    'validator' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', array(
                        'min_range' => 1
                    ))
                ),
                'con_time' => array(
                    'type' => 'filtergt',
                    'field' => 'con_time',
                    'validator' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', array(
                        'min_range' => 1
                    ))
                ),
                'id' => array(
                    'type' => 'filtergt',
                    'field' => 'id',
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

        $filter = self::formatFilter($validAttributes);

        if (isset($_GET['invitation_status']) && $_GET['invitation_status'] != '') {
            $statusLiteral = explode(',',$_GET['invitation_status']);
            $statusMap = array(
                'send' => erLhAbstractModelProactiveChatCampaignConversion::INV_SEND,
                'shown' => erLhAbstractModelProactiveChatCampaignConversion::INV_SHOWN,
                'seen' => erLhAbstractModelProactiveChatCampaignConversion::INV_SEEN,
                'chat_started' => erLhAbstractModelProactiveChatCampaignConversion::INV_CHAT_STARTED
            );

            $statuses = array();
            foreach ($statusLiteral as $item){
                if (isset($statusMap[$item])){
                    $statuses[] = $statusMap[$item];
                }
            }

            if (!empty($statuses)) {
                $filter['filterin']['invitation_status'] = $statuses;
            }
        }
        // 0 - PC, 1 - mobile, 2 - tablet
        if (isset($_GET['device_type']) && $_GET['device_type'] != '') {
            $statusLiteral = explode(',',$_GET['device_type']);
            $statusMap = array(
                'pc' => 0,
                'mobile' => 1,
                'tablet' => 2,
            );

            $statuses = array();
            foreach ($statusLiteral as $item){
                if (isset($statusMap[$item])){
                    $statuses[] = $statusMap[$item];
                }
            }

            if (!empty($statuses)) {
                $filter['filterin']['device_type'] = $statuses;
            }
        }

        if (isset($_GET['invitation_type']) && $_GET['invitation_type'] != '') {
            $statusLiteral = $_GET['invitation_type'];
            $statusMap = array(
                'operator' => 2,
                'system' => 1,
            );

            if (isset($statusMap[$statusLiteral])){
                $filter['filter']['invitation_type'] = $statusMap[$statusLiteral];
            }
        }

        $filter['sort'] = 'id ' . ((isset($_GET['sort']) && $_GET['sort'] == 'desc') ? 'DESC' : 'ASC');

        // Get chats list
        $campaignsConversions = erLhAbstractModelProactiveChatCampaignConversion::getList($filter);

        // Get chats count
        $chatsCount = erLhAbstractModelProactiveChatCampaignConversion::getCount($filter);

        if (isset($_GET['include_invitation']) && $_GET['include_invitation'] == 'true') {
            erLhcoreClassChat::prefillObjects($campaignsConversions,array(
                array(
                    'invitation_id',
                    'invitation',
                    'erLhAbstractModelProactiveChatInvitation::getList'
                ),
            ));
        }

        if (isset($_GET['include_invitation']) && $_GET['include_invitation'] == 'true') {
            erLhcoreClassChat::prefillObjects($campaignsConversions,array(
                array(
                    'invitation_id',
                    'invitation',
                    'erLhAbstractModelProactiveChatInvitation::getList'
                ),
            ));
        }

        if (isset($_GET['include_onlinevisitor']) && $_GET['include_onlinevisitor'] == 'true') {
            erLhcoreClassChat::prefillObjects($campaignsConversions,array(
                array(
                    'vid_id',
                    'vid',
                    'erLhcoreClassModelChatOnlineUser::getList'
                ),
            ));
        }

        if (isset($_GET['include_department']) && $_GET['include_department'] == 'true') {
            erLhcoreClassChat::prefillObjects($campaignsConversions,array(
                array(
                    'department_id',
                    'department',
                    'erLhcoreClassModelDepartament::getList'
                ),
            ));
        }

        // Chats list
        return array(
            'list' => array_values($campaignsConversions),
            'list_count' => $chatsCount,
            'error' => false
        );
    }

    public static function getChatListFilter()
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
                'phone' => array(
                    'type' => 'filter',
                    'field' => 'phone',
                    'validator' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
                ),
                'email' => array(
                    'type' => 'filter',
                    'field' => 'email',
                    'validator' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
                ),
                'nick' => array(
                    'type' => 'filter',
                    'field' => 'nick',
                    'validator' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
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

        $filter = self::formatFilter($validAttributes);

        if (isset($_GET['filtergt']['id']) && is_numeric($_GET['filtergt']['id'])) {
            $filter['filtergt']['id'] = (int)$_GET['filtergt']['id'];
        }

        if (isset($_GET['departament_ids'])) {
            $idDep = explode(',',$_GET['departament_ids']);
            erLhcoreClassChat::validateFilterIn($idDep);
            if (!empty($idDep)){
                $filter['filterin']['dep_id'] = $idDep;
            }
        }

        if (isset($_GET['departament_groups_ids'])) {
            $idDep = explode(',',$_GET['departament_groups_ids']);
            erLhcoreClassChat::validateFilterIn($idDep);
            if (!empty($idDep)){
                $groups = erLhcoreClassModelDepartamentGroup::getList(array('filterin' => array('id' => $idDep)));
                foreach ($groups as $group) {
                    $depIds = $group->departments_ids;
                    if (!empty($depIds)) {
                        if (isset($filter['filterin']['dep_id'])) {
                            $filter['filterin']['dep_id'] = array_merge($filter['filterin']['dep_id'], $depIds);
                        } else {
                            $filter['filterin']['dep_id'] = $depIds;
                        }
                    }
                }
            }
        }

        if (isset($_GET['id_gt']) && is_numeric($_GET['id_gt'])) {
            $filter['filtergt']['id'] = (int)$_GET['id_gt'];
        }

        if (isset($_GET['time_gt']) && is_numeric($_GET['time_gt'])) {
            $filter['filtergt']['time'] = (int)$_GET['time_gt'];
        }

        if (isset($_GET['delay']) && is_numeric($_GET['delay'])) {
            $filter['filterlte']['time'] = time()-(int)$_GET['delay'];
        }

        if (isset($_GET['last_user_msg_time_gt']) && is_numeric($_GET['last_user_msg_time_gt'])) {
            $filter['filtergt']['last_user_msg_time'] = (int)$_GET['last_user_msg_time_gt'];
        }

        $groupFields = array();

        if (isset($_GET['group_by_nick']) && $_GET['group_by_nick'] == 'true') {
            $groupFields[] = '`nick`';
        }

        if (isset($_GET['group_by_phone']) && $_GET['group_by_phone'] == 'true') {
            $groupFields[] = '`phone`';
        }

        if (isset($_GET['group_by_email']) && $_GET['group_by_email'] == 'true') {
            $groupFields[] = '`email`';
        }

        if (!empty($groupFields)) {
            $filter['group'] = implode(', ', $groupFields);
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

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('restapi.chats_filter', array('filter' => & $filter));

        return $filter;
    }

    public static function validateChatListCount()
    {
        $filter = self::getChatListFilter();

        if (isset($filter['limit'])) {
            unset($filter['limit']);
        }

        // Get chats count
        $chatsCount = erLhcoreClassModelChat::getCount($filter);

        // Chats list
        return array(
            'list_count' => $chatsCount,
            'error' => false
        );
    }
    /**
     * Chat's list
     */
    public static function validateChatList()
    {

        $filter = self::getChatListFilter();
        
        // Get chats list
        $chats = erLhcoreClassModelChat::getList($filter);
        
        // Get chats count
        $chatsCount = erLhcoreClassModelChat::getCount($filter);

        // Allow extensions append custom field
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.restapi_chats',array('list' => & $chats));

        if (isset($_GET['include_messages']) && $_GET['include_messages'] == 'true' && !empty($chats)) {
            $messages = erLhcoreClassModelmsg::getList(array('limit' => 100000,'sort' => 'id ASC','filterin' => array('chat_id' => array_keys($chats))));
            foreach ($messages as $message) {
                if (!is_array($chats[$message->chat_id]->messages)) {
                    $chats[$message->chat_id]->messages = array();
                }
                $chats[$message->chat_id]->messages[] = $message;
            }
        }

        $prefillFields = array();

        if (isset($_GET['prefill_fields'])){
            $prefillFields = explode(',',str_replace(' ','',$_GET['prefill_fields']));
        }

        $ignoreFields = array();
        if (isset($_GET['ignore_fields'])){
            $ignoreFields = explode(',',str_replace(' ','',$_GET['ignore_fields']));
        }

        // Option to have department_groups attribute listed in response
        if (isset($_GET['department_groups']) && $_GET['department_groups'] == 'true') {
            $departments = array();
            foreach ($chats as $chat) {
                $departments[] = $chat->dep_id;
            }

            $departments = array_unique($departments);

            $depMembersItems = array();

            if (!empty($departments)) {
                $depMembers = erLhcoreClassModelDepartamentGroupMember::getList(array('filterin' => array('dep_id' => $departments)));
                foreach ($depMembers as $depMember) {
                    $depMembersItems[$depMember->dep_id][] = $depMember->dep_group_id;
                }
            }

            foreach ($chats as $index => $chat) {
                $chats[$index]->department_groups = isset($depMembersItems[$chat->dep_id]) ? $depMembersItems[$chat->dep_id] : array();
            }
        }

        if (!empty($prefillFields) || !empty($ignoreFields)) {
            erLhcoreClassChat::prefillGetAttributes($chats, $prefillFields, $ignoreFields, array('clean_ignore' => true, 'do_not_clean' => true));
        }

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

    public static function hasAccessToWrite($chat)
    {
        $dep = erLhcoreClassUserDep::getUserReadDepartments(self::$apiKey->user->id);
        return !in_array($chat->dep_id, $dep);
    }

    public static function hasAccessTo($module, $functions, $returnLimitation = false)
    {
        $AccessArray = erLhcoreClassRole::accessArrayByUserID( self::$apiKey->user->id );

        // Global rights
        if (isset($AccessArray['*']['*']) || isset($AccessArray[$module]['*']))
        {
            if ($returnLimitation === false) {
                return true;
            } elseif (isset($AccessArray[$module]['*']) && !is_bool($AccessArray[$module]['*'])) {
                return $AccessArray[$module]['*'];
            } elseif ($AccessArray['*']['*'] && !is_bool($AccessArray['*']['*'])) {
                return $AccessArray['*']['*'];
            } else {
                return true;
            }
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
            if (!isset($AccessArray[$module][$functions])) {
                return false;
            } elseif (isset($AccessArray[$module][$functions]) && $returnLimitation === true && !is_bool($AccessArray[$module][$functions])) {
                return $AccessArray[$module][$functions];
            }
        }

        return true;
    }

    public static function getUserId()
    {
        return self::$apiKey->user->id;
    }

    public static function getUser()
    {
        return self::$apiKey->user;
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
                $isIndexed = $isArray && count($value) > 1 && array_keys($value) === range(0, count($value) - 1);
                $isKeyed   = $isArray && count($value) && !$isIndexed;
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
    public static function outputResponse($data, $format = null)
    {
        if ((isset($_GET['format']) && $_GET['format'] == 'xml') || $format === 'xml') {
           echo self::formatXML(json_decode(json_encode($data),true));
        } else {
        
            $json = json_encode($data);
            
            if (isset($_GET['callback'])) {
                echo $_GET['callback'] . '(' . $json . ')';
            } else {
                echo $json;
            }
        }
    }

    public static function importMessages($chat, $messages) {
        foreach ($messages as $message) {
            $msg = new erLhcoreClassModelmsg();
            $msg->msg = isset($message['msg']) ? $message['msg'] : '';
            $msg->meta_msg = isset($message['meta_msg']) ? $message['meta_msg'] : '';
            $msg->time = isset($message['time']) ? $message['time'] : time();
            $msg->chat_id = $chat->id;
            $msg->user_id = isset($message['user_id']) ? $message['user_id'] : 0;
            $msg->name_support = isset($message['name_support']) ? $message['name_support'] : '';
            $msg->saveThis();

            $chat->last_msg_id = $msg->id;
            if ($msg->user_id == 0) {
                $chat->last_user_msg_time = $msg->time;
            } elseif ($msg->user_id == -2) {
                $chat->last_op_msg_time = $msg->time;
            }
        }
    }

    private static $apiKey = null;
}

?>
