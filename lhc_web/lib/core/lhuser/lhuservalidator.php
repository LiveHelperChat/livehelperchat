<?php

/**
 * Class used for validator
 * */

class erLhcoreClassUserValidator {
	
	public static function validateUser(& $userData, $params = array()) {
		
		$definition = array (
			'Password' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'Password1' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'Email' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'validate_email'
			),
			'Name' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'Surname' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'ChatNickname' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'Username' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'UserDisabled' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
			'HideMyStatus' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
			'UserInvisible' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),			
			'ReceivePermissionRequest' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
			'JobTitle' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'UserTimeZone' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'DefaultGroup' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'int',
				null,
				FILTER_REQUIRE_ARRAY
			),
			'Skype' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'XMPPUsername' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			)
		);
		
		$form = new ezcInputForm( INPUT_POST, $definition);
		
		$Errors = array();
		
		if (isset($params['user_new']) && $params['user_new'] == true) {
			
			if ( !$form->hasValidData( 'Username' ) || $form->Username == '') {
				$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Please enter a username');
			} else {
				
				$userData->username = $form->Username;
				
				if(erLhcoreClassModelUser::userExists($userData->username) === true) {
					$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','User exists');
				}
			}
			
			if ( $form->hasValidData( 'Password' ) && $form->hasValidData( 'Password1' ) ) {
    			$userData->password_temp_1 = $form->Password;
    			$userData->password_temp_2 = $form->Password1;
			}
			
			if ( !$form->hasValidData( 'Password' ) || !$form->hasValidData( 'Password1' ) || $form->Password == '' || $form->Password1 == '' || $form->Password != $form->Password1) {
				$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Passwords mismatch');
			} else {
				$userData->setPassword($form->Password);
				$userData->password_front = $form->Password;
			}
			
		} elseif ($params['user_edit'] && $params['user_edit'] == true) {
			
		    if ((isset($params['can_edit_groups']) && $params['can_edit_groups'] == true) || !isset($params['can_edit_groups'])) {
    			if ( !$form->hasValidData( 'Username' ) || $form->Username == '') {
    				$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Please enter a username');
    			} else {
    				
    				if($form->Username != $userData->username) {
    					
    					$userData->username = $form->Username;
    					
    					if(erLhcoreClassModelUser::userExists($userData->username) === true) {
    						$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','User exists');
    					}
    				}
    			}	
    			
    			if ( $form->hasValidData( 'Password' ) && $form->hasValidData( 'Password1' ) ) {
    			    $userData->password_temp_1 = $form->Password;
    			    $userData->password_temp_2 = $form->Password1;
    			}
    			
    			if ( $form->hasInputField( 'Password' ) && (!$form->hasInputField( 'Password1' ) || $form->Password != $form->Password1 ) ) {
    				$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Passwords mismatch');
    			} else {
    				
    				if ($form->hasInputField( 'Password' ) && $form->hasInputField( 'Password1' ) && $form->Password != '' && $form->Password1 != '') {
    					$userData->setPassword($form->Password);
    					$userData->password_front = $form->Password;
    				}
    				
    			}
		    }
		    
		}  else {
			$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','User action type not set');
		}
		
		if ( $form->hasValidData( 'ChatNickname' ) && $form->ChatNickname != '' ) {
		    $userData->chat_nickname = $form->ChatNickname;
		} else {
		    $userData->chat_nickname = '';
		}
		
		if ( !$form->hasValidData( 'Email' ) ) {
			$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Wrong email address');
		} else {
			$userData->email = $form->Email;
		}
		
		if ( !$form->hasValidData( 'Name' ) || $form->Name == '' ) {
			$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Please enter a name');
		} else {
			$userData->name = $form->Name;
		}
		
		if ( $form->hasValidData( 'Surname' ) && $form->Surname != '') {
			$userData->surname = $form->Surname;
		} else {
			$userData->surname = '';
		}
				
		if ( $form->hasValidData( 'JobTitle' ) && $form->JobTitle != '') {
			$userData->job_title = $form->JobTitle;
		} else {
			$userData->job_title = '';
		}
		
		if ( $form->hasValidData( 'UserTimeZone' ) && $form->UserTimeZone != '') {
			$userData->time_zone = $form->UserTimeZone;
		} else {
			$userData->time_zone = '';
		}
		
		if ( $form->hasValidData( 'Skype' ) && $form->Skype != '') {
			$userData->skype = $form->Skype;
		} else {
			$userData->skype = '';
		}
		
		if ( $form->hasValidData( 'XMPPUsername' ) && $form->XMPPUsername != '') {
			$userData->xmpp_username = $form->XMPPUsername;
		} else {
			$userData->xmpp_username = '';
		}
		
		if ( $form->hasValidData( 'HideMyStatus' ) && $form->HideMyStatus == true )	{
			$userData->hide_online = 1;
		} else {
			$userData->hide_online = 0;
		}
		
		if ( $form->hasValidData( 'UserInvisible' ) && $form->UserInvisible == true ) {
			$userData->invisible_mode = 1;
		} else {
			$userData->invisible_mode = 0;
		}
		
		if ( $form->hasValidData( 'ReceivePermissionRequest' ) && $form->ReceivePermissionRequest == true ) {
			$userData->rec_per_req = 1;
		} else {
			$userData->rec_per_req = 0;
		}
		
		if ((isset($params['can_edit_groups']) && $params['can_edit_groups'] == true) || !isset($params['can_edit_groups'])) {

		    if ( $form->hasValidData( 'UserDisabled' ) && $form->UserDisabled == true )	{
		        $userData->disabled = 1;
		    } else {
		        $userData->disabled = 0;
		    }
		    
    		if ( $form->hasValidData( 'DefaultGroup' ) ) {
    		    
    		    if ($params['groups_can_edit'] == true) {
    		        $userData->user_groups_id = $form->DefaultGroup;
    		    } else {
    		        $unknownGroups = array_diff($form->DefaultGroup, $params['groups_can_edit']);
    		        
    		        if (empty($unknownGroups)) {
    		            $userData->user_groups_id = $form->DefaultGroup;
    		        } else {
    		            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','You are trying to assign group which are not known!');
    		        }
    		    }
    		    
    		} else {
    			$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Please choose a default user group');
    		}
		}
		
		return $Errors;
		
	}	
	
	public static function validateDepartments(& $userData, $params = array()) {
	
		$globalDepartament = array();
	
		if (isset($_POST['all_departments']) && $_POST['all_departments'] == 'on') {
			$userData->all_departments = 1;
			$globalDepartament[] = 0;
		} else {
			$userData->all_departments = 0;
			if(isset($params['all_departments_0_global_value'])) {
				$globalDepartament[] = $params['all_departments_0_global_value'];
			}
		}
			
		if (isset($_POST['UserDepartament']) && count($_POST['UserDepartament']) > 0) {
			$globalDepartament = array_merge($_POST['UserDepartament'], $globalDepartament);
		}
	
		$userData->departments_ids = implode(',', $globalDepartament);
	
		return $globalDepartament;
	
	}
	
	public static function validateShowAllPendingOption() {
	
		$definition = array(
			'showAllPendingEnabled' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			)
		);
	
		$form = new ezcInputForm( INPUT_POST, $definition );
	
		if ( $form->hasValidData( 'showAllPendingEnabled' ) && $form->showAllPendingEnabled == true ) {
			$showAllPending = 1;
		} else {
			$showAllPending = 0;
		}
	
		return $showAllPending;
	
	}
	
	public static function validateUserPhoto(& $userData, $params = array()) {
		
		$Errors = false;
		
		if ( isset($_FILES["UserPhoto"]) && is_uploaded_file($_FILES["UserPhoto"]["tmp_name"]) && $_FILES["UserPhoto"]["error"] == 0 && erLhcoreClassImageConverter::isPhoto('UserPhoto') ) {
			
			$Errors = array();
			
			$dir = 'var/userphoto/' . date('Y') . 'y/' . date('m') . '/' . date('d') .'/' . $userData->id . '/';
			 
			erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.edit.photo_path',array('dir' => & $dir,'storage_id' => $userData->id));
			 
			$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.edit.photo_store', array('file_post_variable' => 'UserPhoto', 'dir' => & $dir, 'storage_id' => $userData->id));
		
			// There was no callbacks
			if ($response === false) {
				erLhcoreClassFileUpload::mkdirRecursive( $dir );
				$file = qqFileUploader::upload($_FILES,'UserPhoto',$dir);
			} else {
				$file = $response['data'];
			}
			
			if ( !empty($file["errors"]) ) {
				
				foreach ($file["errors"] as $err) {
					$Errors[] = $err;
				}
				
			} else {
			
				$userData->removeFile();
				$userData->filename	= $file["data"]["filename"];
				$userData->filepath	= $file["data"]["dir"];
			
				$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.edit.photo_resize_150', array('mime_type' => $file["data"]['mime_type'],'user' => $userData));
			
				if ($response === false) {
					erLhcoreClassImageConverter::getInstance()->converter->transform( 'photow_150', $userData->file_path_server, $userData->file_path_server );
					chmod($userData->file_path_server, 0644);
				}
				
			}
			
		}
		
		return $Errors;
	}
	
	public static function validateUserNew(& $userData, & $params = array()) {
	
		$params['user_new'] = true;
		
		$Errors = self::validateUser($userData, $params);
		
		if (isset($params['global_departament'])) {
			$params['global_departament'] = self::validateDepartments($userData);
		}
		
		if (isset($params['show_all_pending'])) {
			$params['show_all_pending'] = self::validateShowAllPendingOption();
		}
		
		erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.new_user', array('userData' => & $userData, 'errors' => & $Errors));
		
		return $Errors;
		
	}
	
	public static function validateUserEdit(& $userData, & $params = array()) {
	
		$params['user_edit'] = true;
		
		$Errors = self::validateUser($userData, $params);
		
		erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.edit_user',array('userData' => & $userData, 'errors' => & $Errors));
		
		return $Errors;
		
	}
	
	public static function validateSpeech() {
		
		$data = array();
		
		$definition = array(
			'select_language' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1)
			),
			'select_dialect' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'string'
			)
		);
				
		$form = new ezcInputForm( INPUT_POST, $definition );
		
		$Errors = array();
		
		$data['speech_language'] = ( $form->hasValidData( 'select_language' ) ) ? $form->select_language : ''; 
		
		$data['speech_dialect'] = ( $form->hasValidData( 'select_dialect' ) && $form->hasValidData( 'select_dialect' ) != '0' ) ? $form->select_dialect : ''; 
		
		return $data;
		
	}
	
	public static function validateVisibilityList() {
		
		$data = array();
		
		$definition = array(
			'pendingTabEnabled' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
			'activeTabEnabled' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
			'closedTabEnabled' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
			'unreadTabEnabled' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
			'mychatsTabEnabled' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			)
		);
		
		$form = new ezcInputForm( INPUT_POST, $definition );
		
		$Errors = array();
		
		$data['enable_pending_list'] = ( $form->hasValidData( 'pendingTabEnabled' ) && $form->pendingTabEnabled == true ) ? 1 : 0;
		
		$data['enable_active_list'] = ( $form->hasValidData( 'activeTabEnabled' ) && $form->activeTabEnabled == true ) ? 1 : 0;
				
		$data['enable_close_list'] = ( $form->hasValidData( 'closedTabEnabled' ) && $form->closedTabEnabled == true ) ? 1 : 0;
		
		$data['enable_unread_list'] = ( $form->hasValidData( 'unreadTabEnabled' ) && $form->unreadTabEnabled == true ) ? 1 : 0;
		
		$data['enable_mchats_list'] = ( $form->hasValidData( 'mychatsTabEnabled' ) && $form->mychatsTabEnabled == true ) ? 1 : 0;
		
		return $data;
	}
	
	public static function validateNotifications() {
	    $data = array();
	    
	    $definition = array(
	        'show_alert_chat' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'sn_off' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'ownntfonly' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'trackactivity' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
	        'trackactivitytimeout' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'int'
	        )
	    );
	    
	    $form = new ezcInputForm( INPUT_POST, $definition );
	    
	    $Errors = array();
	    
	    $data['show_alert_chat'] = ( $form->hasValidData( 'show_alert_chat' ) && $form->show_alert_chat == true ) ? 1 : 0;
	    $data['sn_off'] = ( $form->hasValidData( 'sn_off' ) && $form->sn_off == true ) ? 1 : 0;
	    $data['ownntfonly'] = ( $form->hasValidData( 'ownntfonly' ) && $form->ownntfonly == true ) ? 1 : 0;
	    $data['trackactivity'] = ( $form->hasValidData( 'trackactivity' ) && $form->trackactivity == true ) ? 1 : 0;
	    $data['trackactivitytimeout'] = ( $form->hasValidData( 'trackactivitytimeout' )) ? (int)$form->trackactivitytimeout : -1;
	    
	    return $data;
	}
	
	public static function validateDepartmentsGroup(& $userData, $params = array()) {
	
	    $globalDepartament = array();
	
	    if (isset($_POST['UserDepartamentGroup']) && count($_POST['UserDepartamentGroup']) > 0) {
	        $globalDepartament = array_merge($_POST['UserDepartamentGroup'], $globalDepartament);
	    }
	    	
	    return $globalDepartament;
	}
	
	public static function validateAccount(& $userData) {
		
		$definition = array(
			'Password' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'Password1' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'Email' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::REQUIRED, 'validate_email'
			),
			'Name' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
			),
			'Surname' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
			),
			'Username' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'JobTitle' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'Skype' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),		 
			'XMPPUsername' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'ChatNickname' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'UserTimeZone' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'UserInvisible' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
			'ReceivePermissionRequest' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			)
		);
		
		$form = new ezcInputForm( INPUT_POST, $definition );
		
		$Errors = array();
		
		if ( !$form->hasValidData( 'Username' ) || $form->Username == '') {
			$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Please enter a username');
		} else {
		
			if($form->Username != $userData->username) {
					
				$userData->username = $form->Username;
					
				if(erLhcoreClassModelUser::userExists($userData->username) === true) {
					$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','User exists');
				}
			}
		}
				
		if ( $form->hasValidData( 'Password' ) && $form->hasValidData( 'Password1' ) ) {
		    $userData->password_temp_1 = $form->Password;
		    $userData->password_temp_2 = $form->Password1;
		}
		
		if ( $form->hasInputField( 'Password' ) && (!$form->hasInputField( 'Password1' ) || $form->Password != $form->Password1 ) ) {
			$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Passwords mismatch');
		} else {
			if ($form->hasInputField( 'Password' ) && $form->hasInputField( 'Password1' ) && $form->Password != '' && $form->Password1 != '') {
				$userData->setPassword($form->Password);
				$userData->password_front = $form->Password;
			}
		}

		if ( $form->hasValidData( 'ChatNickname' ) && $form->ChatNickname != '' ) {
		    $userData->chat_nickname = $form->ChatNickname;
		} else {
		    $userData->chat_nickname = '';
		}

		if ( !$form->hasValidData( 'Email' ) ) {
			$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Wrong email address');
		} else {
			$userData->email = $form->Email;
		}
		
		if ( !$form->hasValidData( 'Name' ) || $form->Name == '' ) {
			$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Please enter a name');
		} else {
			$userData->name = $form->Name;
		}
		
		if ( $form->hasValidData( 'Surname' ) && $form->Surname != '') {
			$userData->surname = $form->Surname;
		} else {
			$userData->surname = '';
		}
		
		if ( $form->hasValidData( 'JobTitle' ) && $form->JobTitle != '') {
			$userData->job_title = $form->JobTitle;
		} else {
			$userData->job_title = '';
		}
		
		if ( $form->hasValidData( 'UserTimeZone' ) && $form->UserTimeZone != '') {
			$userData->time_zone = $form->UserTimeZone;
			CSCacheAPC::getMem()->setSession('lhc_user_timezone', $userData->time_zone, true);
		} else {
			CSCacheAPC::getMem()->setSession('lhc_user_timezone', '', true);
			$userData->time_zone = '';
		}
		
		if ( erLhcoreClassUser::instance()->hasAccessTo('lhuser','changevisibility') ) {
			if ( $form->hasValidData( 'UserInvisible' ) && $form->UserInvisible == true ) {
				$userData->invisible_mode = 1;
			} else {
				$userData->invisible_mode = 0;
			}
		}
		
		if ( erLhcoreClassUser::instance()->hasAccessTo('lhuser','receivepermissionrequest') ) {
			if ( $form->hasValidData( 'ReceivePermissionRequest' ) && $form->ReceivePermissionRequest == true ) {
				$userData->rec_per_req = 1;
			} else {
				$userData->rec_per_req = 0;
			}
		}
		
		if ( erLhcoreClassUser::instance()->hasAccessTo('lhuser','changeskypenick') ) {
			if ( $form->hasValidData( 'Skype' ) && $form->Skype != '' ) {
				$userData->skype = $form->Skype;
			} else {
				$userData->skype = '';
			}
		}
		
		if ( $form->hasValidData( 'XMPPUsername' ) && $form->XMPPUsername != '') {
			$userData->xmpp_username = $form->XMPPUsername;
		} else {
			$userData->xmpp_username = '';
		}
		
		// new event for save additional account fields
		erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.account.update', array('userData' => & $userData, 'errors' => & $Errors));
		
		return $Errors;
		
	}
	
	
}

?>