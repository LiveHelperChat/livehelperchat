<?php

class erLhcoreClassGenericBot {

    public static function getSession()
    {
        if ( !isset( self::$persistentSession ) )
        {
            self::$persistentSession = new ezcPersistentSession(
                ezcDbInstance::get(),
                new ezcPersistentCodeManager( './pos/lhgenericbot' )
            );
        }
        return self::$persistentSession;
    }

    public static function validateBot(& $bot, $additionalParams = array()) {

        $definition = array(
            'name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'short_name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'nick' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'avatar' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_1' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_2' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'attr_str_3' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'exc_group_id' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',null, FILTER_REQUIRE_ARRAY
            ),
            'bot_id' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',null, FILTER_REQUIRE_ARRAY
            ),
            'profile_hide' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'msg_hide' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'ign_btn_clk' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'configuration' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            )
        );

        if (isset($additionalParams['payload_data'])) {
            $form = new erLhcoreClassInputForm(INPUT_GET, $definition, null, $additionalParams['payload_data']);
        } else {
            $form = new ezcInputForm( INPUT_POST, $definition );
        }
        
        $Errors = array();

        if ( !$form->hasValidData( 'name' ) || $form->name == '' ) {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter bot name!');
        } else {
            $bot->name = $form->name;
        }

        if ($form->hasValidData('short_name')) {
            $bot->short_name = $form->short_name;
        } else {
            $bot->short_name = '';
        }

        if ( !$form->hasValidData( 'nick' ) || $form->nick == '' ) {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter bot nick!');
        } else {
            $bot->nick = $form->nick;
        }

        if ( $form->hasValidData( 'avatar' )) {
            $bot->avatar = $form->avatar;
        }

        if ( $form->hasValidData( 'attr_str_1' ) ) {
            $bot->attr_str_1 = $form->attr_str_1;
        } else {
            $bot->attr_str_1 = '';
        }

        if ( $form->hasValidData( 'attr_str_2' ) ) {
            $bot->attr_str_2 = $form->attr_str_2;
        } else {
            $bot->attr_str_2 = '';
        }

        if ( $form->hasValidData( 'attr_str_3' ) ) {
            $bot->attr_str_3 = $form->attr_str_3;
        } else {
            $bot->attr_str_3 = '';
        }

        $configurationArray = $bot->configuration_array;

        if ( $form->hasValidData( 'exc_group_id' ) && !empty($form->exc_group_id)) {
            $configurationArray['exc_group_id'] = $form->exc_group_id;
        } else {
            $configurationArray['exc_group_id'] = array();
        }

        if ( $form->hasValidData( 'bot_id' ) && !empty($form->bot_id)) {
            $configurationArray['bot_id'] = $form->bot_id;
        } else {
            $configurationArray['bot_id'] = array();
        }

        if ( $form->hasValidData( 'profile_hide' ) ) {
            $configurationArray['profile_hide'] = true;
        } else {
            $configurationArray['profile_hide'] = false;
        }

        if ( $form->hasValidData( 'msg_hide' ) ) {
            $configurationArray['msg_hide'] = true;
        } else {
            $configurationArray['msg_hide'] = false;
        }

        if ( $form->hasValidData( 'ign_btn_clk' ) ) {
            $configurationArray['ign_btn_clk'] = true;
        } elseif (isset($configurationArray['ign_btn_clk'])) {
            unset($configurationArray['ign_btn_clk']);
        }

        if ( $form->hasInputField( 'configuration' ) && $form->hasValidData('configuration') ) {
            $configurationUpdate = json_decode($form->configuration,true);
            if (is_array($configurationUpdate)) {
                $configurationArray = array_merge($configurationArray, $configurationUpdate);
            }
        }

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('bot.validate',array('bot' => & $bot, 'configuration_array' => & $configurationArray, 'additional_params' => $additionalParams));
        
        $bot->configuration_array = $configurationArray;
        $bot->configuration = json_encode($configurationArray);

        return $Errors;
    }

    public static function validateBotPhotoPayload(& $userData, $params = array()) {
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

                $path = isset($params['path']) ? $params['path'] : 'var/botphoto/';

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

    public static function validateBotPhoto(& $userData, $params = array()) {

        $Errors = false;

        if ( isset($_FILES["UserPhoto"]) && is_uploaded_file($_FILES["UserPhoto"]["tmp_name"]) && $_FILES["UserPhoto"]["error"] == 0 && erLhcoreClassImageConverter::isPhoto('UserPhoto') ) {

            $Errors = array();

            $path = isset($params['path']) ? $params['path'] : 'var/botphoto/';

            $dir = $path . date('Y') . 'y/' . date('m') . '/' . date('d') .'/' . $userData->id . '/';

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.edit.photo_path',array('dir' => & $dir, 'storage_id' => $userData->id));

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

    public static function validateBotRestAPI(& $botRestAPI)
    {
        $definition = array(
            'name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'description' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'configuration' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( !$form->hasValidData( 'name' ) || $form->name == '' ) {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter Rest API Name!');
        } else {
            $botRestAPI->name = $form->name;
        }

        if ( $form->hasValidData( 'description' )  ) {
            $botRestAPI->description = $form->description;
        } else {
            $botRestAPI->description = '';
        }
        
        if ( $form->hasValidData( 'configuration' )  ) {
            $botRestAPI->configuration = preg_replace('/(,?)\"\$\$hashKey\":\"object:([0-9]+)\"/','',$form->configuration);
        } else {
            $botRestAPI->configuration = '';
        }

        return $Errors;
    }

    public static function validateBotTranslationGroup(& $botTranslation)
    {
        $definition = array(
            'name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'Nick' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'bot_lang' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'use_translation_service' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( !$form->hasValidData( 'name' ) || $form->name == '' ) {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter translation group name!');
        } else {
            $botTranslation->name = $form->name;
        }

        if ( $form->hasValidData( 'Nick' )  ) {
            $botTranslation->nick = $form->Nick;
        } else {
            $botTranslation->nick = '';
        }

        if ( $form->hasValidData( 'bot_lang' )  ) {
            $botTranslation->bot_lang = $form->bot_lang;
        } else {
            $botTranslation->bot_lang = '';
        }

        if ( $form->hasValidData( 'use_translation_service' ) && $form->use_translation_service == true ) {
            $botTranslation->use_translation_service = 1;
        } else {
            $botTranslation->use_translation_service = 0;
        }

        return $Errors;
    }

    public static function validateBotTranslationItem(& $botTranslationItem)
    {
        $definition = array(
            'identifier' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'),
            'translation' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',null,FILTER_REQUIRE_ARRAY ),
            'default_message' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' ),
            'message_item' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',null,FILTER_REQUIRE_ARRAY ),
            'languages' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',null,FILTER_REQUIRE_ARRAY),
            'group_id' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)),
            'auto_translate' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean')
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( !$form->hasValidData( 'identifier' ) || $form->identifier == '' ) {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter translation group name!');
        } else {
            $botTranslationItem->identifier = $form->identifier;
        }

        $data = array('default' => '', 'items' => array());

        $languagesData = array();
        if ( $form->hasValidData( 'languages' ) && !empty($form->languages) )
        {
            foreach ($form->languages as $index => $languages) {
                $languagesData[] = array(
                    'languages' => $form->languages[$index],
                    'message' => $form->message_item[$index]
                );
            }
        }

        $data['items'] = $languagesData;

        if ( $form->hasValidData( 'default_message' ) ) {
            $data['default'] = $form->default_message;
        }

        $botTranslationItem->translation_array = $data;
        $botTranslationItem->translation = json_encode($data);

        if ( $form->hasValidData( 'group_id' ) ) {
            $botTranslationItem->group_id = $form->group_id;
        } else {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please choose a group!');
        }

        if ( $form->hasValidData( 'auto_translate' ) ) {
            $botTranslationItem->auto_translate = 1;
        } else {
            $botTranslationItem->auto_translate = 0;
        }

        return $Errors;
    }

    public static function validateBotException(& $botException)
    {
        $definition = array(
            'name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'priority' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'active' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            ),
            'code' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',null, FILTER_REQUIRE_ARRAY
            ),
            'message' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',null, FILTER_REQUIRE_ARRAY
            )
        );
        
        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( !$form->hasValidData( 'name' ) || $form->name == '' ) {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter exception group name!');
        } else {
            $botException->name = $form->name;
        }

        if ( $form->hasValidData( 'priority' ) ) {
            $botException->priority = $form->priority;
        } else {
            $botException->priority = 0;
        }

        if ( $form->hasValidData( 'active' ) && $form->active == true) {
            $botException->active = 1;
        } else {
            $botException->active = 0;
        }

        if ( $form->hasValidData( 'code' ) && is_array($form->code)) {
            foreach ($form->code as $index => $code) {
                $exceptionMessage = erLhcoreClassModelGenericBotExceptionMessage::findOne(array('filter' => array('code' => $code, 'exception_group_id' => $botException->id)));

                if (!($exceptionMessage instanceof erLhcoreClassModelGenericBotExceptionMessage)) {
                    $exceptionMessage = new erLhcoreClassModelGenericBotExceptionMessage();
                    $exceptionMessage->code = $code;
                }

                $exceptionMessage->message = $form->message[$index];
                $exceptionMessage->active = $botException->active;
                $exceptionMessage->priority = $botException->priority;

                $botException->exceptions[] = $exceptionMessage;
            }
        }

        return $Errors;
    }

    public static function validateBotCommand(& $botCommand)
    {
        $definition = array(
            'command' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'string'
            ),
            'name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'string'
            ),
            'sub_command' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'info_msg' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'shortcut_1' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'shortcut_2' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'enabled_display' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,'boolean'
            ),
            'position' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,'int', array('min_range' => 0)
            ),
            'bot_id' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,'int', array('min_range' => 1)
            ),
            'AbstractInput_trigger_id' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
            ),
            'dep_id' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
            ),
            // Custom arguments fields
            'custom_field_name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
            ),
            'custom_field_placeholder' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
            ),
            'custom_field_type' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
            ),
            'custom_field_required' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
            ),
            'custom_field_rows' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1),FILTER_REQUIRE_ARRAY
            )
        );
        
        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( !$form->hasValidData( 'command' ) || $form->command == '' ) {
            $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter a command!');
        } else {
            $botCommand->command = $form->command;
        }

        if ( $form->hasValidData( 'sub_command' )) {
            $botCommand->sub_command = $form->sub_command;
        } else {
            $botCommand->sub_command = '';
        }

        if ( $form->hasValidData( 'enabled_display' ) && $form->enabled_display === true) {
            $botCommand->enabled_display = 1;
        } else {
            $botCommand->enabled_display = 0;
        }

        if ($form->hasValidData( 'position' )) {
            $botCommand->position = $form->position;
        } else {
            $botCommand->position = 0;
        }

        if ( $form->hasValidData( 'info_msg' )) {
            $botCommand->info_msg = $form->info_msg;
        } else {
            $botCommand->info_msg = '';
        }

        if ( $form->hasValidData( 'name' )) {
            $botCommand->name = $form->name;
        } else {
            $botCommand->name = '';
        }

        if ( $form->hasValidData( 'shortcut_1' ) ) {
            $botCommand->shortcut_1 = $form->shortcut_1;
        } else {
            $botCommand->shortcut_1 = '';
        }

        if ( $form->hasValidData( 'shortcut_2' ) ) {
            $botCommand->shortcut_2 = $form->shortcut_2;
        } else {
            $botCommand->shortcut_2 = '';
        }

        if ( $form->hasValidData( 'bot_id' ) ) {
            $botCommand->bot_id = $form->bot_id;
        } else {
            $botCommand->bot_id = 0;
        }

        if ( $form->hasValidData( 'AbstractInput_trigger_id' ) ) {
            $botCommand->trigger_id = $form->AbstractInput_trigger_id;
        } else {
            $botCommand->trigger_id = 0;
        }

        if ( $form->hasValidData( 'dep_id' ) ) {
            $botCommand->dep_id = $form->dep_id;
        } else {
            $botCommand->dep_id = 0;
        }

        if ( $form->hasValidData( 'custom_field_name' )) {
            $fields = [];
            foreach ($form->custom_field_name as $index => $fieldName) {
                $fields[] = [
                    'name' => $fieldName,
                    'placeholder' => isset($form->custom_field_placeholder[$index]) ? $form->custom_field_placeholder[$index] : null,
                    'rows' => isset($form->custom_field_rows[$index]) ? $form->custom_field_rows[$index] : null,
                    'type' => isset($form->custom_field_type[$index]) ? $form->custom_field_type[$index] : null,
                    'required' => isset($form->custom_field_required[$index]) ? $form->custom_field_required[$index] : null
                ];
            }
            $botCommand->fields = json_encode($fields);
        } else {
            $botCommand->fields = '';
        }

        return $Errors;
    }

    private static $persistentSession;
}

?>