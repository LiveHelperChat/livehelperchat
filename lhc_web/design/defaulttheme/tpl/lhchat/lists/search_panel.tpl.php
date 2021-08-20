<form action="<?php echo $input->form_action?>" method="get" name="SearchFormRight" ng-non-bindable autocomplete="off">

	<input type="hidden" name="doSearch" value="1">

	<div class="row">
		<div class="col-md-2">
		   <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Nick');?></label>
			<input type="text" class="form-control form-control-sm" name="nick" value="<?php echo htmlspecialchars($input->nick)?>" />
		   </div>
		</div>
		<div class="col-md-2">
		  <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','E-mail');?></label>
			<input type="text" class="form-control form-control-sm" name="email" value="<?php echo htmlspecialchars($input->email)?>" />
		  </div>
		</div>
		
		<div class="col-md-2">
		  <div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                  'input_name'     => 'department_ids[]',
                  'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department'),
                  'selected_id'    => $input->department_ids,
                  'css_class'      => 'form-control',
                  'display_name'   => 'name',
                  'list_function_params' => erLhcoreClassUserDep::conditionalDepartmentFilter(),
                  'list_function'  => 'erLhcoreClassModelDepartament::getList'
                )); ?>
		  </div>
		</div>

        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department group');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'department_group_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department group'),
                    'selected_id'    => $input->department_group_ids,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name',
                    'list_function_params' => erLhcoreClassUserDep::conditionalDepartmentGroupFilter(),
                    'list_function'  => 'erLhcoreClassModelDepartamentGroup::getList'
                )); ?>
            </div>
        </div>

        <div class="col-md-2">
		   <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','User');?></label>
               <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                   'input_name'     => 'user_ids[]',
                   'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select user'),
                   'selected_id'    => $input->user_ids,
                   'css_class'      => 'form-control',
                   'display_name'   => 'name_official',
                   'ajax'           => 'users',
                   'list_function_params' => array_merge(erLhcoreClassGroupUser::getConditionalUserFilter(),array('limit' => 50)),
                   'list_function'  => 'erLhcoreClassModelUser::getUserList',
               )); ?>
		  </div>
		</div>

        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','User group');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'group_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select group'),
                    'selected_id'    => $input->group_ids,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name',
                    'list_function_params' => erLhcoreClassGroupUser::getConditionalUserFilter(false, true),
                    'list_function'  => 'erLhcoreClassModelGroup::getList'
                )); ?>
            </div>
        </div>

        <div class="col-md-2">
		  <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range from');?></label>
    			<div class="row">
    				<div class="col-md-12">
    					<input type="text" class="form-control form-control-sm" name="timefrom" id="id_timefrom" placeholder="E.g <?php echo date('Y-m-d',time()-7*24*3600)?>" value="<?php echo htmlspecialchars($input->timefrom)?>" />
    				</div>							
    			</div>
			</div>
		</div>

		<div class="col-md-3">
		  <div class="form-group">
              <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute from');?> <small>[<?php echo date('H:i:s')?>]</small></label>
			<div class="row">				
				<div class="col-md-6">
				    <select name="timefrom_hours" class="form-control form-control-sm">
				        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select hour');?></option>
				        <?php for ($i = 0; $i <= 23; $i++) : ?>
				            <option value="<?php echo $i?>" <?php if (isset($input->timefrom_hours) && $input->timefrom_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
				        <?php endfor;?>
				    </select>
				</div>
				<div class="col-md-6">
				    <select name="timefrom_minutes" class="form-control form-control-sm">
				        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select minute');?></option>
				        <?php for ($i = 0; $i <= 59; $i++) : ?>
				            <option value="<?php echo $i?>" <?php if (isset($input->timefrom_minutes) && $input->timefrom_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
				        <?php endfor;?>
				    </select>
				</div>
			</div>
			</div>
		</div>
		
		<div class="col-md-2">
		  <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range to');?></label>
    			<div class="row">
    				<div class="col-md-12">
    					<input type="text" class="form-control form-control-sm" name="timeto" id="id_timeto" placeholder="E.g <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($input->timeto)?>" />
    				</div>							
    			</div>
			</div>
		</div>
		
		<div class="col-md-3">
		  <div class="form-group">
              <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute to');?> <small>[<?php echo date('H:i:s')?>]</small></label>
		    <div class="row">				
				<div class="col-md-6">
				    <select name="timeto_hours" class="form-control form-control-sm">
				        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select hour');?></option>
				        <?php for ($i = 0; $i <= 23; $i++) : ?>
				            <option value="<?php echo $i?>" <?php if (isset($input->timeto_hours) && $input->timeto_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
				        <?php endfor;?>
				    </select>
				</div>
				<div class="col-md-6">
				    <select name="timeto_minutes" class="form-control form-control-sm">
				        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select minute');?></option>
				        <?php for ($i = 0; $i <= 59; $i++) : ?>
				            <option value="<?php echo $i?>" <?php if (isset($input->timeto_minutes) && $input->timeto_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
				        <?php endfor;?>
				    </select>
				</div>
		    </div>
		  </div>
        </div>

          <div class="col-md-2">
              <div class="form-group">
                  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Wait time');?></label>
                  <div class="row">
                      <div class="col-6">
                          <select class="form-control form-control-sm" name="wait_time_from">
                              <option>More than</option>
                              <option value="0" <?php $input->wait_time_from === 0 ? print 'selected="selected"' : ''?>>0 seconds</option>
                              <option value="5" <?php $input->wait_time_from === 5 ? print 'selected="selected"' : ''?>>5 seconds</option>
                              <option value="10" <?php $input->wait_time_from === 10 ? print 'selected="selected"' : ''?>>10 seconds</option>
                              <option value="20" <?php $input->wait_time_from === 20 ? print 'selected="selected"' : ''?>>20 seconds</option>
                              <option value="30" <?php $input->wait_time_from === 30 ? print 'selected="selected"' : ''?>>30 seconds</option>
                              <option value="40" <?php $input->wait_time_from === 40 ? print 'selected="selected"' : ''?>>40 seconds</option>
                              <option value="50" <?php $input->wait_time_from === 50 ? print 'selected="selected"' : ''?>>50 seconds</option>
                              <option value="60" <?php $input->wait_time_from === 60 ? print 'selected="selected"' : ''?>>60 seconds</option>
                              <option value="90" <?php $input->wait_time_from === 90 ? print 'selected="selected"' : ''?>>90 seconds</option>

                              <?php for ($i = 2; $i < 5; $i++) : ?>
                                  <option value="<?php echo $i*60?>" <?php $input->wait_time_from === $i*60 ? print 'selected="selected"' : ''?>><?php echo  $i?> m.</option>
                              <?php endfor ?>

                              <?php for ($i = 2; $i < 13; $i++) : ?>
                                <option value="<?php echo $i*5*60?>" <?php $i*60*5 === $input->wait_time_from ? print 'selected="selected"' : ''?>><?php echo $i*5?> m.</option>
                              <?php endfor ?>
                          </select>
                      </div>
                      <div class="col-6">
                          <select class="form-control form-control-sm" name="wait_time_till">
                              <option>Less than</option>
                              <option value="0" <?php $input->wait_time_till === 0 ? print 'selected="selected"' : ''?>>0 seconds</option>
                              <option value="5" <?php $input->wait_time_till === 5 ? print 'selected="selected"' : ''?>>5 seconds</option>
                              <option value="10" <?php $input->wait_time_till === 10 ? print 'selected="selected"' : ''?>>10 seconds</option>
                              <option value="20" <?php $input->wait_time_till === 20 ? print 'selected="selected"' : ''?>>20 seconds</option>
                              <option value="30" <?php $input->wait_time_till === 30 ? print 'selected="selected"' : ''?>>30 seconds</option>
                              <option value="40" <?php $input->wait_time_till === 40 ? print 'selected="selected"' : ''?>>40 seconds</option>
                              <option value="50" <?php $input->wait_time_till === 50 ? print 'selected="selected"' : ''?>>50 seconds</option>
                              <option value="60" <?php $input->wait_time_till === 60 ? print 'selected="selected"' : ''?>>60 seconds</option>
                              <option value="90" <?php $input->wait_time_till === 90 ? print 'selected="selected"' : ''?>>90 seconds</option>

                              <?php for ($i = 2; $i < 5; $i++) : ?>
                                  <option value="<?php echo $i*60?>" <?php $input->wait_time_till === $i*60 ? print 'selected="selected"' : ''?>><?php echo  $i?> m.</option>
                              <?php endfor ?>

                              <?php for ($i = 2; $i < 13; $i++) : ?>
                                  <option value="<?php echo $i*60*5?>" <?php $i*60*5 === $input->wait_time_till ? print 'selected="selected"' : ''?> ><?php echo $i*5?> m.</option>
                              <?php endfor ?>
                          </select>
                      </div>
                  </div>
              </div>
          </div>

		
	</div>
			
	<div class="row">
		<div class="col-md-2">
		   <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Vote status');?></label>
			<select name="fbst" class="form-control form-control-sm">
				<option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Any');?></option>
				<option value="0" <?php if ($input->fbst === 0) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Not Voted');?></option>
				<option value="1" <?php if ($input->fbst === 1) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Up Voted');?></option>
				<option value="2" <?php if ($input->fbst === 2) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Down vote');?></option>
			</select>           	
		  </div>
		</div>		
		<div class="col-md-2">
		   <div class="form-group">
			    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chat status');?></label>

               <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                   'input_name'     => 'chat_status_ids[]',
                   'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose status'),
                   'selected_id'    => $input->chat_status_ids,
                   'css_class'      => 'form-control',
                   'display_name'   => 'name',
                   'list_function_params' => array(),
                   'list_function'  => function () {
                       $items = array();

                       $item = new StdClass();
                       $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Pending chats');
                       $item->id = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
                       $items[] = $item;

                       $item = new StdClass();
                       $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Active chats');
                       $item->id = erLhcoreClassModelChat::STATUS_ACTIVE_CHAT;
                       $items[] = $item;

                       $item = new StdClass();
                       $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Bot chats');
                       $item->id = erLhcoreClassModelChat::STATUS_BOT_CHAT;
                       $items[] = $item;

                       $item = new StdClass();
                       $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Closed chats');
                       $item->id = erLhcoreClassModelChat::STATUS_CLOSED_CHAT;
                       $items[] = $item;

                       $item = new StdClass();
                       $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chatbox chats');
                       $item->id = erLhcoreClassModelChat::STATUS_CHATBOX_CHAT;
                       $items[] = $item;

                       $item = new StdClass();
                       $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Operators chats');
                       $item->id = erLhcoreClassModelChat::STATUS_OPERATORS_CHAT;
                       $items[] = $item;

                       return $items;
                   }
               )); ?>


		  </div>
		</div>	
		<div class="col-md-3">
		    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Product');?></label>
			<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
	                    'input_name'     => 'product_id',
						'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select product'),
	                    'selected_id'    => $input->product_id,
			            'css_class'      => 'form-control form-control-sm',
	                    'list_function'  => 'erLhAbstractModelProduct::getList'
	         )); ?>
		</div>
		<div class="col-md-1">
			<div class="form-group">
		    	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chat ID');?></label>
				<input type="text" class="form-control form-control-sm" placeholder="<?php echo htmlspecialchars("<id>[,<id>]");?>" name="chat_id" value="<?php echo htmlspecialchars($input->chat_id)?>" />
			</div>
		</div>
        <div class="col-md-1">
			<div class="form-group">
		    	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Visitor ID');?></label>
				<input type="text" class="form-control form-control-sm" placeholder="<?php echo htmlspecialchars("<id>[,<id>]");?>" name="visitor_id" value="<?php echo htmlspecialchars($input->visitor_id)?>" />
			</div>
		</div>
        <div class="col-md-3">
			<div class="form-group">
		    	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chat duration');?></label>
                <div class="row">
                    <div class="col-6">
                        <select class="form-control form-control-sm" name="chat_duration_from" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chat duration from');?>">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','From');?></option>
                            <option value="1" <?php 1 === $input->chat_duration_from ? print 'selected="selected"' : ''?> >1 s.</option>
                            <?php for ($i = 1; $i < 10; $i++) : ?>
                                <option value="<?php echo $i*60?>" <?php $i*60 === $input->chat_duration_from ? print 'selected="selected"' : ''?> ><?php echo $i?> m.</option>
                            <?php endfor; ?>

                            <?php for ($i = 2; $i < 19; $i++) : ?>
                                <option value="<?php echo $i*60*5?>" <?php $i*60*5 === $input->chat_duration_from ? print 'selected="selected"' : ''?> ><?php echo $i*5?> m.</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-6">
                        <select class="form-control form-control-sm" name="chat_duration_till" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chat duration till');?>">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Till');?></option>
                            <?php for ($i = 1; $i < 10; $i++) : ?>
                                <option value="<?php echo $i*60?>" <?php $i*60 === $input->chat_duration_till ? print 'selected="selected"' : ''?> ><?php echo $i?> m.</option>
                            <?php endfor; ?>

                            <?php for ($i = 2; $i < 19; $i++) : ?>
                                <option value="<?php echo $i*60*5?>" <?php $i*60*5 === $input->chat_duration_till ? print 'selected="selected"' : ''?> ><?php echo $i*5?> m.</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                 </div>
			</div>
		</div>
	</div>
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Subject');?></label>
                <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                    'input_name'     => 'subject_id',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Subject'),
                    'selected_id'    => $input->subject_id,
                    'css_class'      => 'form-control form-control-sm',
                    'display_name'   => 'name',
                    'list_function_params'  => (new erLhAbstractModelSubject())->getFilter(),
                    'list_function'  => 'erLhAbstractModelSubject::getList'
                )); ?>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Proactive invitation');?></label>
                <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                    'input_name'     => 'invitation_id',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose proactive invitation'),
                    'selected_id'    => $input->invitation_id,
                    'css_class'      => 'form-control form-control-sm',
                    'display_name'   => 'name',
                    'list_function'  => 'erLhAbstractModelProactiveChatInvitation::getList'
                )); ?>
            </div>
        </div>
        <div class="col-md-2">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Bot');?></label>
            <div class="form-group">
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'bot_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select bot'),
                    'selected_id'    => $input->bot_ids,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name',
                    'list_function_params' => [],
                    'list_function'  => 'erLhcoreClassModelGenericBotBot::getList'
                )); ?>
            </div>
        </div>
        <div class="col-md-2">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Visitor status on chat close');?></label>
            <div class="form-group">
                <select name="cls_us" class="form-control form-control-sm">
                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Any');?></option>
                    <option value="1" <?php $input->cls_us === 1 ? print 'selected="selected"' : '' ?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Online');?></option>
                    <option value="2" <?php $input->cls_us === 2 ? print 'selected="selected"' : '' ?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Offline');?></option>
                    <option value="0" <?php $input->cls_us === 0 ? print 'selected="selected"' : '' ?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Undetermined');?></option>
                </select>
            </div>
        </div>

        <div class="col-md-2">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Has unread operator messages');?></label>
            <div class="form-group">
                <select name="has_unread_op_messages" class="form-control form-control-sm">
                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Any');?></option>
                    <option value="1" <?php $input->has_unread_op_messages === 1 ? print 'selected="selected"' : '' ?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Yes');?></option>
                    <option value="0" <?php $input->has_unread_op_messages === 0 ? print 'selected="selected"' : '' ?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','No');?></option>
                </select>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','IP');?></label>
                <input type="text" class="form-control form-control-sm" name="ip" value="<?php echo htmlspecialchars($input->ip)?>" />
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'country_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose country'),
                    'selected_id'    => $input->country_ids,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name',
                    'list_function'  => 'lhCountries::getCountries'
                )); ?>
            </div>
        </div>

		<div class="col-md-2">
    		<div class="form-group">
        	   <label class="col-form-label"><input type="checkbox" name="hum" <?php $input->hum == 1 ? print ' checked="checked" ' : ''?> value="on" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Has unread messages')?></label>
        	</div>
		</div>
        <div class="col-md-2">
    		<div class="form-group">
        	   <label class="col-form-label"><input type="checkbox" name="una" <?php $input->una == 1 ? print ' checked="checked" ' : ''?> value="on" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Unanswered chat')?></label>
        	</div>
		</div>
        <div class="col-md-2">
    		<div class="form-group">
        	   <label class="col-form-label"><input type="checkbox" name="anonymized" <?php $input->anonymized == 1 ? print ' checked="checked" ' : ''?> value="on" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Anonymised')?></label>
        	</div>
		</div>

        <div class="col-2"><label class="col-form-label"><input type="checkbox" name="no_operator" value="1" <?php $input->no_operator == true ? print 'checked="checked"' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chats without an operator')?></label></div>
        <div class="col-2"><label class="col-form-label"><input type="checkbox" name="has_operator" value="1" <?php $input->has_operator == true ? print 'checked="checked"' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chats with an operator')?></label></div>
        <div class="col-2"><label class="col-form-label"><input type="checkbox" name="with_bot" value="1" <?php $input->with_bot == true ? print 'checked="checked"' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chats which had a bot')?></label></div>
        <div class="col-2"><label class="col-form-label"><input type="checkbox" name="without_bot" value="1" <?php $input->without_bot == true ? print 'checked="checked"' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chats which did not had a bot')?></label></div>
        <div class="col-2"><label class="col-form-label"><input type="checkbox" name="abandoned_chat" value="1" <?php $input->abandoned_chat == true ? print 'checked="checked"' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Abandoned chats')?></label></div>
        <div class="col-2"><label class="col-form-label"><input type="checkbox" name="dropped_chat" value="1" <?php $input->dropped_chat == true ? print 'checked="checked"' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Dropped chat')?></label></div>

        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/search_panel_multiinclude.tpl.php'));?>

    </div>

    <div class="row mb-2">
        <div class="col-12">
            <div class="btn-group" role="group" aria-label="...">
                <input type="submit" name="doSearch" class="btn btn-secondary btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />
            </div>
            <div class="btn-group" role="group" aria-label="...">
                <?php if ($pages->items_total > 0) : $appendPrintExportURL = '';?>
                    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/search_panel_append_print_multiinclude.tpl.php'));?>
                    <a target="_blank" class="btn btn-secondary btn-sm" href="<?php echo $pages->serverURL?>/(print)/1?<?php echo $appendPrintExportURL?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Print');?></a>
                    <button type="button" onclick="return lhc.revealModal({'title' : 'Export', 'height':350, backdrop:true, 'url':'<?php echo $pages->serverURL?>/(export)/1?<?php echo $appendPrintExportURL?>'})" class="btn btn-secondary btn-sm"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Export')?> (<?php echo $pages->items_total?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','chats');?>)</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
	
</form>

<script>
$(function() {
	$('#id_timefrom,#id_timeto').fdatepicker({
		format: 'yyyy-mm-dd'
	});
    $('.btn-block-department').makeDropdown();
});
</script>