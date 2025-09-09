<?php

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

if ( erLhcoreClassChat::hasAccessToRead($chat) )
{
    $command = erLhcoreClassModelGenericBotCommand::fetch($Params['user_parameters']['command_id']);

    $tpl = new erLhcoreClassTemplate( 'lhchatcommand/command.tpl.php');
    $tpl->set('chat', $chat);
    $tpl->set('command', $command);

    if ( ezcInputForm::hasPostData() ) {

        $validationFields = array();

        foreach ($command->fields_array as $fieldIndex => $field) {
            $validationFields['field_' . $fieldIndex] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw');
        }

        $form = new ezcInputForm(INPUT_POST, $validationFields);
        $Errors = array();

        $commandArguments = [];

        foreach ($command->fields_array as $fieldIndex => $field) {
            if ($form->hasValidData('field_' . $fieldIndex) && $form->{'field_' . $fieldIndex} != '') {
                $commandArguments['field_' . $fieldIndex] = $form->{'field_' . $fieldIndex};
            } elseif ((isset($field['required']) && $field['required'] == 'required') || !isset($field['required'])) {
                $Errors['field_' . $fieldIndex] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat', 'Field is required'). ' &quot;' . htmlspecialchars($field['name']) . '&quot;';
            } else {
                $commandArguments['field_' . $fieldIndex] = '';
            }
        }

        if (empty($Errors)) {

            $commandArgumentsWithNames = $commandArguments;

            foreach ($command->fields_array as $fieldIndex => $field) {
                $commandArgumentsWithNames['field_' . $fieldIndex] .= '|||' . $field['name'];
            }

            $tpl->set('commandExecution', '!' . $command->command . ' ' . implode(' --arg ',$commandArgumentsWithNames));
        } else {
            $tpl->set('errors', $Errors);
        }

        $tpl->set('commandArguments', $commandArguments);

    }

    echo $tpl->fetch();
}

exit;

?>