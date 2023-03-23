<?php

/**
 * Class used for validator
 * */

class erLhcoreClassUserValidator {

    public static function validateDepartmentAssignment(& $userDep) {
        $definition = array(
            'exc_indv_autoasign' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'ro' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int', ['min_range' => 0, 'max_range' => 1]
            ),
            'chat_max_priority' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'chat_min_priority' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'assign_priority' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            )
        );

        $form = new ezcInputForm( INPUT_POST, $definition );

        $Errors = [];

        if ( $form->hasValidData( 'exc_indv_autoasign' ) && $form->exc_indv_autoasign == true ) {
            $userDep->exc_indv_autoasign = 1;
        } else {
            $userDep->exc_indv_autoasign = 0;
        }

        if ($form->hasValidData( 'ro' )) {
            if ($userDep instanceof erLhcoreClassModelDepartamentGroupUser) {
                $userDep->read_only = $form->ro;
            } else {
                $userDep->ro = $form->ro;
            }
        } else {
            $Errors[] = 'Invalid Read Only value';
        }

        if ( $form->hasValidData( 'chat_max_priority' )) {
            $userDep->chat_max_priority = $form->chat_max_priority;
        } else {
            $Errors[] = 'Invalid chat_max_priority';
        }

        if ( $form->hasValidData( 'chat_min_priority' )) {
            $userDep->chat_min_priority = $form->chat_min_priority;
        } else {
            $Errors[] = 'Invalid chat_max_priority';
        }

        if ( $form->hasValidData( 'assign_priority' )) {
            $userDep->assign_priority = $form->assign_priority;
        } else {
            $Errors[] = 'Invalid assign_priority';
        }

        return $Errors;
    }

    public static function validateAliasDepartment(& $userDepAlias, $params = array())
    {
        $definition = array(
            'alias_nick' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'avataralias_dep' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'alias_photo_delete' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            )
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = [];

        if ( $form->hasValidData( 'alias_nick' )) {
            $userDepAlias->nick = $form->alias_nick;
        }

        if ( $form->hasValidData( 'avataralias_dep' )) {
            $userDepAlias->avatar = $form->avataralias_dep;
        }

        if ( $form->hasValidData( 'alias_photo_delete' ) && $form->alias_photo_delete == true) {
            $userDepAlias->removeFile();
        }

        // We want ID always
        $userDepAlias->saveThis();

        if ( isset($_FILES["alias_photo"]) && is_uploaded_file($_FILES["alias_photo"]["tmp_name"]) && $_FILES["alias_photo"]["error"] == 0 && erLhcoreClassImageConverter::isPhoto('alias_photo') ) {

            $Errors = array();

            $dir = 'var/userphoto/' . date('Y') . 'yna/' . date('m') . '/' . date('d') .'/' . $userDepAlias->id . '/';

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.edit.photo_path', array('dir' => & $dir, 'storage_id' => $userDepAlias->id));

            $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.edit.photo_store', array('file_post_variable' => 'alias_photo', 'dir' => & $dir, 'storage_id' => $userDepAlias->id));

            // There was no callbacks
            if ($response === false) {
                erLhcoreClassFileUpload::mkdirRecursive( $dir );
                $file = qqFileUploader::upload($_FILES,'alias_photo',$dir);
            } else {
                $file = $response['data'];
            }

            if ( !empty($file["errors"]) ) {

                foreach ($file["errors"] as $err) {
                    $Errors[] = $err;
                }

            } else {
                $userDepAlias->removeFile();
                $userDepAlias->filename	= $file["data"]["filename"];
                $userDepAlias->filepath	= $file["data"]["dir"];

                $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.edit.photo_resize_150', array('mime_type' => $file["data"]['mime_type'], 'user' => $userDepAlias));

                if ($response === false) {
                    if ($file["data"]['mime_type'] != 'image/svg+xml') {
                        erLhcoreClassImageConverter::getInstance()->converter->transform( 'photow_150', $userDepAlias->file_path_server, $userDepAlias->file_path_server );
                    }
                    chmod($userDepAlias->file_path_server, 0644);
                }
            }
        }

        // Save always
        $userDepAlias->saveThis();

        return $Errors;
    }

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
            'force_logout' => new ezcInputFormDefinitionElement(
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
            'avatar' => new ezcInputFormDefinitionElement(
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
    				$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Passwords mismatch');
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

		self::validatePassword($userData,$Errors);

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

		if ($form->hasValidData( 'avatar' ) ) {
            $userData->avatar = $form->avatar;
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
			$userData->skype = mb_substr($form->Skype,0,50);
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

		if ( $form->hasValidData( 'force_logout' ) && $form->force_logout == true )	{
			$userData->force_logout = 1;
		} else {
			$userData->force_logout = 0;
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

    		    if ($params['groups_can_edit'] === true) {
    		        $userData->user_groups_id = $form->DefaultGroup;

    		        $groupsRequired = erLhcoreClassModelGroup::getList(array('filter' => array('required' => 1)));

    		        if (!empty($groupsRequired)) {
                        $diff = array_diff(array_keys($groupsRequired), $userData->user_groups_id);

                        if (count($diff) == count($groupsRequired)) {
                            $Errors['group_required'] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','You have to choose one of required groups!');
                        }
    		        }

    		    } else {

                    $groupsMustChecked = array_intersect($userData->user_groups_id,$params['groups_can_read']);

    		        $unknownGroups = array_diff($form->DefaultGroup, $params['groups_can_edit']);

    		        if (empty($unknownGroups)) {
    		            $userData->user_groups_id = $form->DefaultGroup;
                        foreach ($groupsMustChecked as $groupAdd) {
                            $userData->user_groups_id[] = $groupAdd;
                        }

    		            if (!empty($params['groups_can_edit'])) {
                            $groupsRequired = erLhcoreClassModelGroup::getList(array('filterin' => array('id' => $params['groups_can_edit']), 'filter' => array('required' => 1)));

                            if (!empty($groupsRequired)) {
                                $diff = array_diff(array_keys($groupsRequired), $userData->user_groups_id);
    
                                if (count($diff) == count($groupsRequired)) {
                                    $Errors['group_required'] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','You have to choose one of required groups!');
                                }
                            }
    		            }

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

        if (!isset($params['all_departments']) || $params['all_departments'] == true)
        {
            if (isset($_POST['all_departments']) && $_POST['all_departments'] == 'on') {
                $userData->all_departments = 1;
                $globalDepartament[] = 0;
            } else {
                $userData->all_departments = 0;
                if(isset($params['all_departments_0_global_value'])) {
                    $globalDepartament[] = $params['all_departments_0_global_value'];
                } else {
                    $globalDepartament[] = -1;
                }
            }
        } else {
            $globalDepartament[] = $userData->all_departments == 1 ? 0 : -1;
        }

		if ($params['edit_params']['individual']['edit_all'] == true) {
            if (isset($_POST['UserDepartament']) && count($_POST['UserDepartament']) > 0) {
                $globalDepartament = array_merge($_POST['UserDepartament'], $globalDepartament);
            }
		} elseif ($params['edit_params']['individual']['edit_personal'] == true) {
            // Append departments present operator can't control
            $globalDepartament = array_merge($params['edit_params']['individual']['remote_id_write'], $globalDepartament);

            if (isset($_POST['UserDepartament']) && count($_POST['UserDepartament']) > 0) {
                foreach ($params['edit_params']['individual']['id'] as $depId) {
                    if (in_array($depId, $_POST['UserDepartament'])){
                        $globalDepartament[] = $depId;
                    }
                }
            }

        } else {
            $globalDepartament = array_merge($params['edit_params']['individual']['remote_id_write_all'], $globalDepartament); // Keep previously selected departments
        }

        if ($params['edit_params']['individual']['edit_all'] == true) {
            if (isset($_POST['UserDepartamentRead']) && count($_POST['UserDepartamentRead']) > 0) {
                $globalDepartament = array_merge($_POST['UserDepartamentRead'], $globalDepartament);
            }
		} elseif ($params['edit_params']['individual']['edit_personal'] == true) {
            // Append departments present operator can't control
            $globalDepartament = array_merge($params['edit_params']['individual']['remote_id_read'], $globalDepartament);

            if (isset($_POST['UserDepartamentRead']) && count($_POST['UserDepartamentRead']) > 0) {
                foreach ($params['edit_params']['individual']['id'] as $depId) {
                    if (in_array($depId, $_POST['UserDepartamentRead'])){
                        $globalDepartament[] = $depId;
                    }
                }
            }

        } else {
            $globalDepartament = array_merge($params['edit_params']['individual']['remote_id_read_all'], $globalDepartament); // Keep previously selected departments
        }

        $globalDepartament = array_unique($globalDepartament);

		$userData->departments_ids = implode(',', $globalDepartament);
	
		return $globalDepartament;
	
	}
	
	public static function validateShowAllPendingOption() {
	
		$definition = array(
			'showAllPendingEnabled' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
            'autoAccept' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
            'exclude_autoasign' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
            'remove_closed_chats' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
            'remove_closed_chats_remote' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
            'auto_uppercase' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
            'auto_join_private' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
            'auto_preload' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
            'no_scroll_bottom' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
            'chat_text_rows' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 2, 'max_range' => 50)
			),
            'maximumChats' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'int'
			),
            'remove_close_timeout' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 5, 'max_range' => 60)
			),
		);
	
		$form = new ezcInputForm( INPUT_POST, $definition );

		$result = array();

		if ( $form->hasValidData( 'showAllPendingEnabled' ) && $form->showAllPendingEnabled == true ) {
            $result['show_all_pending'] = 1;
		} else {
            $result['show_all_pending'] = 0;
		}

		if ( $form->hasValidData( 'auto_uppercase' ) && $form->auto_uppercase == true ) {
            $result['auto_uppercase'] = 1;
		} else {
            $result['auto_uppercase'] = 0;
		}

		if ( $form->hasValidData( 'no_scroll_bottom' ) && $form->no_scroll_bottom == true ) {
            $result['no_scroll_bottom'] = 1;
		} else {
            $result['no_scroll_bottom'] = 0;
		}

		if ($form->hasValidData( 'chat_text_rows' )) {
            $result['chat_text_rows'] = $form->chat_text_rows;
		} else {
            $result['chat_text_rows'] = 2;
		}

		if ( $form->hasValidData( 'auto_preload' ) && $form->auto_preload == true ) {
            $result['auto_preload'] = 1;
		} else {
            $result['auto_preload'] = 0;
		}

		if ( $form->hasValidData( 'autoAccept' ) && $form->autoAccept == true ) {
            $result['auto_accept'] = 1;
		} else {
            $result['auto_accept'] = 0;
		}

		if ( $form->hasValidData( 'remove_closed_chats' ) && $form->remove_closed_chats == true ) {
            $result['remove_closed_chats'] = 1;
		} else {
            $result['remove_closed_chats'] = 0;
		}

		if ( $form->hasValidData( 'remove_closed_chats_remote' ) && $form->remove_closed_chats_remote == true ) {
            $result['remove_closed_chats_remote'] = 1;
		} else {
            $result['remove_closed_chats_remote'] = 0;
		}

		if ( $form->hasValidData( 'auto_join_private' ) && $form->auto_join_private == true ) {
            $result['auto_join_private'] = 1;
		} else {
            $result['auto_join_private'] = 0;
		}

		if ( $form->hasValidData( 'exclude_autoasign' ) && $form->exclude_autoasign == true ) {
            $result['exclude_autoasign'] = 1;
		} else {
            $result['exclude_autoasign'] = 0;
		}

		if ( $form->hasValidData( 'maximumChats' )) {
            $result['max_chats'] = $form->maximumChats;
		} else {
            $result['max_chats'] = 0;
		}
        
		if ( $form->hasValidData( 'remove_close_timeout' )) {
            $result['remove_close_timeout'] = $form->remove_close_timeout;
		} else {
            $result['remove_close_timeout'] = 5;
		}
	
		return $result;
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
				    if ($file["data"]['mime_type'] != 'image/svg+xml') {
                        erLhcoreClassImageConverter::getInstance()->converter->transform( 'photow_150', $userData->file_path_server, $userData->file_path_server );
                    }
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
			$params['global_departament'] = self::validateDepartments($userData, ['edit_params' => $params['edit_params']]);
		}

		if (isset($params['show_all_pending'])) {
			$paramsPending = self::validateShowAllPendingOption();
            $params = array_merge($params,$paramsPending);

            $paramsNotifications = erLhcoreClassUserValidator::validateNotifications();
            $params = array_merge($params,$paramsNotifications);
		}

        $userData->auto_accept = $params['auto_accept'];
        $userData->max_active_chats = $params['max_chats'];
        $userData->exclude_autoasign = $params['exclude_autoasign'];
        $userData->pswd_updated = time();

		erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.new_user', array('userData' => & $userData, 'errors' => & $Errors));

        if ($userData->time_zone == '') {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Please choose a user Time Zone!');
        }

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
			),
            'userLanguages' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'string',
                null,
                FILTER_REQUIRE_ARRAY
            )
		);
				
		$form = new ezcInputForm( INPUT_POST, $definition );
		
		$Errors = array();
		
		$data['speech_language'] = ( $form->hasValidData( 'select_language' ) ) ? $form->select_language : ''; 
		
		$data['speech_dialect'] = ( $form->hasValidData( 'select_dialect' ) && $form->hasValidData( 'select_dialect' ) != '0' ) ? $form->select_dialect : '';

        $data['user_languages'] = ($form->hasValidData( 'userLanguages' ) && !empty($form->userLanguages)) ? $form->userLanguages : array();

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
			),
            'botchatsTabEnabled' => new ezcInputFormDefinitionElement(
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

		$data['enable_bot_list'] = ( $form->hasValidData( 'botchatsTabEnabled' ) && $form->botchatsTabEnabled == true ) ? 1 : 0;

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
	        ),
            'show_alert_transfer' => new ezcInputFormDefinitionElement(
	            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	        ),
            'hide_quick_notifications' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
	    );
	    
	    $form = new ezcInputForm( INPUT_POST, $definition );

	    $data['show_alert_chat'] = ( $form->hasValidData( 'show_alert_chat' ) && $form->show_alert_chat == true ) ? 1 : 0;
	    $data['sn_off'] = ( $form->hasValidData( 'sn_off' ) && $form->sn_off == true ) ? 1 : 0;
	    $data['ownntfonly'] = ( $form->hasValidData( 'ownntfonly' ) && $form->ownntfonly == true ) ? 1 : 0;
	    $data['trackactivity'] = ( $form->hasValidData( 'trackactivity' ) && $form->trackactivity == true ) ? 1 : 0;
	    $data['trackactivitytimeout'] = ( $form->hasValidData( 'trackactivitytimeout' )) ? (int)$form->trackactivitytimeout : -1;
	    $data['show_alert_transfer'] = ( $form->hasValidData( 'show_alert_transfer' ) && $form->show_alert_transfer == true) ? (int)$form->show_alert_transfer : 0;
	    $data['hide_quick_notifications'] = ( $form->hasValidData( 'hide_quick_notifications' ) && $form->hide_quick_notifications == true) ? (int)$form->hide_quick_notifications : 0;

	    return $data;
	}
	
	public static function validateDepartmentsGroup(& $userData, $params = array()) {
	
	    $globalDepartament = array();

        $attr = isset($params['read_only']) && $params['read_only'] == true ?  'UserDepartamentGroupRead' : 'UserDepartamentGroup';

        if (isset($params['exclude_auto']) && $params['exclude_auto'] == true) {
            $attr = 'UserDepartamentGroupAutoExc';
        }

        if ($params['edit_params']['groups']['edit_all'] == true) {

            if (isset($_POST[$attr]) && count($_POST[$attr]) > 0) {
                $globalDepartament = array_merge($_POST[$attr], $globalDepartament);
            }

        } elseif ($params['edit_params']['groups']['edit_personal'] == true) {

            // Keep original departments present user can't edit in other user
            if (isset($params['read_only']) && $params['read_only'] == true) {
                $globalDepartament = $params['edit_params']['groups']['remote_id_read'];
            } else {
                $globalDepartament = $params['edit_params']['groups']['remote_id_write'];
            }

            if (isset($_POST[$attr]) && count($_POST[$attr]) > 0) {
                foreach ($_POST[$attr] as $depId) {
                    if (in_array($depId, $params['edit_params']['groups']['id'])) {
                        $globalDepartament[] = $depId;
                    }
                }
            }

        } else {
            if (isset($params['read_only']) && $params['read_only'] == true) {
                $globalDepartament = $params['edit_params']['groups']['remote_id_read_all'];
            } else {
                $globalDepartament = $params['edit_params']['groups']['remote_id_write_all'];
            }

            if (isset($params['exclude_auto']) && $params['exclude_auto'] == true) {
                $globalDepartament = $params['edit_params']['groups']['remote_id_auto_assign_all'];
            }
        }

	    return $globalDepartament;
	}

    public static function generatePassword() {

        $passwordData = (array)erLhcoreClassModelChatConfig::fetch('password_data')->data;

        $charactersList = [
            'uppercase_required' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'lowercase_required' => 'abcdefghijklmnopqrstuvwxyz',
            'number_required' => '0123456789',
            'special_required' => '~!@#$%^&*()_+-,.<>/?;:\'"{}[]\\|=',
        ];

        $passwordParts = [];

        foreach (['uppercase_required','number_required','special_required','lowercase_required'] as $parameter){
            if (isset($passwordData[$parameter]) && $passwordData[$parameter] > 0) {
                for ($i = 0; $i < $passwordData[$parameter]; $i++) {
                    $passwordParts[] = substr($charactersList[$parameter], random_int(1, strlen($charactersList[$parameter])) - 1, 1);
                }
            }
        }

        $passwordRandomized = [];

        while (!empty($passwordParts)) {
            $passwordRandomized[] = implode('',array_splice($passwordParts,random_int(1, count($passwordParts)) - 1,1));
        }

        if (empty($passwordRandomized)) {
            return erLhcoreClassModelForgotPassword::randomPassword(10);
        }

        return implode($passwordRandomized);
    }

	public static function validatePassword(& $userData, & $Errors)
    {
        if ($userData->password_temp_1 != '') {

            $length = mb_strlen($userData->password_temp_1);
            $lowercase = preg_match_all('@[a-z]@', $userData->password_temp_1);
            $uppercase = preg_match_all('@[A-Z]@', $userData->password_temp_1);
            $number    = preg_match_all('@[0-9]@', $userData->password_temp_1);
            $specialChars = preg_match_all('@[^\w]@', $userData->password_temp_1);

            $passwordData = (array)erLhcoreClassModelChatConfig::fetch('password_data')->data;

            if (isset($passwordData['length']) && $passwordData['length'] > 0 && $passwordData['length'] > $length) {
                $Errors[] = sprintf(erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Password has to be at least %d characters length'),$passwordData['length']);
            }

            if (isset($passwordData['uppercase_required']) && $passwordData['uppercase_required'] > 0 && $passwordData['uppercase_required'] > $uppercase) {
                $Errors[] = sprintf(erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Password has to have at-least %d uppercase letter'),$passwordData['uppercase_required']);
            }

            if (isset($passwordData['number_required']) && $passwordData['number_required'] > 0 && $passwordData['number_required'] > $number) {
                $Errors[] = sprintf(erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Password has to have at-least %d number(s)'),$passwordData['number_required']);
            }

            if (isset($passwordData['special_required']) && $passwordData['special_required'] > 1 && $passwordData['special_required'] > $specialChars) {
                $Errors[] = sprintf(erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Password has to have at-least %d special character(s)'),$passwordData['special_required']);
            }

            if (isset($passwordData['lowercase_required']) && $passwordData['lowercase_required'] > 1 && $passwordData['lowercase_required'] > $lowercase) {
                $Errors[] = sprintf(erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Password has to have at-least %d lowercase letters'),$passwordData['lowercase_required']);
            }
        }
    }

    public static function validatePasswordChange(& $userData, & $Errors)
    {
        $definition = array (
            'OldPassword' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'NewPassword' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'NewPassword1' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            )
        );

        $form = new ezcInputForm( INPUT_POST, $definition);

        $Errors = array();

        if ( $form->hasValidData( 'NewPassword' ) && $form->hasValidData( 'NewPassword1' ) ) {
            $userData->password_temp_1 = $form->NewPassword;
            $userData->password_temp_2 = $form->NewPassword1;
        }

        if (!$form->hasValidData( 'OldPassword' ) || !password_verify($form->OldPassword, $userData->password)) {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Old password incorrect!');
        }

        if ( !$form->hasValidData( 'NewPassword' ) || !$form->hasValidData( 'NewPassword1' ) || $form->NewPassword == '' || $form->NewPassword1 == '' || $form->NewPassword != $form->NewPassword1) {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Passwords mismatch!');
        } else {
            $userData->setPassword($form->NewPassword);
            $userData->password_front = $form->NewPassword;
        }

        if ($form->hasValidData( 'NewPassword' ) && $form->hasValidData( 'OldPassword' ) && $form->OldPassword === $form->NewPassword) {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Old and new password has to be different!');
        }

        self::validatePassword($userData, $Errors);

        return $Errors;
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
				ezcInputFormDefinitionElement::OPTIONAL, 'validate_email'
			),
			'Name' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
            'avatar' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'Surname' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
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
            'HideMyStatus' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
			'ReceivePermissionRequest' => new ezcInputFormDefinitionElement(
				ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			)
		);
		
		$form = new ezcInputForm( INPUT_POST, $definition );
		
		$Errors = array();

        if (erLhcoreClassUser::instance()->hasAccessTo('lhuser', 'change_core_attributes') ) {
            if (!$form->hasValidData('Username') || $form->Username == '') {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator', 'Please enter a username');
            } else {
                if ($form->Username != $userData->username) {
                    $userData->username = $form->Username;
                    if (erLhcoreClassModelUser::userExists($userData->username) === true) {
                        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator', 'User exists');
                    }
                }
            }
        }

        if ( $form->hasValidData( 'HideMyStatus' ) && $form->HideMyStatus == true )	{
            $userData->hide_online = 1;
        } else {
            $userData->hide_online = 0;
        }

        if (erLhcoreClassUser::instance()->hasAccessTo('lhuser', 'change_password') ) {

            if ($form->hasValidData('Password') && $form->hasValidData('Password1')) {
                $userData->password_temp_1 = $form->Password;
                $userData->password_temp_2 = $form->Password1;
            }

            if ($form->hasInputField('Password') && (!$form->hasInputField('Password1') || $form->Password != $form->Password1)) {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator', 'Passwords mismatch');
            } else {
                if ($form->hasInputField('Password') && $form->hasInputField('Password1') && $form->Password != '' && $form->Password1 != '') {
                    $userData->setPassword($form->Password);
                    $userData->password_front = $form->Password;
                }
            }
            
            self::validatePassword($userData, $Errors);
        }

        if (erLhcoreClassUser::instance()->hasAccessTo('lhuser', 'change_core_attributes') ) {
            if ( !$form->hasValidData( 'Email' ) ) {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Wrong email address');
            } else {
                $userData->email = $form->Email;
            }
        }

        if (erLhcoreClassUser::instance()->hasAccessTo('lhuser', 'change_chat_nickname') ) {
            if ( $form->hasValidData( 'ChatNickname' ) && $form->ChatNickname != '' ) {
                $userData->chat_nickname = $form->ChatNickname;
            } else {
                $userData->chat_nickname = '';
            }
        }

        if (erLhcoreClassUser::instance()->hasAccessTo('lhuser', 'change_name_surname') ) {
            if (!$form->hasValidData('Name') || $form->Name == '') {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator', 'Please enter a name');
            } else {
                $userData->name = $form->Name;
            }

            if ($form->hasValidData('Surname') && $form->Surname != '') {
                $userData->surname = $form->Surname;
            } else {
                $userData->surname = '';
            }
        }

        if (erLhcoreClassUser::instance()->hasAccessTo('lhuser', 'change_job_title') ) {
            if ( $form->hasValidData( 'JobTitle' ) && $form->JobTitle != '') {
                $userData->job_title = $form->JobTitle;
            } else {
                $userData->job_title = '';
            }
        }

		if ( $form->hasValidData( 'avatar' )) {
			$userData->avatar = $form->avatar;
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
				$userData->skype = mb_substr($form->Skype,0,50);
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

	public static function validateUsersImport(& $settings)
    {
        $Errors = array();

        $definition = array(
            'CSVSeparator' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'oneRecordImport' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'skipFirstRow' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'DefaultGroup' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',
                null,
                FILTER_REQUIRE_ARRAY
            ),
            'DepartmentGroup' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',
                null,
                FILTER_REQUIRE_ARRAY
            ),
            'DepartmentGroups' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',
                null,
                FILTER_REQUIRE_ARRAY
            ),
            'field' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',
                null,
                FILTER_REQUIRE_ARRAY
            )
        );

        $form = new ezcInputForm( INPUT_POST, $definition );

        if ( $form->hasValidData( 'CSVSeparator' )) {
            $settings['csv_separator'] = $form->CSVSeparator;
        } else {
            $settings['csv_separator'] = ',';
        }

        if ( $form->hasValidData( 'DefaultGroup' )) {
            $settings['user_groups_id'] = $form->DefaultGroup;
        } else {
            $settings['user_groups_id'] = array();
        }

        if ( $form->hasValidData( 'DepartmentGroup' )) {
            $settings['dep_id'] = $form->DepartmentGroup;
        } else {
            $settings['dep_id'] = array();
        }

        if ( $form->hasValidData( 'DepartmentGroups' )) {
            $settings['dep_group_id'] = $form->DepartmentGroups;
        } else {
            $settings['dep_group_id'] = array();
        }

        if ( $form->hasValidData( 'oneRecordImport' )) {
            $settings['import_one'] = 1;
        } else {
            $settings['import_one'] = 0;
        }

        if ( $form->hasValidData( 'skipFirstRow' )) {
            $settings['skip_first_row'] = 1;
        } else {
            $settings['skip_first_row'] = 0;
        }

        if ( $form->hasValidData( 'field' )) {
            $settings['field'] = $form->field;
        } else {
            $settings['field'] = array();
        }

        return $Errors;
    }

    public static function importUsers($configuration, $users) {

	    $status = array('created' => 0, 'updated' => 0);

        $Data = str_getcsv($users, "\n"); //parse the rows

        foreach($Data as $key => $Row) {

            if ($key == 0 && $configuration['skip_first_row'] == true) {
                continue;
            }

            $allDepartments = false;

            $RowData = str_getcsv($Row, $configuration['csv_separator']);

            if (isset($RowData[$configuration['field']['username']-1])) {
                $username = $RowData[$configuration['field']['username'] - 1];
            } else {
                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('user/import','Username field not found!'));
            }

            $user = erLhcoreClassModelUser::findOne(array('filter' => array('username' => $username)));

            if (!($user instanceof erLhcoreClassModelUser)){
                $user = new erLhcoreClassModelUser();
                $status['created']++;
            } else {
                $status['updated']++;
            }

            $maxActiveChats = null;

            foreach ($configuration['field'] as $attr => $index) {
                if (is_numeric($index) && $index > 0 && isset($RowData[$configuration['field'][$attr]-1])) {
                    if ($attr == 'password') {
                        $user->setPassword($RowData[$index - 1]);
                    } elseif ($attr == 'all_departments') {
                        $allDepartments = $RowData[$index-1] == 1;
                    } elseif ($attr == 'max_active_chats') {
                        $user->max_active_chats = $maxActiveChats = (int)$RowData[$index-1];
                    } elseif ($attr == 'show_all_pending') {
                        $show_all_pending = $RowData[$index-1] == 1;
                    } else {
                        $user->{$attr} = $RowData[$index-1];
                    }
                }
            }

            $user->saveThis();

            if (isset($show_all_pending)) {
                erLhcoreClassModelUserSetting::setSetting('show_all_pending', (int)$show_all_pending, $user->id);
            }

            if (count($configuration['dep_id']) > 0) {

                if ($allDepartments == 1) {
                    $configuration['dep_id'][] = 0;
                    $user->all_departments = 1;
                } else {
                    $user->all_departments = 0;
                }

                $user->departments_ids = implode(',',$configuration['dep_id']);
                $user->saveThis();

                erLhcoreClassUserDep::addUserDepartaments($configuration['dep_id'], $user->id, $user);
            } else {
                erLhcoreClassUserDep::addUserDepartaments(array(), $user->id, $user);
            }

            if (count($configuration['dep_group_id']) > 0) {
                erLhcoreClassModelDepartamentGroupUser::addUserDepartmentGroups($user, $configuration['dep_group_id']);
            }

            if (isset($maxActiveChats)) {
                // Update max active chats directly
                $db = ezcDbInstance::get();
                $stmt = $db->prepare('UPDATE lh_userdep SET max_chats = :max_chats WHERE user_id = :user_id');
                $stmt->bindValue(':max_chats', $user->max_active_chats, PDO::PARAM_INT);
                $stmt->bindValue(':user_id', $user->id, PDO::PARAM_INT);
                $stmt->execute();
            }

            if (count($configuration['user_groups_id']) > 0) {
                $user->user_groups_id = $configuration['user_groups_id'];
                $user->setUserGroups();
            }

            erLhcoreClassUserDep::setHideOnlineStatus($user);

            if ($configuration['import_one'] == true) {
                break;
            }

        }

        return $status;
    }

    public static function getDepartmentValidationParams($UserData, $selfEdit = false)
    {
        $departmentEditParams = [
            'self_edit' => $selfEdit,
            'all_departments' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','edit_all_departments'),
            'individual' => [
                'read_all' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','see_user_assigned_departments') || erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_all_department_individual'),
                'edit_all' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_all_department_individual'),
                'edit_personal' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_to_own_department_individual')
            ],
            'groups' => [
                'read_all' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','see_user_assigned_departments_groups') || erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_all_department_group'),
                'edit_all' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_all_department_group'),
                'edit_personal' => erLhcoreClassUser::instance()->hasAccessTo('lhuser','assign_to_own_department_group')
            ]
        ];

        if ($departmentEditParams['individual']['edit_all'] == false) {
            $departmentEditParams['individual']['id'] = array_merge(
                erLhcoreClassUserDep::getUserDepartamentsIndividual(
                    erLhcoreClassUser::instance()->getUserID()
                ),
                erLhcoreClassUserDep::getUserDepartamentsIndividual(
                    erLhcoreClassUser::instance()->getUserID(),
                    true
                )
            );

            $departmentEditParams['individual']['remote_id_read_all'] = $departmentEditParams['individual']['remote_id_write_all'] = [];

            if ($UserData->id > 0) {

                $departmentEditParams['individual']['remote_id_write_all'] = erLhcoreClassUserDep::getUserDepartamentsIndividual(
                    $UserData->id
                );

                $departmentEditParams['individual']['remote_id_read_all'] = erLhcoreClassUserDep::getUserDepartamentsIndividual(
                    $UserData->id,
                    true
                );
            }

            // Departments to whom edited operator has access to
            $departmentEditParams['individual']['remote_id_write'] = array_diff($departmentEditParams['individual']['remote_id_write_all'], $departmentEditParams['individual']['id']);

            $departmentEditParams['individual']['remote_id_read'] = array_diff($departmentEditParams['individual']['remote_id_read_all'], $departmentEditParams['individual']['id']);

        }

        if ($departmentEditParams['groups']['edit_all'] == false) {
            $departmentEditParams['groups']['id'] = array_merge(
                erLhcoreClassModelDepartamentGroupUser::getUserGroupsIds(
                    erLhcoreClassUser::instance()->getUserID()
                ),
                erLhcoreClassModelDepartamentGroupUser::getUserGroupsIds(
                    erLhcoreClassUser::instance()->getUserID(),
                    true
                )
            );

            $departmentEditParams['groups']['remote_id_write_all'] = $departmentEditParams['groups']['remote_id_read_all'] = [];

            if ($UserData->id > 0) {
                $departmentEditParams['groups']['remote_id_write_all'] = erLhcoreClassModelDepartamentGroupUser::getUserGroupsIds(
                    $UserData->id
                );

                $departmentEditParams['groups']['remote_id_read_all'] = erLhcoreClassModelDepartamentGroupUser::getUserGroupsIds(
                    $UserData->id,
                    true
                );

                $departmentEditParams['groups']['remote_id_auto_assign_all'] = erLhcoreClassModelDepartamentGroupUser::getUserGroupsExcAutoassignIds(
                    $UserData->id
                );
            }

            // Departments to whom edited operator has access to
            $departmentEditParams['groups']['remote_id_write'] = array_diff($departmentEditParams['groups']['remote_id_write_all'], $departmentEditParams['groups']['id']);

            $departmentEditParams['groups']['remote_id_read'] = array_diff($departmentEditParams['groups']['remote_id_read_all'], $departmentEditParams['groups']['id']);
        }

        return $departmentEditParams;
    }
}

?>