<?php

$tpl = erLhcoreClassTemplate::getInstance('lhuser/remindpassword.tpl.php');

$hash = $Params['user_parameters']['hash'];

if ($hash != '') {

    $hashData = erLhcoreClassModelForgotPassword::checkHash($hash);

    if ($hashData) {

        $UserData = erLhcoreClassUser::getSession()->load('erLhcoreClassModelUser', (int)$hashData['user_id']);

        if ($UserData) {

            $tpl->set('hash', $hashData['hash']);

            $passwordData = (array)erLhcoreClassModelChatConfig::fetch('password_data')->data;

            $tpl->set('minimum_length', 1);

            if (isset($passwordData['length']) && $passwordData['length'] > 0) {
                $tpl->set('minimum_length', $passwordData['length']);
            }

            foreach (['uppercase_required','number_required','special_required','lowercase_required'] as $passwordRequirement) {
                if (isset($passwordData[$passwordRequirement]) && $passwordData[$passwordRequirement] > 0) {
                    $tpl->set($passwordRequirement, $passwordData[$passwordRequirement]);
                }
            }

            if (isset($passwordData['generate_manually']) && $passwordData['generate_manually'] == 1) {

                $newPassword = erLhcoreClassUserValidator::generatePassword();

                $tpl->set('manual_password', true);
                $tpl->set('new_password', $newPassword);

                $UserData->setPassword($newPassword);
                erLhcoreClassUser::getSession()->update($UserData);

                erLhcoreClassModelForgotPassword::deleteHash($hashData['user_id']);

            } else {
                if (ezcInputForm::hasPostData()) {
                    $definition = array(
                        'Password1' => new ezcInputFormDefinitionElement(
                            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                        ),
                        'Password2' => new ezcInputFormDefinitionElement(
                            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                        )
                    );

                    $form = new ezcInputForm(INPUT_POST, $definition);

                    $Errors = array();

                    if (!$form->hasValidData('Password1') || !$form->hasValidData('Password2')) {
                        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator', 'Please enter a password!');
                    }

                    if ($form->hasValidData('Password1') && $form->hasValidData('Password2')) {
                        $UserData->password_temp_1 = $form->Password1;
                        $UserData->password_temp_2 = $form->Password2;
                    }

                    if ($form->hasValidData('Password1') && $form->hasValidData('Password2') && $form->Password1 != '' && $form->Password2 != '' && $form->Password1 != $form->Password2) {
                        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator', 'Passwords must match!');
                    }

                    if ($form->hasValidData('Password1') && $form->hasValidData('Password2') && $form->Password1 != '' && $form->Password2 != '') {
                        $UserData->setPassword($form->Password1);
                        $UserData->password_front = $form->Password2;
                        erLhcoreClassUserValidator::validatePassword($UserData, $Errors);
                    } else {
                        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/validator', 'Please enter a password!');
                    }

                    if (empty($Errors)) {
                        $tpl->set('account_updated', true);

                        $UserData->setPassword($UserData->password_front);
                        erLhcoreClassUser::getSession()->update($UserData);

                        erLhcoreClassModelForgotPassword::deleteHash($hashData['user_id']);
                    } else {
                        $tpl->set('errors', $Errors);
                    }
                }
            }
        }
    }
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'login';

?>