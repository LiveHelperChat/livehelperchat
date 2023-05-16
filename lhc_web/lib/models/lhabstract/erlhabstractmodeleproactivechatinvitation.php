<?php

class erLhAbstractModelProactiveChatInvitation {

    use erLhcoreClassDBTrait;
    
    public static $dbTable = 'lh_abstract_proactive_chat_invitation';
    
    public static $dbTableId = 'id';
    
    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

	public function getState()
	{
		$stateArray = array (
			'id'         	=> $this->id,
			'name'  		=> $this->name,
			'siteaccess'  	=> $this->siteaccess,
			'time_on_site'  => $this->time_on_site,
			'referrer' 		=> $this->referrer,
			'pageviews' 	=> $this->pageviews,
			'message' 			=> $this->message,
			'autoresponder_id' 	=> $this->autoresponder_id,
			'message_returning' => $this->message_returning,
			'message_returning_nick' => $this->message_returning_nick,
			'identifier' 	=> $this->identifier,
			'dep_id' 		=> $this->dep_id,
			'executed_times'=> $this->executed_times,
			'position'		=> $this->position,
			'operator_name'	=> $this->operator_name,
			'requires_email'		=> $this->requires_email,
			'requires_username'		=> $this->requires_username,
			'show_random_operator'	=> $this->show_random_operator,
			'hide_after_ntimes'	    => $this->hide_after_ntimes,
			'operator_ids'	    => $this->operator_ids,
			'requires_phone'	=> $this->requires_phone,
			'tag' => $this->tag,
			'dynamic_invitation' => $this->dynamic_invitation,
			'event_invitation' => $this->event_invitation,
			'iddle_for' => $this->iddle_for,
			'event_type' => $this->event_type,
			'show_on_mobile' => $this->show_on_mobile,
			'delay' => $this->delay,
			'delay_init' => $this->delay_init,
			'show_instant' => $this->show_instant,
			'bot_id' => $this->bot_id,
			'trigger_id' => $this->trigger_id,
			'bot_offline' => $this->bot_offline,
			'disabled' => $this->disabled,
			'campaign_id' => $this->campaign_id,
			'design_data' => $this->design_data,
			'inject_only_html' => $this->inject_only_html,
			'parent_id' => $this->parent_id
		);
			
		return $stateArray;
	}

	public function __toString()
	{
		return $this->name;
	}
	
	public function checkPermission(){

		$currentUser = erLhcoreClassUser::instance();
		
		/**
		 * Append user departments filter
		 * */
		$departmentParams = array();
		$userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID(), $currentUser->cache_version);
		if ($userDepartments !== true) {
            $depIDS = $this->dep_ids_front;
			if (!empty($depIDS) && count(array_diff($depIDS, $userDepartments)) > 0) {
				return false;
			}
		}
	}
	
	public static function getFilter(){
        // Global filters
        return erLhcoreClassUserDep::conditionalDepartmentFilter(false,'dep_id');
	}

	public function getFields()
   	{
   	    $currentUser = erLhcoreClassUser::instance();
   		$userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID(), $currentUser->cache_version);
   		
   		return include('lib/core/lhabstract/fields/erlhabstractmodeleproactivechatinvitation.php');
	}

	public static function getEventTypes()
	{
	    $items = array();
	    
	    $item = new stdClass();
	    $item->id = 1;
	    $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Mouse leaves a browser window');
	    
	    $items[] = $item;
	    
	    $item = new stdClass();
	    $item->id = 2;
	    $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Visitor idle N seconds on site');
	     
	    $items[] = $item;
	    
	    return $items;
	}


	public function getModuleTranslations()
	{
	    /**
	     * Get's executed before permissions check. It can redirect to frontpage throw permission exception etc
	     * */
	    $metaData = array('permission_delete' => array('module' => 'lhchat','function' => 'administrateinvitations'),'permission' => array('module' => 'lhchat','function' => 'administrateinvitations'),'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Pro active chat invitations'));
	    
	    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('feature.can_use_proactive', array('object_meta_data' => & $metaData));	
	    
		return $metaData;
	}

	public function __get($var)
	{
	   switch ($var) {
	   	case 'left_menu':
	   	       $this->left_menu = '';
	   		   return $this->left_menu;
	   		break;
	   		
	   	case 'events':
	   	       $this->events = erLhAbstractModelProactiveChatInvitationEvent::getList(array('filter' => array('invitation_id' => $this->id)));
	   	       return $this->events;
	   	    break;

	   	case 'autoresponder':
	   	       if ($this->autoresponder_id > 0) {
	   	            $this->autoresponder = erLhAbstractModelAutoResponder::fetch($this->autoresponder_id);
	   	       } else {
                   $this->autoresponder = false;
               }
	   	       return $this->autoresponder;
	   	    break;

       case 'design_data_array':
           $attr = str_replace('_array','',$var);
           if (!empty($this->{$attr})) {
               $jsonData = json_decode($this->{$attr},true);
               if ($jsonData !== null) {
                   $this->{$var} = $jsonData;
               } else {
                   $this->{$var} = array();
               }
           } else {
               $this->{$var} = array();
           }
           return $this->{$var};
           break;

       case 'design_data_img_1_url':
       case 'design_data_img_2_url':
       case 'design_data_img_3_url':
       case 'design_data_img_4_url':
       case 'design_data_img_5_url':
           $attr = str_replace('_url', '', $var);
           $this->$var = '';
           if ($this->$attr != ''){
               $this->$var =  ($this->{$attr.'_path'} != '' ? erLhcoreClassSystem::instance()->wwwDir() : erLhcoreClassSystem::instance()->wwwImagesDir() ) . '/' . $this->{$attr.'_path'} . $this->$attr;
           }
           return $this->$var;
           break;

       case 'design_data_img_1':
       case 'design_data_img_2':
       case 'design_data_img_3':
       case 'design_data_img_4':
       case 'design_data_img_5':
       case 'design_data_img_1_path':
       case 'design_data_img_2_path':
       case 'design_data_img_3_path':
       case 'design_data_img_4_path':
       case 'design_data_img_5_path':
           $configurationArray = $this->design_data_array;
           if (isset($configurationArray[$var]) && $configurationArray[$var] != '') {
               $this->$var = $configurationArray[$var];
           } else {
               $this->$var = '';
           }
           return $this->$var;
           break;

       case 'design_data_img_1_url_img':
       case 'design_data_img_2_url_img':
       case 'design_data_img_3_url_img':
       case 'design_data_img_4_url_img':
       case 'design_data_img_5_url_img':
           $attr = str_replace('_url_img', '', $var);
           $configurationArray = $this->design_data_array;
           if (isset($configurationArray[$attr]) && $configurationArray[$attr] != '') {
               $this->$var = '<img src="'.($this->{$attr.'_path'} != '' ? erLhcoreClassSystem::instance()->wwwDir() : erLhcoreClassSystem::instance()->wwwImagesDir() ) .'/'.$this->{$attr.'_path'} . $configurationArray[$attr].'"/>';
           } else {
               $this->$var = false;
           }
           return $this->$var;

       case 'dep_ids_front':
           $this->dep_ids_front = [];
           if ($this->id > 0) {
               $db = ezcDbInstance::get();
               $stmt = $db->prepare("SELECT `dep_id` FROM `lh_abstract_proactive_chat_invitation_dep` WHERE `invitation_id` = " . $this->id);
               $stmt->execute();
               $this->dep_ids_front = $stmt->fetchAll(PDO::FETCH_COLUMN);
           }
           return $this->dep_ids_front;

	   	default:
	   		break;
	   }
	}

	public static function getHost($url) {
		$url = parse_url($url);
		if (isset($url['host'])) {
			return str_replace('www.','',$url['host']);
		}
		
		return '';
	}

	public static function getDeviceOptions() {

	    $items = [];

        foreach ([
            1 => 'All devices',
            0 => 'Desktop only',
            2 => 'Mobile only',
            3 => 'Tablet only',
            4 => 'Mobile & Desktop',
            5 => 'Tablet & Desktop',
            6 => 'Mobile & Tablet',
        ] as $id => $item) {
            $itemStd = new stdClass();
            $itemStd->id = $id;
            $itemStd->name = $item;
            $items[] = $itemStd;
        }

        return $items;
    }

	public static function processInjectHTMLInvitation(erLhcoreClassModelChatOnlineUser & $item, $params = array())
    {
        $referrer = self::getHost($item->referrer);

        $session = erLhcoreClassAbstract::getSession();

        $q = $session->createFindQuery( 'erLhAbstractModelProactiveChatInvitation' );

        if (isset($params['tag']) && $params['tag'] != '') {
            $appendTag = 'AND ('.$q->expr->eq( 'tag', $q->bindValue( $params['tag'] ) ).' OR tag = \'\')';
        } else {
            $appendTag = 'AND (tag = \'\')';
        }

        $q->where( $q->expr->lte( 'time_on_site', $q->bindValue( $item->time_on_site ) ).' AND '.$q->expr->lte( 'pageviews', $q->bindValue( $item->pages_count ) ).'
				AND ('.$q->expr->eq( 'siteaccess', $q->bindValue( erLhcoreClassSystem::instance()->SiteAccess ) ).' OR siteaccess = \'\')
				AND ('.$q->expr->eq( 'identifier', $q->bindValue( $item->identifier ) ).' OR identifier = \'\')
				' . $appendTag . '
				AND (`lh_abstract_proactive_chat_invitation`.`id` IN (SELECT `invitation_id` FROM `lh_abstract_proactive_chat_invitation_dep` WHERE `dep_id` = ' . (int)$item->dep_id . ') OR `dep_id` = ' . (int)$item->dep_id . ' OR `dep_id` = 0)
	            AND `inject_only_html` = 1
	            AND `disabled` = 0
	            AND `parent_id` = 0
				AND ('.$q->expr->like( $session->database->quote(trim($referrer)), 'concat(referrer,\'%\')' ).' OR referrer = \'\')'
        )
        ->orderBy('position ASC')
        ->limit( 10 );

        $messagesToUser = $session->find( $q );

        return $messagesToUser;
    }

	public static function processProActiveInvitationDynamic(erLhcoreClassModelChatOnlineUser & $item, $params = array())
	{
	    $referrer = self::getHost($item->referrer);
	    
	    $session = erLhcoreClassAbstract::getSession();

	    $q = $session->createFindQuery( 'erLhAbstractModelProactiveChatInvitation' );
	    
	    if (isset($params['tag']) && $params['tag'] != '') {
	        $appendTag = 'AND ('.$q->expr->eq( 'tag', $q->bindValue( $params['tag'] ) ).' OR tag = \'\')';
	    } else {
	        $appendTag = 'AND (tag = \'\')';
	    }

        // Device was not detected yet
        if ($item->device_type == 0) {
            $detect = new Mobile_Detect;
            $detect->setUserAgent($item->user_agent);
            $item->device_type = ($detect->isMobile() ? ($detect->isTablet() ? 3 : 2) : 1);
            $item->updateThis(['update' => ['device_type']]);
        }

        $devicesFilter = [
            1 => '(1,0,4,5)',
            2 => '(1,2,4,6)',
            3 => '(1,3,5,6)',
        ];

        $appendDevice = '';
        if (isset($devicesFilter[$item->device_type])) {
            $appendDevice = 'AND show_on_mobile IN ' . $devicesFilter[$item->device_type];
        }

	    $q->where( $q->expr->lte( 'time_on_site', $q->bindValue( $item->time_on_site ) ).' AND '.$q->expr->lte( 'pageviews', $q->bindValue( $item->pages_count ) ).'
				AND ('.$q->expr->eq( 'siteaccess', $q->bindValue( erLhcoreClassSystem::instance()->SiteAccess ) ).' OR siteaccess = \'\')
				AND ('.$q->expr->eq( 'identifier', $q->bindValue( $item->identifier ) ).' OR identifier = \'\')
				' . $appendTag . '
				' . $appendDevice . '
				AND (`lh_abstract_proactive_chat_invitation`.`id` IN (SELECT `invitation_id` FROM `lh_abstract_proactive_chat_invitation_dep` WHERE `dep_id` = ' . (int)$item->dep_id . ') OR `dep_id` = ' . (int)$item->dep_id . ' OR `dep_id` = 0)
	            AND `inject_only_html` = 0
	            AND `dynamic_invitation` = 1
	            AND `disabled` = 0
	            AND `parent_id` = 0
				AND ('.$q->expr->like( $session->database->quote(trim($referrer)), 'concat(referrer,\'%\')' ).' OR referrer = \'\')'
	    )
	    ->orderBy('position ASC, RAND()')
	    ->limit( 10 );

	    $messagesToUser = $session->find( $q );
	    
	    return $messagesToUser;
	}
	
	public static function setInvitation(erLhcoreClassModelChatOnlineUser & $item, $invitationId) {

        $messageContent = $message = self::fetch($invitationId);

        // This is a variation message
        if (isset($item->online_attr_system_array['lhc_inv_var_id'])) {
            $messageContent = self::fetch($item->online_attr_system_array['lhc_inv_var_id']);
            if (!($messageContent instanceof erLhAbstractModelProactiveChatInvitation)){
                $messageContent = $message;
            }
        }

        $messageContent->translateByLocale();

	    if ($item->total_visits == 1 || $messageContent->message_returning == '') {
	        $item->operator_message = $messageContent->message;
	    } else {
	        if ($item->chat !== false && $item->chat->nick != '') {
	            $nick = $item->chat->nick;
	        } elseif ($messageContent->message_returning_nick != '') {
	            $nick = $messageContent->message_returning_nick;
	        } else {
	            $nick = '';
	        }
	    
	        $item->operator_message = str_replace('{nick}', $nick, $messageContent->message_returning);
	    }

	    $item->operator_user_proactive = $messageContent->operator_name;

        if ($item->invitation_id != $message->id) {
            $item->invitation_seen_count = 1;
        }

        $item->invitation_id = $message->id;
	    $item->requires_email = $message->requires_email;
	    $item->requires_username = $message->requires_username;
	    $item->requires_phone = $message->requires_phone;
	    $item->invitation_count++;
	    $item->store_chat = true;
	    $item->invitation_assigned = true;
	    $item->last_visit = time();
	    
	    if ($messageContent->show_random_operator == 1) {
	        $item->operator_user_id = erLhcoreClassChat::getRandomOnlineUserID(array('operators' => explode(',',trim($messageContent->operator_ids))));

	        // Assign same operator as invitation was shown from
	        $onlineAttrSystem = $item->online_attr_system_array;
            $attributesDesignData = $messageContent->design_data_array;

            if ($item->operator_user_id > 0 && !isset($onlineAttrSystem['lhc_assign_to_me']) && isset($attributesDesignData['assign_to_randomop']) && $attributesDesignData['assign_to_randomop'] == 1) {
                $onlineAttrSystem['lhc_assign_to_me'] = 1;
                $item->online_attr_system_array = $onlineAttrSystem;
                $item->online_attr_system = json_encode($onlineAttrSystem);
            }
	    } else {
            $item->operator_user_id = 0;
        }

        $message->executed_times += 1;
        $message->updateThis(array('update' => array(
            'executed_times'
        )));

        if ($message->id != $messageContent->id) {
            $messageContent->executed_times += 1;
            $messageContent->updateThis(array('update' => array(
                'executed_times'
            )));
        }

	    $item->saveThis();
	    
	    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('onlineuser.proactive_triggered', array('message' => & $message, 'variation' => & $messageContent, 'ou' => & $item));

        return ['invitation' => $message, 'variation' => $messageContent];
	}

	public function translateByLocale()
    {
        $chatLocale = erLhcoreClassChatValidator::getVisitorLocale();

        // We set custom chat locale only if visitor is not using default siteaccss and default langauge is not english.
        if (erConfigClassLhConfig::getInstance()->getSetting('site','default_site_access') != erLhcoreClassSystem::instance()->SiteAccess) {
            $siteAccessOptions = erConfigClassLhConfig::getInstance()->getSetting('site_access_options', erLhcoreClassSystem::instance()->SiteAccess);
            // Never override to en
            if (isset($siteAccessOptions['content_language'])) {
                $chatLocale = $siteAccessOptions['content_language'];
            }
        }

        if ($chatLocale !== null) {

            $attributes =  $this->design_data_array;

            $translatableAttributes = array(
                'message',
                'message_returning',
                'message_returning',
                'operator_name'
            );

            foreach ($translatableAttributes as $attr) {
                if (isset($attributes[$attr . '_lang'])) {

                    $translated = false;

                    if ($chatLocale !== null) {
                        foreach ($attributes[$attr . '_lang'] as $attrTrans) {
                            if (in_array($chatLocale, $attrTrans['languages']) && $attrTrans['content'] != '') {
                                $attributes[$attr] = $attrTrans['content'];
                                $translated = true;
                                break;
                            }
                        }
                    }

                    if ($translated === true) {
                        $this->$attr = $attributes[$attr];
                    }
                }
            }
        }
    }

	public static function processProActiveInvitation(erLhcoreClassModelChatOnlineUser & $item, $params = array()) {

		$referrer = self::getHost($item->referrer);

		$session = erLhcoreClassAbstract::getSession();			

		$q = $session->createFindQuery( 'erLhAbstractModelProactiveChatInvitation' );
		
		if (isset($params['tag']) && $params['tag'] != '') {
		    $appendTag = 'AND ('.$q->expr->eq( 'tag', $q->bindValue( $params['tag'] ) ).' OR tag = \'\')';
		} else {
		    $appendTag = 'AND (tag = \'\')';
		}
		
		$appendInvitationsId = '';
		if ( isset($params['invitation_id']) && !empty($params['invitation_id']) ) {
		    $appendInvitationsId = 'AND id IN ('.implode(',', $params['invitation_id']).')';
		}

		// Device was not detected yet
		if ($item->device_type == 0) {
            $detect = new Mobile_Detect;
            $detect->setUserAgent($item->user_agent);
            $item->device_type = ($detect->isMobile() ? ($detect->isTablet() ? 3 : 2) : 1);
            $item->updateThis(['update' => ['device_type']]);
        }

		$devicesFilter = [
		    1 => '(1,0,4,5)',
		    2 => '(1,2,4,6)',
		    3 => '(1,3,5,6)',
        ];

		$appendDevice = '';
        if (isset($devicesFilter[$item->device_type])) {
            $appendDevice = 'AND show_on_mobile IN ' . $devicesFilter[$item->device_type];
        }

		$q->where( $q->expr->lte( 'pageviews', $q->bindValue( $item->pages_count ) ).'
				AND ('.$q->expr->eq( 'siteaccess', $q->bindValue( erLhcoreClassSystem::instance()->SiteAccess ) ).' OR siteaccess = \'\')
				AND ('.$q->expr->eq( 'identifier', $q->bindValue( $item->identifier ) ).' OR identifier = \'\')
				' . $appendTag . '
				' . $appendDevice . '
		        AND `dynamic_invitation` = 0
		        AND `disabled` = 0
		        AND `parent_id` = 0
		        AND `inject_only_html` = 0
		        ' . $appendInvitationsId . '
				AND (`lh_abstract_proactive_chat_invitation`.`id` IN (SELECT `invitation_id` FROM `lh_abstract_proactive_chat_invitation_dep` WHERE `dep_id` = ' . (int)$item->dep_id . ') OR `dep_id` = ' . (int)$item->dep_id . ' OR `dep_id` = 0)
				AND ('.$q->expr->like( $session->database->quote(trim($referrer)), 'concat(referrer,\'%\')' ).' OR referrer = \'\')'
		)
		->orderBy('position ASC, time_on_site ASC, RAND()')
		->limit( 50 );

		$messagesToUserRaw = $session->find( $q );

        $messagesToUser = [];

        $onlineAttrSystem = $item->online_attr_system_array;

        // Verify dynamic conditions
        foreach ($messagesToUserRaw as $messageToUser) {
            $optionsInvitation = $messageToUser->design_data_array;
            $operatorsOnlineId = [];

            if (isset($optionsInvitation['on_op_id']) && !empty($optionsInvitation['on_op_id'])) {
                $operators = explode(',',$optionsInvitation['on_op_id']);
                $filter = [];
                $filter['filterin']['user_id'] = $operators;
                $filter['filter']['hide_online'] = 0;
                $filter['customfilter'][] = '(last_activity > ' . (int)(time() - 120) . ' OR always_on = 1)';
                $filter['filterin']['dep_id'] = [$item->dep_id,0];
                $filter['group'] = 'user_id';
                $filter['ignore_fields'] = array('exclude_autoasign','exc_indv_autoasign','max_chats','dep_group_id','type','ro','id','dep_id','hide_online_ts','hide_online','last_activity','lastd_activity','always_on','last_accepted','active_chats','pending_chats','inactive_chats');
                $filter['select_columns'] = 'max(`id`) as `id`,user_id';

                if (isset($optionsInvitation['op_max_chats']) && !empty($optionsInvitation['op_max_chats'])) {
                    $filter['customfilter'][] = '(max_chats = 0 || ((pending_chats + active_chats) - inactive_chats) < (max_chats + '.(int)$optionsInvitation['op_max_chats'] . '))';
                }

                $onlineOperators = erLhcoreClassModelUserDep::getList($filter);

                if (empty($onlineOperators)) {
                    continue;
                } else {
                    foreach ($onlineOperators as $onlineOperator) {
                        $operatorsOnlineId[] = $onlineOperator->user_id;
                    }
                }
            }

            if (
                $item->last_visit_prev > 0 &&
                isset($optionsInvitation['last_visit_prev']) &&
                    is_numeric($optionsInvitation['last_visit_prev']) &&
                    $optionsInvitation['last_visit_prev'] > 0 &&
                    $item->last_visit_prev > time() - $optionsInvitation['last_visit_prev']
                ) {
                continue;
            }

            if (
                $item->chat_time > 0 &&
                isset($optionsInvitation['last_chat']) &&
                    is_numeric($optionsInvitation['last_chat']) &&
                    $optionsInvitation['last_chat'] > 0 &&
                    $item->chat_time > time() - $optionsInvitation['last_chat']
                ) {
                continue;
            }

            $design_data_array = $messageToUser->design_data_array;
            $conditionsValid = true;

            for ($i = 1; $i <= 10; $i++) {
                 if ( isset($design_data_array['attrf_key_' . $i]) &&  $design_data_array['attrf_key_' . $i] != '' ) {

                     if (!isset($onlineAttrSystem[$design_data_array['attrf_key_' . $i]])) {
                         $conditionsValid = false;
                         break;
                     }

                     if (!isset($design_data_array['attrf_val_' . $i])) {
                         $design_data_array['attrf_val_' . $i] = '';
                     }

                     $valuesExpected = explode('||',strtolower($design_data_array['attrf_val_' . $i]));
                     $conditionAttr = strtolower($design_data_array['attrf_val_' . $i]);
                     $valueAttr = strtolower($onlineAttrSystem[$design_data_array['attrf_key_' . $i]]);

                     $replaceArray = array(
                         '{time}' => time()
                     );

                     // Remove internal variables
                     $conditionAttr = str_replace(array_keys($replaceArray), array_values($replaceArray),$conditionAttr);
                     $valueAttr = str_replace(array_keys($replaceArray), array_values($replaceArray),$valueAttr);

                     if (isset($design_data_array['attrf_cond_' . $i]) && !in_array($design_data_array['attrf_cond_' . $i],['like','notlike','contains'])) {
                         // Remove spaces
                         $conditionAttr = preg_replace('/\s+/', '', $conditionAttr);
                         $valueAttr = preg_replace('/\s+/', '', $valueAttr);

                         // Allow only mathematical operators
                         $conditionAttrMath = preg_replace("/[^\(\)\.\*\-\/\+0-9]+/", "", $conditionAttr);
                         $valueAttrMath = preg_replace("/[^\(\)\.\*\-\/\+0-9]+/", "", $valueAttr);

                         if ($conditionAttrMath != '' && $conditionAttrMath == $conditionAttr) {
                             // Evaluate if there is mathematical rules
                             try {
                                 eval('$conditionAttr = ' . $conditionAttrMath . ";");
                             } catch (ParseError $e) {
                                 // Do nothing
                             }
                         }

                         if ($valueAttrMath != '' && $valueAttrMath == $valueAttr) {
                             // Evaluate if there is mathematical rules
                             try {
                                 eval('$valueAttr = ' . $valueAttrMath . ";");
                             } catch (ParseError $e) {
                                 // Do nothing
                             }
                         }
                     }

                     if (isset($design_data_array['attrf_cond_' . $i]) && in_array($design_data_array['attrf_cond_' . $i],['lt','lte','gt','gte'])) {
                         $conditionAttr = round((float)$conditionAttr,3);
                         $valueAttr = round((float)$valueAttr,3);
                     } elseif ((is_string($conditionAttr) || is_numeric($conditionAttr)) && (is_string($valueAttr) || is_numeric($valueAttr))) {
                         $conditionAttr = (string)$conditionAttr;
                         $valueAttr = (string)$valueAttr;
                     }

                     if (!isset($design_data_array['attrf_cond_' . $i]) || ($design_data_array['attrf_cond_' . $i] == 'eq' && !in_array(
                                 $valueAttr,
                             $valuesExpected
                         ))) {
                         $conditionsValid = false;
                         break;
                     } elseif ($design_data_array['attrf_cond_' . $i] == 'neq' && in_array(
                             $valueAttr,
                             $valuesExpected
                         )) {
                         $conditionsValid = false;
                         break;
                     } elseif ($design_data_array['attrf_cond_' . $i] == 'gt' && !($valueAttr > $conditionAttr)) {
                         $conditionsValid = false;
                         break;
                     } elseif ($design_data_array['attrf_cond_' . $i] == 'gte' && !($valueAttr >= $conditionAttr)) {
                         $conditionsValid = false;
                         break;
                     } elseif ($design_data_array['attrf_cond_' . $i] == 'lt' && !($valueAttr < $conditionAttr)) {
                         $conditionsValid = false;
                         break;
                     } elseif ($design_data_array['attrf_cond_' . $i] == 'lte' && !($valueAttr <= $conditionAttr)) {
                         $conditionsValid = false;
                         break;
                     } elseif ($design_data_array['attrf_cond_' . $i] == 'like' && erLhcoreClassGenericBotWorkflow::checkPresenceMessage(array(
                             'pattern' => $conditionAttr,
                             'msg' => $valueAttr,
                             'words_typo' => 0,
                         ))['found'] !== true) {
                         $conditionsValid = false;
                         break;
                     } elseif ($design_data_array['attrf_cond_' . $i] == 'notlike' && erLhcoreClassGenericBotWorkflow::checkPresenceMessage(array(
                             'pattern' => $conditionAttr,
                             'msg' => $valueAttr,
                             'words_typo' => 0,
                         ))['found'] === true) {
                         $conditionsValid = false;
                         break;
                     } elseif ($design_data_array['attrf_cond_' . $i] == 'contains' && strrpos($valueAttr, $conditionAttr) === false) {
                         $conditionsValid = false;
                         break;
                     }
                }
            }

            if ($conditionsValid === true) {
                $messagesToUser[] = $messageToUser;
            }
        }

		if ( !empty($messagesToUser) ) {

			$message = array_shift($messagesToUser);

			if ($message->time_on_site <= $item->time_on_site)
            {
                if ($message->event_invitation == 1 && (!isset($params['ignore_event']) || $params['ignore_event'] == 0)) {

                    // Event conditions does not satisfied
                    if (erLhcoreClassChatEvent::isConditionsSatisfied($item, $message) === false) {
                        return;
                    }
                }

                // Variation message either original either some child
                // We should always find one even yourself
                $messageContent = erLhAbstractModelProactiveChatInvitation::findOne(['sort' => 'rand()', 'filter' => ['disabled' => 0], 'filterlor' => ['parent_id' => [$message->id], 'id' => [$message->id]]]);
                $messageContent->translateByLocale();

                // Use default message if first time visit or returning message is empty
                if ($item->total_visits == 1 || $messageContent->message_returning == '') {
                    $item->operator_message = $messageContent->message;
                } else {
                    if ($item->chat !== false && $item->chat->nick != '') {
                        $nick = $item->chat->nick;
                    } elseif ($messageContent->message_returning_nick != '') {
                        $nick = $messageContent->message_returning_nick;
                    } else {
                        $nick = '';
                    }

                    $item->operator_message = str_replace('{nick}', $nick, $messageContent->message_returning);
                }

                $item->operator_user_proactive = $messageContent->operator_name;
                $item->invitation_id = $message->id;
                $item->invitation_seen_count = 1;
                $item->requires_email = $message->requires_email;
                $item->requires_username = $message->requires_username;
                $item->requires_phone = $message->requires_phone;
                $item->invitation_count++;
                $item->store_chat = true;
                $item->invitation_assigned = true;
                $item->last_visit = time();

                if ($messageContent->show_random_operator == 1) {
                    $item->operator_user_id = erLhcoreClassChat::getRandomOnlineUserID(array('operators' => (isset($operatorsOnlineId) && !empty($operatorsOnlineId)) ? $operatorsOnlineId : explode(',',trim($messageContent->operator_ids))));
                } else {
                    $item->operator_user_id = 0;
                }

                $onlineAttrSystem = $item->online_attr_system_array;

                if (isset($messageContent->design_data_array['next_inv_time']) && (int)$messageContent->design_data_array['next_inv_time'] > 0) {
                    $onlineAttrSystem['lhcnxt_ivt'] = (int)$messageContent->design_data_array['next_inv_time'];
                    $item->online_attr_system = json_encode($onlineAttrSystem);
                    $item->online_attr_system_array = $onlineAttrSystem;
                } elseif (isset($onlineAttrSystem['lhcnxt_ivt'])) {
                    unset($onlineAttrSystem['lhcnxt_ivt']);
                    $item->online_attr_system = json_encode($onlineAttrSystem);
                    $item->online_attr_system_array = $onlineAttrSystem;
                }

                if (isset($messageContent->design_data_array['expires_after']) && (int)$messageContent->design_data_array['expires_after'] > 0) {
                    $onlineAttrSystem['lhcinv_exp'] = (int)$messageContent->design_data_array['expires_after'] + time();
                    $item->online_attr_system = json_encode($onlineAttrSystem);
                    $item->online_attr_system_array = $onlineAttrSystem;
                } elseif (isset($onlineAttrSystem['lhcinv_exp'])) {
                    unset($onlineAttrSystem['lhcinv_exp']);
                    $item->online_attr_system = json_encode($onlineAttrSystem);
                    $item->online_attr_system_array = $onlineAttrSystem;
                }

                if (isset($messageContent->design_data_array['ignore_bot']) && $messageContent->design_data_array['ignore_bot'] == true) {
                    $onlineAttrSystem['lhc_ignore_bot'] = 1;
                    $item->online_attr_system = json_encode($onlineAttrSystem);
                    $item->online_attr_system_array = $onlineAttrSystem;
                } elseif (isset($onlineAttrSystem['lhc_ignore_bot'])) {
                    unset($onlineAttrSystem['lhc_ignore_bot']);
                    $item->online_attr_system = json_encode($onlineAttrSystem);
                    $item->online_attr_system_array = $onlineAttrSystem;
                }

                if ($item->dep_id > 0 && isset($messageContent->design_data_array['lock_department']) && $messageContent->design_data_array['lock_department'] == true) {
                    $onlineAttrSystem['inv_ldp'] = $item->dep_id; // Remember department to set it later
                    $item->online_attr_system = json_encode($onlineAttrSystem);
                    $item->online_attr_system_array = $onlineAttrSystem;
                } elseif (isset($onlineAttrSystem['inv_ldp'])) {
                    unset($onlineAttrSystem['inv_ldp']);
                    $item->online_attr_system = json_encode($onlineAttrSystem);
                    $item->online_attr_system_array = $onlineAttrSystem;
                }

                if ($message->id != $messageContent->id) {
                    $onlineAttrSystem['lhc_inv_var_id'] = $messageContent->id;
                    $item->online_attr_system = json_encode($onlineAttrSystem);
                    $item->online_attr_system_array = $onlineAttrSystem;
                } elseif (isset($onlineAttrSystem['lhc_inv_var_id'])) {
                    unset($onlineAttrSystem['lhc_inv_var_id']);
                    $item->online_attr_system = json_encode($onlineAttrSystem);
                    $item->online_attr_system_array = $onlineAttrSystem;
                }

                $campaign = erLhAbstractModelProactiveChatCampaignConversion::findOne(array('filterin' => array('invitation_status' => array(
                    erLhAbstractModelProactiveChatCampaignConversion::INV_SEND,
                    erLhAbstractModelProactiveChatCampaignConversion::INV_SHOWN
                )),'filter' => array('vid_id' => $item->id, 'invitation_id' => $message->id)));

                $message->executed_times += 1;
                $message->updateThis(array('update' => array(
                    'executed_times'
                )));

                if ($message->id != $messageContent->id) {
                    $messageContent->executed_times += 1;
                    $messageContent->updateThis(array('update' => array(
                        'executed_times'
                    )));
                }

                // Campaign tracking
                if (!($campaign instanceof erLhAbstractModelProactiveChatCampaignConversion)) {
                    $campaign = new erLhAbstractModelProactiveChatCampaignConversion();
                }

                $campaign->vid_id = $item->id;
                $campaign->invitation_status = erLhAbstractModelProactiveChatCampaignConversion::INV_SEND;
                $campaign->ctime = time();
                $campaign->con_time = time();
                $campaign->department_id = $item->dep_id;
                $campaign->invitation_id = $message->id;
                $campaign->invitation_type = 1;
                $campaign->campaign_id = $message->campaign_id;
                $campaign->variation_id = $messageContent->id;
                $campaign->conv_event = isset($message->design_data_array['event_id']) ? $message->design_data_array['event_id'] : '';
                $campaign->conv_int_expires = isset($message->design_data_array['conversion_expires_in']) && (int)$message->design_data_array['conversion_expires_in'] > 0 ? time() + (int)$message->design_data_array['conversion_expires_in'] : 0;

                $detect = new Mobile_Detect;
                $detect->setUserAgent($item->user_agent);
                $campaign->device_type = ($detect->isMobile() ? ($detect->isTablet() ? 2 : 1) : 0);
                $campaign->saveThis();

                // Set conversion for track back for online visitor record
                $item->conversion_id = $campaign->id;

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('onlineuser.proactive_triggered', array('campaign' => & $campaign, 'variation' => & $messageContent, 'message' => & $message, 'ou' => & $item));
            } else {
			    // We know there is invitation based on current criteria just time on site is still not matched.
                $item->next_reschedule = $message->time_on_site - $item->time_on_site;
            }
		}
	}

	public function customForm(){
	    return 'proactive_invitation.tpl.php';
	}

    public function dependJs()
    {
        return '<script type="text/javascript" src="'.erLhcoreClassDesign::designJS('js/ace/ace.js').'"></script>';
    }

	public function dependFooterJs(){
	    return '<script type="text/javascript" src="'.erLhcoreClassDesign::designJS('js/angular-sanitize.min.js;js/angular.lhc.events.js;js/angular.lhc.theme.js').'"></script>';
	}
	
	public function validateInput($params)
	{
	    $params['obj'] = & $this;
	    erLhcoreClassChatEvent::validateProactive($params);
	}
	
	public function afterUpdate($params)
	{
        // Only one field was updated
        // We can ignore these type of events
        if (isset($params['update'])) {
            return ;
        }

	    $ids = array();

	    // Save events and collect id's
	    foreach ($this->events as $event) {
	        $event->saveThis();
	        $ids[] = $event->id;
	    }

	    // Remove old, non-existing events
	    foreach (erLhAbstractModelProactiveChatInvitationEvent::getList(array('filter' => array('invitation_id' => $this->id))) as $oldEvent) {
	        if (!in_array($oldEvent->id, $ids)) {
	            $oldEvent->removeThis();
	        }
	    }

	    if (empty($ids) && $this->event_invitation == 1) {
	        $this->event_invitation = 0;
	        $this->saveThis();
	    } elseif (!empty($ids) && $this->event_invitation == 0) {
	        $this->event_invitation = 1;
	        $this->saveThis();
	    }

        $db = ezcDbInstance::get();
        $stmt = $db->prepare('DELETE FROM `lh_abstract_proactive_chat_invitation_dep` WHERE `invitation_id` = :invitation_id');
        $stmt->bindValue(':invitation_id', $this->id,PDO::PARAM_INT);
        $stmt->execute();

        if (isset($this->dep_ids) && !empty($this->dep_ids)) {
            $values = [];
            foreach ($this->dep_ids as $department_id) {
                $values[] = "(" . $this->id . "," . $department_id . ")";
            }
            if (!empty($values)) {
                $db->query('INSERT INTO `lh_abstract_proactive_chat_invitation_dep` (`invitation_id`,`dep_id`) VALUES ' . implode(',',$values));
            }
        }

	}

	public function afterRemove()
	{
	    foreach (erLhAbstractModelProactiveChatInvitationEvent::getList(array('filter' => array('invitation_id' => $this->id))) as $oldEvent) {
            $oldEvent->removeThis();
	    }

        $db = ezcDbInstance::get();
        $stmt = $db->prepare('DELETE FROM `lh_abstract_proactive_chat_invitation_dep` WHERE `invitation_id` = :invitation_id');
        $stmt->bindValue(':invitation_id', $this->id,PDO::PARAM_INT);
        $stmt->execute();
	}

    public function beforeUpdate()
    {
        $this->design_data = json_encode($this->design_data_array);
    }

    public function beforeSave()
    {
        $this->beforeUpdate();
    }

    public function getContentAttribute($attr)
    {
        $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('theme.download_image.'.$attr, array('theme' => $this, 'attr' => $attr));
        if ($response === false) {
            return file_get_contents($this->{$attr.'_path'}.'/'.$this->$attr);
        } else {
            return $response['filedata'];
        }
    }

    public function movePhoto($attr, $isLocal = false, $localFile = false)
    {
        $this->deletePhoto($attr);

        if ($this->id != null){
            $dir = 'var/storageinvitation/' . date('Y') . 'y/' . date('m') . '/' . date('d') .'/' . $this->id . '/';

            $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('theme.edit.'.$attr.'_path',array('dir' => & $dir, 'storage_id' => $this->id));

            erLhcoreClassFileUpload::mkdirRecursive( $dir );

            if ($isLocal == false) {
                $this->$attr = erLhcoreClassSearchHandler::moveUploadedFile('AbstractInput_'.$attr, $dir . '/','.' );
            } else {
                $this->$attr = erLhcoreClassSearchHandler::moveLocalFile($localFile, $dir . '/','.' );
            }

            $this->{$attr.'_path'} = $dir;

            $noteConfigurationArray = $this->design_data_array;
            $noteConfigurationArray[$attr.'_path'] = $this->{$attr.'_path'};
            $noteConfigurationArray[$attr] = $this->{$attr};

            $this->design_data_array = $noteConfigurationArray;

            $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('theme.edit.store_'.$attr,array(
                'theme' => & $this,
                'path_attr' => $attr.'_path',
                'name' => $this->$attr,
                'name_attr' => $attr,
                'file_path' => $this->{$attr.'_path'} . $this->$attr));

        } else {
            $this->{$attr.'_pending'} = true;
        }
    }

    public function deletePhoto($attr)
    {
        if ($this->$attr != '') {
            if ( file_exists($this->{$attr.'_path'} . $this->$attr) ) {
                unlink($this->{$attr.'_path'} . $this->$attr);
            }

            if ($this->{$attr.'_path'} != '') {
                erLhcoreClassFileUpload::removeRecursiveIfEmpty('var/storageinvitation/',str_replace('var/storageinvitation/','',$this->{$attr.'_path'}));
            }

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('theme.edit.remove_'.$attr,array(
                'theme' => & $this,
                'path_attr' => $attr.'_path',
                'name' => $this->$attr));

            $this->$attr = '';
            $this->{$attr.'_path'} = '';

            $noteConfigurationArray = $this->design_data_array;
            $noteConfigurationArray[$attr.'_path'] = '';
            $noteConfigurationArray[$attr] = '';
            $this->design_data_array = $noteConfigurationArray;
        }
    }

    public function afterSave($params)
    {
        $movePhotos = array(
            'design_data_img_1',
        );

        $pendingUpdate = false;
        foreach ($movePhotos as $photoAttr) {
            if ($this->{$photoAttr.'_pending'} == true) {
                $this->movePhoto($photoAttr);
                $pendingUpdate = true;
            }
        }

        if ($pendingUpdate == true) {
            $this->updateThis();
        } else {
            $this->afterUpdate($params);
        }
    }

   	public $id = null;
	public $siteaccess = '';
	public $time_on_site = 0;
	public $pageviews = 0;
	public $message = '';
	public $message_returning = '';
	public $message_returning_nick = '';
	public $position = 0;
	public $requires_email = 0;
	public $requires_username = 0;
	public $requires_phone = 0;
	public $name = '';
	public $identifier = '';
	public $executed_times = 0;
	public $operator_name = '';
	public $show_random_operator = 0;
	public $hide_after_ntimes = 0;
	public $dep_id = 0;
	public $dep_ids = [];
	public $referrer = '';
	public $operator_ids = '';
	public $tag = '';
	public $dynamic_invitation = 0;
	public $event_invitation = 0;
	public $iddle_for = 0;
	public $event_type = 0;
	public $autoresponder_id = 0;
	public $show_on_mobile = 1;
	public $delay = 0;
	public $delay_init = 0;
	public $show_instant = 0;
	public $bot_id = 0;
	public $trigger_id = 0;
	public $bot_offline = 0;
	public $disabled = 0;
	public $campaign_id = 0;
	public $design_data = '';
	public $inject_only_html = 0;
	public $parent_id = 0;

	public $next_reschedule = 0;
	public $hide_add = false;
	public $hide_delete = false;

}

?>
