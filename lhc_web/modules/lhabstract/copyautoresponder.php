<?php

$autoresponder = erLhAbstractModelAutoResponder::fetch($Params['user_parameters']['id']);

$tpl = erLhcoreClassTemplate::getInstance('lhabstract/copyautoresponder.tpl.php');

if (isset($_POST['CopyAction'])) {

    $definition = array(
        'CopyDepartments' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', null, FILTER_REQUIRE_ARRAY
        )
    );
    
    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();
    
    if ( !$form->hasValidData( 'CopyDepartments' ) || empty($form->CopyDepartments))
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/copyautoresponder','Please choose atleast one department!');
    } else {
        $copyTo = $form->CopyDepartments;
    }
    
  	if (count($Errors) == 0) {
  	    
  	    foreach ($copyTo as $depId) {
  	        if ($depId != $autoresponder->dep_id) {
  	            $autoresponderNew = clone $autoresponder;
  	            $autoresponderNew->dep_id = $depId;
  	            $autoresponderNew->id = null;
  	            $autoresponderNew->saveThis();
  	        }
  	    }
  	    
  		$tpl->set('message_saved',true);
  	} else {
  		$tpl->set('errors',$Errors);
  	}
}

$tpl->set('autoresponder',$autoresponder);

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'popup';

?>