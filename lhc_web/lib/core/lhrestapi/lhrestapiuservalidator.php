<?php

class erLhcoreClassRestAPIUserValidator
{
    public static function validateUser(erLhcoreClassModelUser $userData, $params = [])
    {
        $definition = array (
            'password' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'email' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'validate_email'
            ),
            'name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'surname' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'chat_nickname' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'username' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'disabled' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'hide_online' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'invisible_mode' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'rec_per_req' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'always_on' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'auto_accept' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'exclude_autoasign' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'job_title' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'time_zone' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'user_groups' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',
                null,
                FILTER_REQUIRE_ARRAY
            ),
            'departments' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',
                null,
                FILTER_REQUIRE_ARRAY
            ),
            'departments_read' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',
                null,
                FILTER_REQUIRE_ARRAY
            ),
            'department_groups' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',
                null,
                FILTER_REQUIRE_ARRAY
            ),
            'attr_int_1' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'all_departments' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'attr_int_2' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'attr_int_3' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'max_active_chats' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'skype' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'xmpp_username' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            )
        );

        $form = new erLhcoreClassInputForm(INPUT_GET, $definition, null, $params['payload_data']);

        $Errors = array();

        if ($userData->id == null) {

            if ( !$form->hasValidData( 'username' ) || $form->username == '') {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Please enter a username');
            } else {

                $userData->username = $form->username;

                if (erLhcoreClassModelUser::userExists($userData->username) === true) {
                    $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','User exists');
                }
            }

            if ( $form->hasValidData( 'password' )) {
                $userData->password_temp_1 = $form->password;
             }

            if ( !$form->hasValidData( 'password' ) || $form->password == '') {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Password missing');
            } else {
                $userData->setPassword($form->password);
                $userData->password_front = $form->password;
            }

        } else {
            if ((isset($params['can_edit_groups']) && $params['can_edit_groups'] == true) || !isset($params['can_edit_groups'])) {

                if ($form->hasValidData('username'))
                {
                    if ($form->username == '') {
                        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator', 'Please enter a username');
                    } else {

                        if ($form->username != $userData->username) {

                            $userData->username = $form->username;

                            if (erLhcoreClassModelUser::userExists($userData->username) === true) {
                                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator', 'User exists');
                            }
                        }
                    }
                }

                if ($form->hasInputField('password') && $form->hasValidData('password')) {
                    $userData->password_temp_1 = $form->password;
                }

                if ($form->hasInputField('password') && $form->hasValidData('password') && $form->password != '') {
                    $userData->setPassword($form->password);
                    $userData->password_front = $form->password;
                }

            }
        }

        erLhcoreClassUserValidator::validatePassword($userData, $Errors);

        if ( $form->hasValidData( 'chat_nickname' )) {
            $userData->chat_nickname = $form->chat_nickname;
        }

        if ( $form->hasValidData( 'attr_int_1' )) {
            $userData->attr_int_1 = $form->attr_int_1;
        }

        if ( $form->hasValidData( 'max_active_chats' )) {
            $userData->max_active_chats = $form->max_active_chats;
        }

        if ( $form->hasValidData( 'attr_int_2' )) {
            $userData->attr_int_2 = $form->attr_int_2;
        }

        if ( $form->hasValidData( 'attr_int_3' )) {
            $userData->attr_int_3 = $form->attr_int_3;
        }

        if ( !$form->hasValidData( 'email' ) ) {
            if ($userData->id == null) {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Wrong email address');
            }
        } else {
            $userData->email = $form->email;
        }

        if ( !$form->hasValidData( 'name' ) || $form->name == '' ) {
            if ($userData->id == null) {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Please enter a name');
            }
        } else {
            if ($form->hasValidData( 'name' )) {
                $userData->name = $form->name;
            }
        }

        if ( $form->hasValidData( 'surname' )) {
            $userData->surname = $form->surname;
        }

        if ( $form->hasValidData( 'job_title' )) {
            $userData->job_title = $form->job_title;
        }

        if ( $form->hasValidData( 'time_zone' )) {
            $userData->time_zone = $form->time_zone;
        }

        if ( $form->hasValidData( 'skype' )) {
            $userData->skype = $form->skype;
        }

        if ( $form->hasValidData( 'xmpp_username' )) {
            $userData->xmpp_username = $form->xmpp_username;
        }

        if ( $form->hasValidData( 'hide_online' )) {
            $userData->hide_online = $form->hide_online == true ? 1 : 0;
        }

        if ( $form->hasValidData( 'all_departments' )) {
            $userData->all_departments = $form->all_departments == true ? 1 : 0;
            if (!$form->hasValidData('departments') || !$form->hasValidData('departments_read')) {
                $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','In order to assign user all departments you have to provide  `departments` and `departments_read` argument.');
            }
        }

        if ( $form->hasValidData( 'auto_accept' ) && $form->auto_accept == true )	{
            $userData->auto_accept = $form->auto_accept == true ? 1 : 0;
        }

        if ( $form->hasValidData( 'invisible_mode' ) ) {
            $userData->invisible_mode = $form->invisible_mode == true ? 1 : 0;
        }

        if ( $form->hasValidData( 'always_on' ) && $form->always_on == true ) {
            $userData->always_on = $form->always_on == true ? 1 : 0;
        }

        if ( $form->hasValidData( 'exclude_autoasign' )) {
            $userData->exclude_autoasign = $form->exclude_autoasign == true ? 1 : 0;;
        }

        if ( $form->hasValidData( 'rec_per_req' ) && $form->rec_per_req == true ) {
            $userData->rec_per_req = $form->rec_per_req == true ? 1 : 0;
        }

        if ((isset($params['can_edit_groups']) && $params['can_edit_groups'] == true) || !isset($params['can_edit_groups'])) {

            if ( $form->hasValidData( 'disabled' )) {
                $userData->disabled = $form->disabled == true ? 1 : 0;
            }

            if ( $form->hasValidData( 'user_groups' ) ) {

                if ($params['groups_can_edit'] === true) {
                    $userData->user_groups_id = $form->user_groups;

                    $groupsRequired = erLhcoreClassModelGroup::getList(array('filter' => array('required' => 1)));

                    if (!empty($groupsRequired)) {
                        $diff = array_diff(array_keys($groupsRequired), $userData->user_groups_id);

                        if (count($diff) == count($groupsRequired)) {
                            $Errors['group_required'] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','You have to choose one of required groups!');
                        }
                    }

                } else {

                    $groupsMustChecked = array_intersect($userData->user_groups_id,$params['groups_can_read']);

                    $unknownGroups = array_diff($form->user_groups, $params['groups_can_edit']);

                    if (empty($unknownGroups)) {
                        $userData->user_groups_id = $form->user_groups;
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
                if ($userData->id == null) {
                    $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator','Please choose a default user group');
                }
            }
        }

        if ($form->hasInputField('department_groups') && $form->hasValidData('department_groups')) {
            $userData->department_groups = $form->department_groups;
        }

        if ($userData->id == null)
        {
            $userData->pswd_updated = time();
        }

        if ($form->hasInputField('departments') && $form->hasValidData('departments'))
        {
            $globalDepartament = array();

            if ($userData->all_departments == 1) {
                $globalDepartament[] = 0;
            }

            if ($form->hasInputField('departments') && $form->hasValidData('departments')) {
                $globalDepartament = array_merge($form->departments, $globalDepartament);
            }

            $userData->departments_ids_read_array = [];

            if ($form->hasInputField('departments_read') && $form->hasValidData('departments_read')) {
                $globalDepartament = array_merge($form->departments_read, $globalDepartament);
                $userData->departments_ids_read_array = $form->departments_read;
            }

            $globalDepartament = array_unique($globalDepartament);

            $userData->departments_ids = implode(',', $globalDepartament);
            $userData->departments_ids_array = $globalDepartament;
        }

        return $Errors;
    }

    public static function validateOperatorPhotoPayload(& $userData, $params = array()) {
        $Errors = false;

        if (isset($params["payload"]['image']) && !empty($params["payload"]['image'])) {

            $imgDataItem = base64_decode($params["payload"]['image']);

            $imagesize = getimagesizefromstring($imgDataItem);

            if ($imagesize !== false && isset($imagesize['mime'])){
                $mimetype = $imagesize['mime'];
            } elseif(class_exists('finfo')) {
                $finfo    = new finfo(FILEINFO_MIME);
                $mimetype = $finfo->buffer($imgDataItem);
            }

            $extensionMimetype = array(
                'image/jpeg' => 'jpg',
                'image/jpg' => 'jpg',
                'image/png' => 'png'
            );

            if (!key_exists($mimetype,$extensionMimetype)){
                return;
            }

            $dir = 'var/tmpfiles/';
            $fileName = 'data' . '.' . $extensionMimetype[$mimetype];

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('theme.temppath',array('dir' => & $dir));

            erLhcoreClassFileUpload::mkdirRecursive( $dir );

            $imgPath = $dir . $fileName;
            file_put_contents($imgPath, $imgDataItem);

            if (erLhcoreClassImageConverter::isPhotoLocal($imgPath)) {

                $Errors = array();

                $path = isset($params['path']) ? $params['path'] : 'var/userphoto/';

                $dir = $path . date('Y') . 'y/' . date('m') . '/' . date('d') .'/' . $userData->id . '/';

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.edit.photo_path',array('dir' => & $dir, 'storage_id' => $userData->id));

                erLhcoreClassFileUpload::mkdirRecursive( $dir );

                $fileName = erLhcoreClassSearchHandler::moveLocalFile($imgPath, $dir . '/','.' );

                if ( !empty($file["errors"]) ) {
                    foreach ($file["errors"] as $err) {
                        $Errors[] = $err;
                    }
                } else {
                    $userData->removeFile();
                    $userData->filename	= $fileName;
                    $userData->filepath	= $dir;

                    $response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.edit.photo_resize_150', array('mime_type' => $extensionMimetype[$mimetype], 'user' => $userData));

                    if ($response === false) {
                        erLhcoreClassImageConverter::getInstance()->converter->transform( 'photow_150', $userData->file_path_server, $userData->file_path_server);
                        chmod($userData->file_path_server, 0644);
                    }
                }
            } else {
                unlink($imgPath);
            }
        }

        return $Errors;
    }


    public static function formatAPI(erLhcoreClassModelUser $user)
    {
        return get_object_vars($user);
    }
}

?>
