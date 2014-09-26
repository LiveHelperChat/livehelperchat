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
	   			'delay_lm' => new ezcInputFormDefinitionElement(
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
	   			'Hidden' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	   			),
	   			'inform_close' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	   			),
	   			'inform_unread' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	   			),
	   			'nc_cb_execute' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	   			),
	   			'na_cb_execute' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	   			),
	   			'AutoAssignActive' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	   			),
	   			'MaxNumberActiveChats' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'int'
	   			),
	   			'MaxWaitTimeoutSeconds' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'int'
	   			),
	   			'StartHour' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 0, 'mx_range' => 24)
	   			),
	   			'StartHourMinit' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 0, 'mx_range' => 60)
	   			),
	   			'inform_unread_delay' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 5)
	   			),
	   			'EndHour' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 0, 'mx_range' => 24)
	   			),
	   			'EndHourMinit' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 0, 'mx_range' => 60)
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
	   	
	   	if ( erLhcoreClassUser::instance()->hasAccessTo('lhdepartament','actautoassignment') ) {
		   	if ( $form->hasValidData( 'AutoAssignActive' ) && $form->AutoAssignActive == true )	{
		   		$department->active_balancing = 1;
		   	} else {
		   		$department->active_balancing = 0;
		   	}
		   	
		   	if ( $form->hasValidData( 'MaxNumberActiveChats' ) )
		   	{
		   		$department->max_active_chats = $form->MaxNumberActiveChats;
		   	} else {
		   		$department->max_active_chats = 0;
		   	}
		   	
		   	if ( $form->hasValidData( 'MaxWaitTimeoutSeconds' ) )
		   	{
		   		$department->max_timeout_seconds = $form->MaxWaitTimeoutSeconds;
		   	} else {
		   		$department->max_timeout_seconds = 0;
		   	}
	   	}
	   	
	   	if ( erLhcoreClassUser::instance()->hasAccessTo('lhdepartament','actworkflow') ) {
		   	if ( $form->hasValidData( 'TansferDepartmentID' ) )
		   	{
		   		$department->department_transfer_id = $form->TansferDepartmentID;
		   	} else {
		   		$department->department_transfer_id = 0;
		   	}
		   	
		   	if ( $form->hasValidData( 'TransferTimeout' ) )
		   	{
		   		$department->transfer_timeout = $form->TransferTimeout;
		   	} else {
		   		$department->transfer_timeout = 0;
		   	}
		   			   	
		   	if ( $form->hasValidData( 'nc_cb_execute' ) && $form->nc_cb_execute == true )
		   	{
		   		$department->nc_cb_execute = 1;
		   	} else {
		   		$department->nc_cb_execute = 0;
		   	}
		   	
		   	if ( $form->hasValidData( 'na_cb_execute' ) && $form->na_cb_execute == true )
		   	{
		   		$department->na_cb_execute = 1;
		   	} else {
		   		$department->na_cb_execute = 0;
		   	}
	   	}
	   	
	   	if ( $form->hasValidData( 'Identifier' ) )
	   	{
	   		$department->identifier = $form->Identifier;
	   	}
	   	
	   	if ( $form->hasValidData( 'delay_lm' ) )
	   	{
	   		$department->delay_lm = $form->delay_lm;
	   	} else {
	   		$department->delay_lm = 0;
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
	   		   	
	   		   	
	   	if ( $form->hasValidData( 'inform_unread' ) && $form->inform_unread === true ) {
	   		$department->inform_unread = 1;
	   	} else {
	   		$department->inform_unread = 0;
	   	}
	   		   	
	   	if ($form->hasValidData( 'inform_unread_delay' )) {
	   		$department->inform_unread_delay = $form->inform_unread_delay;
	   	} elseif ($department->inform_unread == 1) {
	   		$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Minimum 5 seconds');
	   	} else {
	   		$department->inform_unread_delay = 0;
	   	}
	   		   	
	   	if ( $form->hasValidData( 'Disabled' ) && $form->Disabled === true ) {
	   		$department->disabled = 1;
	   	} else {
	   		$department->disabled = 0;
	   	}
	   		   	
	   	if ( $form->hasValidData( 'Hidden' ) && $form->Hidden === true ) {
	   		$department->hidden = 1;
	   	} else {
	   		$department->hidden = 0;
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
	   		$startHour = $form->StartHour;
	   	} else {
	   		$startHour = 0;
	   	}
	   	
	   	if ( $form->hasValidData( 'EndHour' ) ) {
	   		$endHour = $form->EndHour;
	   	} else {
	   		$endHour = 0;
	   	}
	   	
	   	if ( $form->hasValidData( 'StartHourMinit' ) ) {
	   		$StartHourMinit =  str_pad($form->StartHourMinit, 2, '0', STR_PAD_LEFT);
	   	} else {
	   		$StartHourMinit = '00';
	   	}
	   	
	   	if ( $form->hasValidData( 'EndHourMinit' ) ) {
	   		$endHourMinit = str_pad($form->EndHourMinit, 2, '0', STR_PAD_LEFT);
	   	} else {
	   		$endHourMinit = '00';
	   	}
	   	
	   	$department->start_hour = $startHour.$StartHourMinit;
	   	$department->end_hour = $endHour.$endHourMinit;
	   	
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