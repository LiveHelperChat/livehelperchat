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
			'inject_only_html' => $this->inject_only_html
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
		$userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID());
		if ($userDepartments !== true) {
			if (!in_array($this->dep_id, $userDepartments) && $this->dep_id != 0) {
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
   		$userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID());
   		
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
           break;

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
				AND ('.$q->expr->eq( 'dep_id', $q->bindValue( $item->dep_id ) ).' OR dep_id = 0)
	            AND `inject_only_html` = 1
	            AND `disabled` = 0
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
	    
	    $q->where( $q->expr->lte( 'time_on_site', $q->bindValue( $item->time_on_site ) ).' AND '.$q->expr->lte( 'pageviews', $q->bindValue( $item->pages_count ) ).'
				AND ('.$q->expr->eq( 'siteaccess', $q->bindValue( erLhcoreClassSystem::instance()->SiteAccess ) ).' OR siteaccess = \'\')
				AND ('.$q->expr->eq( 'identifier', $q->bindValue( $item->identifier ) ).' OR identifier = \'\')
				' . $appendTag . '
				AND ('.$q->expr->eq( 'dep_id', $q->bindValue( $item->dep_id ) ).' OR dep_id = 0)
	            AND `inject_only_html` = 0
	            AND `dynamic_invitation` = 1
	            AND `disabled` = 0
				AND ('.$q->expr->like( $session->database->quote(trim($referrer)), 'concat(referrer,\'%\')' ).' OR referrer = \'\')'
	    )
	    ->orderBy('position ASC, RAND()')
	    ->limit( 10 );

	    $messagesToUser = $session->find( $q );
	    
	    return $messagesToUser;
	}
	
	public static function setInvitation(erLhcoreClassModelChatOnlineUser & $item, $invitationId) {
	    
	    $message = self::fetch($invitationId);

	    $message->translateByLocale();

	    if ($item->total_visits == 1 || $message->message_returning == '') {
	        $item->operator_message = $message->message;
	    } else {
	        if ($item->chat !== false && $item->chat->nick != '') {
	            $nick = $item->chat->nick;
	        } elseif ($message->message_returning_nick != '') {
	            $nick = $message->message_returning_nick;
	        } else {
	            $nick = '';
	        }
	    
	        $item->operator_message = str_replace('{nick}', $nick, $message->message_returning);
	    }

	    $item->operator_user_proactive = $message->operator_name;
	    $item->invitation_id = $message->id;
	    $item->invitation_seen_count = 1;
	    $item->requires_email = $message->requires_email;
	    $item->requires_username = $message->requires_username;
	    $item->requires_phone = $message->requires_phone;
	    $item->invitation_count++;
	    $item->store_chat = true;
	    $item->invitation_assigned = true;
	    $item->last_visit = time();
	    
	    if ($message->show_random_operator == 1) {
	        $item->operator_user_id = erLhcoreClassChat::getRandomOnlineUserID(array('operators' => explode(',',trim($message->operator_ids))));
	    }

        $message->executed_times += 1;
        $message->updateThis(array('update' => array(
            'executed_times'
        )));
	    	
	    $item->saveThis();
	    
	    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('onlineuser.proactive_triggered', array('message' => & $message, 'ou' => & $item));
	}

	public function translateByLocale()
    {
        $chatLocale = null;

        // Detect user locale
        if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $parts = explode(';',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $languages = explode(',',$parts[0]);
            if (isset($languages[0])) {
                $chatLocale = $languages[0];
            }
        }

        // We set custom chat locale only if visitor is not using default siteaccss and default langauge is not english.
        if (erConfigClassLhConfig::getInstance()->getSetting('site','default_site_access') != erLhcoreClassSystem::instance()->SiteAccess) {
            $siteAccessOptions = erConfigClassLhConfig::getInstance()->getSetting('site_access_options', erLhcoreClassSystem::instance()->SiteAccess);
            // Never override to en
            if (isset($siteAccessOptions['content_language']) && $siteAccessOptions['content_language'] != 'en') {
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

		$q->where( /*$q->expr->lte( 'time_on_site', $q->bindValue( $item->time_on_site ) ).' AND '.*/ $q->expr->lte( 'pageviews', $q->bindValue( $item->pages_count ) ).'
				AND ('.$q->expr->eq( 'siteaccess', $q->bindValue( erLhcoreClassSystem::instance()->SiteAccess ) ).' OR siteaccess = \'\')
				AND ('.$q->expr->eq( 'identifier', $q->bindValue( $item->identifier ) ).' OR identifier = \'\')
				' . $appendTag . '
		        AND `dynamic_invitation` = 0
		        AND `disabled` = 0
		        AND `inject_only_html` = 0
		        ' . $appendInvitationsId . '
				AND ('.$q->expr->eq( 'dep_id', $q->bindValue( $item->dep_id ) ).' OR dep_id = 0)
				AND ('.$q->expr->like( $session->database->quote(trim($referrer)), 'concat(referrer,\'%\')' ).' OR referrer = \'\')'
		)
		->orderBy('position ASC, time_on_site ASC, RAND()')
		->limit( 1 );
		
		$messagesToUser = $session->find( $q );
		
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

                $message->translateByLocale();

                // Use default message if first time visit or returning message is empty
                if ($item->total_visits == 1 || $message->message_returning == '') {
                    $item->operator_message = $message->message;
                } else {
                    if ($item->chat !== false && $item->chat->nick != '') {
                        $nick = $item->chat->nick;
                    } elseif ($message->message_returning_nick != '') {
                        $nick = $message->message_returning_nick;
                    } else {
                        $nick = '';
                    }

                    $item->operator_message = str_replace('{nick}', $nick, $message->message_returning);
                }

                $item->operator_user_proactive = $message->operator_name;
                $item->invitation_id = $message->id;
                $item->invitation_seen_count = 1;
                $item->requires_email = $message->requires_email;
                $item->requires_username = $message->requires_username;
                $item->requires_phone = $message->requires_phone;
                $item->invitation_count++;
                $item->store_chat = true;
                $item->invitation_assigned = true;
                $item->last_visit = time();

                if ($message->show_random_operator == 1) {
                    $item->operator_user_id = erLhcoreClassChat::getRandomOnlineUserID(array('operators' => explode(',',trim($message->operator_ids))));
                }

                $campaign = erLhAbstractModelProactiveChatCampaignConversion::findOne(array('filterin' => array('invitation_status' => array(
                    erLhAbstractModelProactiveChatCampaignConversion::INV_SEND,
                    erLhAbstractModelProactiveChatCampaignConversion::INV_SHOWN
                )),'filter' => array('vid_id' => $item->id, 'invitation_id' => $message->id)));

                $message->executed_times += 1;
                $message->updateThis(array('update' => array(
                    'executed_times'
                )));

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

                $detect = new Mobile_Detect;
                $detect->setUserAgent($item->user_agent);
                $campaign->device_type = ($detect->isMobile() ? ($detect->isTablet() ? 2 : 1) : 0);
                $campaign->saveThis();

                // Set conversion for trackback for online visitor record
                $item->conversion_id = $campaign->id;

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('onlineuser.proactive_triggered', array('message' => & $message, 'ou' => & $item));
            } else {
			    // We know there is invitation based on current criteria just time on site is still not matched.
                $item->next_reschedule = $message->time_on_site - $item->time_on_site;
            }
		}
	}
	
	public function customForm(){
	    return 'proactive_invitation.tpl.php';
	}
	
	public function dependFooterJs(){
	    return '<script type="text/javascript" src="'.erLhcoreClassDesign::designJS('js/angular-sanitize.min.js;js/angular.lhc.events.js;js/angular.lhc.theme.js').'"></script>';
	}
	
	public function validateInput($params)
	{
	    $params['obj'] = & $this;
	    erLhcoreClassChatEvent::validateProactive($params);
	}
	
	public function afterUpdate()
	{
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
	}

	public function afterRemove()
	{
	    foreach (erLhAbstractModelProactiveChatInvitationEvent::getList(array('filter' => array('invitation_id' => $this->id))) as $oldEvent) {
            $oldEvent->removeThis();
	    }
	}

    public function beforeUpdate()
    {
        $this->design_data = json_encode($this->design_data_array);
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

    public function afterSave()
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
	public $referrer = '';
	public $operator_ids = '';
	public $tag = '';
	public $dynamic_invitation = 0;
	public $event_invitation = 0;
	public $iddle_for = 0;
	public $event_type = 0;
	public $autoresponder_id = 0;
	public $show_on_mobile = 0;
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

	public $next_reschedule = 0;
	public $hide_add = false;
	public $hide_delete = false;

}

?>