<?php 

$tpl = erLhcoreClassTemplate::getInstance('lhspeech/setchatspeechlanguage.tpl.php');

if (is_numeric($Params['user_parameters']['chat_id']))
{
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
    if ( erLhcoreClassChat::hasAccessToRead($chat) )
    {
         $tpl->set('chat',$chat);          
         $chatSpeech = erLhcoreClassSpeech::getSpeechInstance($chat);
        
         // Customer want's to speak other language than default
         if (ezcInputForm::hasPostData()) {
             
             $currentUser = erLhcoreClassUser::instance();
             
             if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
                 echo json_encode(array('error' => 'true', 'result' => 'Invalid CSRF Token' ));
                 exit;
             }
             
             $definition = array(
                 'select_language' => new ezcInputFormDefinitionElement(
                     ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1)
                 ),
                 'select_dialect' => new ezcInputFormDefinitionElement(
                     ezcInputFormDefinitionElement::OPTIONAL, 'string'
                 ),
             );
             
             $form = new ezcInputForm( INPUT_POST, $definition );
             
             if ( $form->hasValidData( 'select_language' ) ) {
                 $chatSpeech->language_id = $form->select_language;
             }
             
             if ( $form->hasValidData( 'select_dialect' ) )	{
                 $chatSpeech->dialect = $form->select_dialect;
             }
             
             $chatSpeech->saveThis();
             echo json_encode(array('error' => 'false', 'dialect' => $chatSpeech->dialect));
             exit;
         }
         
         $tpl->set('chat_speech',$chatSpeech);
         
    } else {
         $tpl->setFile('lhchat/errors/adminchatnopermission.tpl.php');
    }
}

echo $tpl->fetch();
exit;


?>