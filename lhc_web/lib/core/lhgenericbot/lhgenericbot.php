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

    public static function validateBot(& $bot) {

        $definition = array(
            'Name' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'Nick' => new ezcInputFormDefinitionElement(
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
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( !$form->hasValidData( 'Name' ) || $form->Name == '' ) {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter bot name!');
        } else {
            $bot->name = $form->Name;
        }

        if ( !$form->hasValidData( 'Nick' ) || $form->Nick == '' ) {
            $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter bot nick!');
        } else {
            $bot->nick = $form->Nick;
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

        $bot->configuration_array = $configurationArray;
        $bot->configuration = json_encode($configurationArray);

        return $Errors;
    }

    public static function validateBotPhoto(& $userData, $params = array()) {

        $Errors = false;

        if ( isset($_FILES["UserPhoto"]) && is_uploaded_file($_FILES["UserPhoto"]["tmp_name"]) && $_FILES["UserPhoto"]["error"] == 0 && erLhcoreClassImageConverter::isPhoto('UserPhoto') ) {

            $Errors = array();

            $dir = 'var/botphoto/' . date('Y') . 'y/' . date('m') . '/' . date('d') .'/' . $userData->id . '/';

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
    private static $persistentSession;
}

?>