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
	   			'SortPriority' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'int'
	   			),
	   			'TansferDepartmentID' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
	   			),
	   			'TransferTimeout' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 5)
	   			),	   			
	   			'delay_lm' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 5)
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
	   			'VisibleIfOnline' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	   			),
                'ExcludeInactiveChats' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	   			),
	   			'MaxNumberActiveChats' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'int'
	   			),
	   			'MaxWaitTimeoutSeconds' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'int'
	   			)
                ,'MaxNumberActiveDepChats' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'int'
	   			),
	   			'pending_max' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'int'
	   			),
                'delay_before_assign' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'int'
	   			),
	   			'inform_unread_delay' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 5)
	   			),
	   			'inform_delay' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 0)
	   			),
	   			'inform_options' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'string', null, FILTER_REQUIRE_ARRAY
	   			),
	   			'inform_close_all' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	   			),
	   			'inform_close_all_email' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'string'
	   			),
	   			'DepartamentProducts' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'int', null, FILTER_REQUIRE_ARRAY
	   			),
	   			'products_enabled' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	   			),
	   			'products_required' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
	   			),
	   	);

        foreach (self::getWeekDays() as $dayShort => $dayLong) {
            $definition[$dayShort] = new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
            );

            $key = 'StartHour'.ucfirst($dayShort);
            $definition[$key] = new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 0, 'mx_range' => 24)
            );

            $key = 'StartMinutes'.ucfirst($dayShort);
            $definition[$key] = new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 0, 'mx_range' => 60)
            );

            $key = 'EndHour'.ucfirst($dayShort);
            $definition[$key] = new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 0, 'mx_range' => 24)
            );

            $key = 'EndMinutes'.ucfirst($dayShort);
            $definition[$key] = new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 0, 'mx_range' => 60)
            );
        }
		   	
	   	
	   	$form = new ezcInputForm( INPUT_POST, $definition );
	   	$Errors = array();
	   	
	   	if ( !$form->hasValidData( 'Name' ) || $form->Name == '' )
	   	{
	   		$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter a department name');
	   	} else {
	   		$department->name = $form->Name;
	   	}
	   	
	   	if ( erLhcoreClassUser::instance()->hasAccessTo('lhdepartment','actautoassignment') ) {
		   	if ( $form->hasValidData( 'AutoAssignActive' ) && $form->AutoAssignActive == true )	{
		   		$department->active_balancing = 1;
		   	} else {
		   		$department->active_balancing = 0;
		   	}
		   	
		   	if ( $form->hasValidData( 'MaxNumberActiveChats' ) )	{
		   		$department->max_active_chats = $form->MaxNumberActiveChats;
		   	} else {
		   		$department->max_active_chats = 0;
		   	}
		   	
		   	if ( $form->hasValidData( 'MaxWaitTimeoutSeconds' ) )	{
		   		$department->max_timeout_seconds = $form->MaxWaitTimeoutSeconds;
		   	} else {
		   		$department->max_timeout_seconds = 0;
		   	}

		   	if ( $form->hasValidData( 'delay_before_assign' ) )	{
		   		$department->delay_before_assign = $form->delay_before_assign;
		   	} else {
		   		$department->delay_before_assign = 0;
		   	}

		   	if ( $form->hasValidData( 'ExcludeInactiveChats' ) )	{
		   		$department->exclude_inactive_chats = $form->ExcludeInactiveChats;
		   	} else {
		   		$department->exclude_inactive_chats = 0;
		   	}

		   	if ( $form->hasValidData( 'MaxNumberActiveDepChats' ) )	{
		   		$department->max_ac_dep_chats = $form->MaxNumberActiveDepChats;
		   	} else {
		   		$department->max_ac_dep_chats = 0;
		   	}
	   	}
	   	
	   	if ( erLhcoreClassUser::instance()->hasAccessTo('lhdepartment','actworkflow') ) {
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
	   	
	   	if ( $form->hasValidData( 'pending_max' ) )
	   	{
	   		$department->pending_max = $form->pending_max;
	   	} else {
	   		$department->pending_max = 0;
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
	   	
	   	if ( $form->hasValidData( 'SortPriority' ) ) {
	   		$department->sort_priority = $form->SortPriority;
	   	} else {
	   		$department->sort_priority = 0;
	   	}
	   		   	
	   	if ( $form->hasValidData( 'inform_close' ) && $form->inform_close === true ) {
	   		$department->inform_close = 1;
	   	} else {
	   		$department->inform_close = 0;
	   	}
	   		   	
	   	if ( $form->hasValidData( 'inform_close_all' ) && $form->inform_close_all === true ) {
	   		$department->inform_close_all = 1;
	   	} else {
	   		$department->inform_close_all = 0;
	   	}
	   		   	
	   	if ( $form->hasValidData( 'inform_close_all_email' ) ) {
	   		$department->inform_close_all_email = $form->inform_close_all_email;
	   	} else {
	   		$department->inform_close_all_email = '';
	   	}

	   	if ( $form->hasValidData( 'inform_unread' ) && $form->inform_unread === true ) {
	   		$department->inform_unread = 1;
	   	} else {
	   		$department->inform_unread = 0;
	   	}
	   	
	   	if ( $form->hasValidData( 'VisibleIfOnline' ) && $form->VisibleIfOnline === true ) {
	   		$department->visible_if_online = 1;
	   	} else {
	   		$department->visible_if_online = 0;
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
	   		   	
	   	if ( $form->hasValidData( 'OnlineHoursActive' ) && $form->OnlineHoursActive === true ) {
	   		$department->online_hours_active = 1;
	   	} else {
	   		$department->online_hours_active = 0;
	   	}

	   	$productsConfiguration = array();
	   	
	   	if ( $form->hasValidData( 'products_enabled' ) && $form->products_enabled === true ) {
	   		$productsConfiguration['products_enabled'] = 1;
	   	} else {
	   		$productsConfiguration['products_enabled'] = 0;
	   	}
	   	
	   	if ( $form->hasValidData( 'products_required' ) && $form->products_required === true ) {
	   		$productsConfiguration['products_required'] = 1;
	   	} else {
	   		$productsConfiguration['products_required'] = 0;
	   	}
	   	
	   	$department->product_configuration_array = $productsConfiguration;
	   	$department->product_configuration = json_encode($productsConfiguration);
	   	
	   	if ( $form->hasValidData( 'inform_options' ) ) {
	   		$department->inform_options = serialize($form->inform_options);
	   		$department->inform_options_array = $form->inform_options;
	   	} else {
	   		$department->inform_options = serialize(array());
	   	}

	   	if ( $form->hasValidData( 'inform_delay' )  ) {
	   		$department->inform_delay = $form->inform_delay;
	   	} else {
	   		$department->inform_delay = 0;
	   	}
	   	
	   	if ($department->id > 0 && $department->department_transfer_id == $department->id) {
	   		$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Transfer department has to be different one than self');
	   	}

       foreach (self::getWeekDays() as $dayShort => $dayLong) {
           if($form->hasValidData( $dayShort ) && $form->$dayShort === true) {
               $key = 'StartHour' . ucfirst($dayShort);
               if ($form->hasValidData($key)) {
                   $startHour = $form->$key;
               } else {
                   $startHour = 0;
               }

               $key = 'EndHour' . ucfirst($dayShort);
               if ($form->hasValidData($key)) {
                   $endHour = $form->$key;
               } else {
                   $endHour = 0;
               }

               $key = 'StartMinutes' . ucfirst($dayShort);
               if ($form->hasValidData($key)) {
                   $StartMinutes = str_pad($form->$key, 2, '0', STR_PAD_LEFT);
               } else {
                   $StartMinutes = '00';
               }

               $key = 'EndMinutes' . ucfirst($dayShort);
               if ($form->hasValidData($key)) {
                   $endHourMinutes = str_pad($form->$key, 2, '0', STR_PAD_LEFT);
               } else {
                   $endHourMinutes = '00';
               }

               $key = $dayShort . '_start_hour';
               $department->$key = $startHour . $StartMinutes;

               $key = $dayShort . '_end_hour';
               $department->$key = $endHour . $endHourMinutes;
           } else {
               $key = $dayShort . '_start_hour';
               $department->$key = -1;

               $key = $dayShort . '_end_hour';
               $department->$key = -1;
           }
       }
       
       if ( $form->hasValidData( 'DepartamentProducts' ) && !empty($form->DepartamentProducts)) {
           $department->departament_products_id = $form->DepartamentProducts;
       } else {
           $department->departament_products_id = array();
       }
       
	   return $Errors;   	
   }

   public static function validateDepartmentProducts(erLhcoreClassModelDepartament $departament)
   {
       /**
        * Remove old
        */
       $db = ezcDbInstance::get();
       $stmt = $db->prepare('DELETE FROM lh_abstract_product_departament WHERE departament_id = :departament_id');
       $stmt->bindValue(':departament_id',$departament->id,PDO::PARAM_INT);
       $stmt->execute();
       
       if (is_array($departament->departament_products_id)) {
           foreach ($departament->departament_products_id as $id) {
               $item = new erLhAbstractModelProductDepartament();
               $item->product_id = $id;
               $item->departament_id = $departament->id;
               $item->saveThis();
           }
       }       
   }

    /**
     * validate and saves/removes department custom work hours, and return result of current custom work hours
     *
     * @param erLhcoreClassModelDepartament $departament
     * @param erLhcoreClassModelDepartamentCustomWorkHours[] $departamentCustomWorkHours
     * @return erLhcoreClassModelDepartamentCustomWorkHours[]
     */
   public static function validateDepartmentCustomWorkHours(erLhcoreClassModelDepartament $departament, $departamentCustomWorkHours = array())
   {
       $availableCustomWorkHours = array();

       $definition = array(
           'customPeriodId' => new ezcInputFormDefinitionElement(
               ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
           ),
           'customPeriodDateFrom' => new ezcInputFormDefinitionElement(
               ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
           ),
           'customPeriodDateTo' => new ezcInputFormDefinitionElement(
               ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
           ),
           'customPeriodStartHour' => new ezcInputFormDefinitionElement(
               ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
           ),
           'customPeriodStartHourMin' => new ezcInputFormDefinitionElement(
               ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
           ),
           'customPeriodEndHour' => new ezcInputFormDefinitionElement(
               ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
           ),
           'customPeriodEndHourMin' => new ezcInputFormDefinitionElement(
               ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
           ),
       );

       $form = new ezcInputForm( INPUT_POST, $definition );

       if ( $form->hasValidData( 'customPeriodId' ) && !empty($form->customPeriodId)) {
           foreach ($form->customPeriodId as $key => $customPeriodId) {
               if (!$customPeriodId) {
                   // if id is not defined save new custom departament work hours
                   $newDepartamentCustomWorkHours = new erLhcoreClassModelDepartamentCustomWorkHours();
                   $newDepartamentCustomWorkHours->setState(array(
                       'dep_id'         => $departament->id,
                       'date_from'      => strtotime($form->customPeriodDateFrom[$key]),
                       'date_to'        => strtotime($form->customPeriodDateTo[$key]),
                       'start_hour'     => $form->customPeriodStartHour[$key] . (($form->customPeriodStartHourMin[$key] > 0) ? str_pad($form->customPeriodStartHourMin[$key], 2, '0', STR_PAD_LEFT) : '00'),
                       'end_hour'       => $form->customPeriodEndHour[$key] . (($form->customPeriodEndHourMin[$key] > 0) ? str_pad($form->customPeriodEndHourMin[$key], 2, '0', STR_PAD_LEFT) : '00'),
                   ));

                   erLhcoreClassDepartament::getSession()->save($newDepartamentCustomWorkHours);

                   $availableCustomWorkHours[$key]              = $newDepartamentCustomWorkHours;
                   unset($departamentCustomWorkHours[$customPeriodId]);
               } elseif($customPeriodId && !empty($departamentCustomWorkHours) && isset($departamentCustomWorkHours[$customPeriodId])) {
                   // if id isset, unset from provided array
                   $availableCustomWorkHours[$key] = $departamentCustomWorkHours[$customPeriodId];
                   unset($departamentCustomWorkHours[$customPeriodId]);
               }
           }
       }

       // if there are left elements, remove them from DB
       if(!empty($departamentCustomWorkHours)) {
           foreach ($departamentCustomWorkHours as $departamentCustomWorkHour) {
               erLhcoreClassDepartament::getSession()->delete($departamentCustomWorkHour);
           }
       }

       return $availableCustomWorkHours;
   }

   /**
    * Validates department group submit
    * 
    * @param erLhcoreClassModelDepartamentGroup $departamentGroup
    */
   public static function validateDepartmentGroup(erLhcoreClassModelDepartamentGroup $departamentGroup)
   {
       $availableCustomWorkHours = array();
       
       $definition = array(
           'Name' => new ezcInputFormDefinitionElement(
               ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
           )
       );
       
       $form = new ezcInputForm( INPUT_POST, $definition );
       $Errors = array();
        
       if ( !$form->hasValidData( 'Name' ) || $form->Name == '' ) {
           $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/editgroup','Please enter a department group name');
       } else {
           $departamentGroup->name = $form->Name;
       }
       
       return $Errors;
   }
   
   /**
    * Validates department group submit
    * 
    * @param erLhcoreClassModelDepartamentGroup $departamentGroup
    */
   public static function validateDepartmentLimitGroup(erLhcoreClassModelDepartamentLimitGroup $departamentGroup)
   {
       $availableCustomWorkHours = array();
       
       $definition = array(
           'Name' => new ezcInputFormDefinitionElement(
               ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
           ),
           'PendingMax' => new ezcInputFormDefinitionElement(
               ezcInputFormDefinitionElement::OPTIONAL, 'int'
           )
       );
       
       $form = new ezcInputForm( INPUT_POST, $definition );
       $Errors = array();
        
       if ( !$form->hasValidData( 'Name' ) || $form->Name == '' ) {
           $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/editgroup','Please enter a department group name');
       } else {
           $departamentGroup->name = $form->Name;
       }
       
       if ( $form->hasValidData( 'PendingMax' )) {
           $departamentGroup->pending_max = $form->PendingMax;
       } else {
           $departamentGroup->pending_max = 0;
       }
       
       return $Errors;
   }
   
   /**
    * Validates department group submit
    * 
    * @param erLhcoreClassModelDepartamentGroup $departamentGroup
    * 
    */
   public static function validateDepartmentGroupDepartments(erLhcoreClassModelDepartamentGroup $departamentGroup)
   {
       $definition = array(
           'departaments' => new ezcInputFormDefinitionElement(
               ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
           ));
       
       $form = new ezcInputForm( INPUT_POST, $definition );
       $Errors = array();
       
       if ( $form->hasValidData( 'departaments' ) && !empty($form->departaments)) {
           // Remove old departaments
           self::assignDepartmentsToGroup($departamentGroup, $form->departaments);
       } else {
           // Remove old departaments
           self::assignDepartmentsToGroup($departamentGroup, array());
       }
   }
   
   /**
    * Validates department group submit
    * 
    * @param erLhcoreClassModelDepartamentLimitGroup $departamentGroup
    * 
    */
   public static function validateDepartmentGroupLimitDepartments(erLhcoreClassModelDepartamentLimitGroup $departamentGroup)
   {
       $definition = array(
           'departaments' => new ezcInputFormDefinitionElement(
               ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
           ));
       
       $form = new ezcInputForm( INPUT_POST, $definition );
       $Errors = array();
       
       if ( $form->hasValidData( 'departaments' ) && !empty($form->departaments)) {
           // Remove old departaments
           self::assignDepartmentsToLimitGroup($departamentGroup, $form->departaments);
       } else {
           // Remove old departaments
           self::assignDepartmentsToLimitGroup($departamentGroup, array());
       }
   }
   
   public static function assignDepartmentsToLimitGroup(erLhcoreClassModelDepartamentLimitGroup $departamentGroup, $ids)
   {
       $members = erLhcoreClassModelDepartamentLimitGroupMember::getList(array('limit' => false,'filter' => array('dep_limit_group_id' => $departamentGroup->id)));
       
       $newMembers = array();
       $removeMembers = array();       
       $oldMembers = array();
       
       // Remove old members
       foreach ($members as $member) {
           if (!in_array($member->dep_id, $ids)) {
               $member->removeThis();
           } else {
               $oldMembers[] = $member->dep_id;
           }
       }
       
       // Store new members
       foreach ($ids as $id) {
           if (!in_array($id, $oldMembers)) {
               $member = new erLhcoreClassModelDepartamentLimitGroupMember();
               $member->dep_id = $id;
               $member->dep_limit_group_id = $departamentGroup->id;
               $member->saveThis();
           }
       }
   }
   
   
   public static function assignDepartmentsToGroup(erLhcoreClassModelDepartamentGroup $departamentGroup, $ids)
   {
       $members = erLhcoreClassModelDepartamentGroupMember::getList(array('limit' => false,'filter' => array('dep_group_id' => $departamentGroup->id)));
       
       $newMembers = array();
       $removeMembers = array();       
       $oldMembers = array();
       
       // Remove old members
       foreach ($members as $member) {
           if (!in_array($member->dep_id, $ids)) {
               $member->removeThis();
           } else {
               $oldMembers[] = $member->dep_id;
           }
       }
       
       // Store new members
       foreach ($ids as $id) {
           if (!in_array($id, $oldMembers)) {
               $member = new erLhcoreClassModelDepartamentGroupMember();
               $member->dep_id = $id;
               $member->dep_group_id = $departamentGroup->id;
               $member->saveThis();
           }
       }
   }
   
    /**
     * Convert departament custom work hours to template data
     *
     * @param erLhcoreClassModelDepartamentCustomWorkHours[] $departamentCustomWorkHours
     * @return array
     */
   public static function getDepartamentCustomWorkHoursData($departamentCustomWorkHours = array())
   {
       $data = array();

       foreach ($departamentCustomWorkHours as $departamentCustomWorkHour) {
           $data[] = array(
               'dep_id'         => $departamentCustomWorkHour->dep_id,
               'date_from'      => date('Y-m-d', $departamentCustomWorkHour->date_from),
               'date_to'        => date('Y-m-d', $departamentCustomWorkHour->date_to),
               'start_hour'     => $departamentCustomWorkHour->start_hour_front,
               'start_hour_min' => $departamentCustomWorkHour->start_minutes_front,
               'end_hour'       => $departamentCustomWorkHour->end_hour_front,
               'end_hour_min'   => $departamentCustomWorkHour->end_minutes_front
           );
       }

       return $data;
   }

   public static function getWeekDays()
   {        
       return array(
           'mod' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Monday'),
           'tud' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Tuesday'),
           'wed' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Wednesday'),
           'thd' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Thursday'),
           'frd' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Friday'),
           'sad' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Saturday'),
           'sud' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Sunday')
       );
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