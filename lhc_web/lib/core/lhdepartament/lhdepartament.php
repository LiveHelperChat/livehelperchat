<?php

class erLhcoreClassDepartament{

   function __construct()
   {

   }

   public static function getDepartaments()
   {
         $db = ezcDbInstance::get();

         $stmt = $db->prepare('SELECT * FROM lh_departament ORDER BY id ASC');
         $stmt->execute();
         $rows = $stmt->fetchAll();

         return $rows;
   }

   public static function sortByStatus($departments) {

	   	$onlineDep = array();
	   	$offlineDep = array();

	   	foreach ($departments as $dep) {
	   		if ($dep->is_online === true){
	   			$onlineDep[] = $dep;
	   		} else {
	   			$offlineDep[] = $dep;
	   		}
	   	}

	   	return array_merge($onlineDep,$offlineDep);
   }

   public static function validateDepartment(erLhcoreClassModelDepartament & $department) {
   	
	   	$definition = array(
	   			'Name' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
	   			),
	   			'Email' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
	   			),
	   			'XMPPRecipients' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
	   			),
	   			'XMPPRecipientsGroup' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
	   			),
	   			'Identifier' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'string'
	   			),
	   			'Priority' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'int'
	   			),
	   			'TansferDepartmentID' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1)
	   			),
	   			'TransferTimeout' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 5)
	   			),	   			
	   			'mod' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	   			),
	   			'tud' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	   			),
	   			'wed' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	   			),
	   			'thd' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	   			),
	   			'frd' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	   			),
	   			'sad' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	   			),
	   			'sud' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	   			),
	   			'OnlineHoursActive' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	   			),
	   			'Disabled' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	   			),
	   			'inform_close' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	   			),
	   			'StartHour' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 0, 'mx_range' => 24)
	   			),
	   			'EndHour' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 0, 'mx_range' => 24)
	   			),
	   			'inform_delay' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 0)
	   			),
	   			'inform_options' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'string', null, FILTER_REQUIRE_ARRAY
	   			),
	   	);
		   	
	   	
	   	$form = new ezcInputForm( INPUT_POST, $definition );
	   	$Errors = array();
	   	
	   	if ( !$form->hasValidData( 'Name' ) || $form->Name == '' )
	   	{
	   		$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter a department name');
	   	} else {
	   		$department->name = $form->Name;
	   	}
	   	
	   	if ( $form->hasValidData( 'TansferDepartmentID' ) )
	   	{
	   		$department->department_transfer_id = $form->TansferDepartmentID;
	   	} else {
	   		$department->department_transfer_id = 0;
	   	}
	   	
	   	if ( $form->hasValidData( 'Identifier' ) )
	   	{
	   		$department->identifier = $form->Identifier;
	   	}
	   	
	   	if ( $form->hasValidData( 'TransferTimeout' ) )
	   	{
	   		$department->transfer_timeout = $form->TransferTimeout;
	   	} else {
	   		$department->transfer_timeout = 0;
	   	}
	   	
	   	if ( $form->hasValidData( 'Email' ) ) {	   	
	   		$partsEmail = explode(',', $form->Email);
	   		$validatedEmail = array();
	   		foreach ($partsEmail as $email){
	   			if (filter_var(trim($email), FILTER_VALIDATE_EMAIL)){
	   				$validatedEmail[] = trim($email);
	   			}
	   		}	   	
	   		$department->email = implode(',', $validatedEmail);	   	
	   	} else {
	   		$department->email = '';
	   	}
	   	
	   	if ( $form->hasValidData( 'XMPPRecipients' ) ) {	   	
	   		$department->xmpp_recipients = $form->XMPPRecipients;	   			   	
	   	} else {
	   		$department->xmpp_recipients = '';
	   	}
	   	
	   	if ( $form->hasValidData( 'XMPPRecipientsGroup' ) ) {	   	
	   		$department->xmpp_group_recipients = $form->XMPPRecipientsGroup;	   			   	
	   	} else {
	   		$department->xmpp_group_recipients = '';
	   	}
	   	
	   	if ( $form->hasValidData( 'Priority' ) ) {
	   		$department->priority = $form->Priority;
	   	} else {
	   		$department->priority = 0;
	   	}
	   		   	
	   	if ( $form->hasValidData( 'inform_close' ) && $form->inform_close === true ) {
	   		$department->inform_close = 1;
	   	} else {
	   		$department->inform_close = 0;
	   	}
	   		   	
	   	if ( $form->hasValidData( 'Disabled' ) && $form->Disabled === true ) {
	   		$department->disabled = 1;
	   	} else {
	   		$department->disabled = 0;
	   	}
	   		   	
	   	if ( $form->hasValidData( 'mod' ) && $form->mod === true ) {
	   		$department->mod = 1;
	   	} else {
	   		$department->mod = 0;
	   	}
	   		   	
	   	if ( $form->hasValidData( 'tud' ) && $form->tud === true ) {
	   		$department->tud = 1;
	   	} else {
	   		$department->tud = 0;
	   	}
	   		   	
	   	if ( $form->hasValidData( 'wed' ) && $form->wed === true ) {
	   		$department->wed = 1;
	   	} else {
	   		$department->wed = 0;
	   	}
	   		   	
	   	if ( $form->hasValidData( 'thd' ) && $form->thd === true ) {
	   		$department->thd = 1;
	   	} else {
	   		$department->thd = 0;
	   	}
	   		   	
	   	if ( $form->hasValidData( 'frd' ) && $form->frd === true ) {
	   		$department->frd = 1;
	   	} else {
	   		$department->frd = 0;
	   	}
	   		   	
	   	if ( $form->hasValidData( 'sad' ) && $form->sad === true ) {
	   		$department->sad = 1;
	   	} else {
	   		$department->sad = 0;
	   	}
	   		   	
	   	if ( $form->hasValidData( 'sud' ) && $form->sud === true ) {
	   		$department->sud = 1;
	   	} else {
	   		$department->sud = 0;
	   	}
	   		   	
	   	if ( $form->hasValidData( 'OnlineHoursActive' ) && $form->OnlineHoursActive === true ) {
	   		$department->online_hours_active = 1;
	   	} else {
	   		$department->online_hours_active = 0;
	   	}
	   		   	
	   	if ( $form->hasValidData( 'inform_options' ) ) {
	   		$department->inform_options = serialize($form->inform_options);
	   		$department->inform_options_array = $form->inform_options;
	   	} else {
	   		$department->inform_options = serialize(array());
	   	}
	   		   	
	   	if ( $form->hasValidData( 'StartHour' ) ) {
	   		$department->start_hour = $form->StartHour;
	   	} else {
	   		$department->start_hour = 0;
	   	}
	   		   	
	   	if ( $form->hasValidData( 'EndHour' ) ) {
	   		$department->end_hour = $form->EndHour;
	   	} else {
	   		$department->end_hour = 0;
	   	}
	   	
	   	if ( $form->hasValidData( 'inform_delay' )  ) {
	   		$department->inform_delay = $form->inform_delay;
	   	} else {
	   		$department->inform_delay = 0;
	   	}
	   	
	   	if ($department->id > 0 && $department->department_transfer_id == $department->id) {
	   		$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Transfer department has to be different one than self');
	   	}
	   	
	   	return $Errors;
   	
   }
   
   public static function getSession()
   {
        if ( !isset( self::$persistentSession ) )
        {
            self::$persistentSession = new ezcPersistentSession(
                ezcDbInstance::get(),
                new ezcPersistentCodeManager( './pos/lhdepartament' )
            );
        }
        return self::$persistentSession;
   }

   private static $persistentSession;

}


?>