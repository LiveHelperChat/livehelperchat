<form action="" id="form-statistic-action" method="get" autocomplete="off" ng-non-bindable>

<input type="hidden" name="doSearch" value="on" />
<input type="hidden" id="id-report-type" name="reportType" value="live" />

<div class="row form-group">

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
                'list_function_params' => array_merge(erLhcoreClassGroupUser::getConditionalUserFilter(),array('sort' => '`name` ASC','limit' => 50)),
                'list_function'  => 'erLhcoreClassModelUser::getUserList'
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
           'list_function_params' => array_merge(array('sort' => '`name` ASC'),erLhcoreClassGroupUser::getConditionalUserFilter(false, true)),
           'list_function'  => 'erLhcoreClassModelGroup::getList'
        )); ?>
        </div>   
    </div>
    
    <div class="col-md-2">
    <div class="form-group">
	   <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Group by');?></label>
	   <select name="groupby" class="form-control form-control-sm">
	       <option value="0" <?php $input->groupby == 0 ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Month');?></option>
	       <option value="1" <?php $input->groupby == 1 ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Day');?></option>
	       <option value="2" <?php $input->groupby == 2 ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Week');?></option>
	       <option value="3" <?php $input->groupby == 3 ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Day of the week');?></option>
	   </select>
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
                'ajax'           => 'deps',
                'list_function_params' => array_merge(['sort' => '`name` ASC','limit' => 50],erLhcoreClassUserDep::conditionalDepartmentFilter()),
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
                'list_function_params' => array_merge(['sort' => '`name` ASC'],erLhcoreClassUserDep::conditionalDepartmentGroupFilter()),
                'list_function'  => 'erLhcoreClassModelDepartamentGroup::getList'
            )); ?>
        </div>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Invitation');?></label>
            <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                'input_name'     => 'invitation_ids[]',
                'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose proactive invitation'),
                'selected_id'    => $input->invitation_ids,
                'css_class'      => 'form-control',
                'display_name'   => 'name',
                'list_function_params'  => ['sort' => '`name` ASC'],
                'list_function'  => 'erLhAbstractModelProactiveChatInvitation::getList'
            )); ?>
        </div>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Bot');?></label>
            <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                'input_name'     => 'bot_ids[]',
                'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select bot'),
                'selected_id'    => $input->bot_ids,
                'css_class'      => 'form-control',
                'display_name'   => 'name',
                'list_function_params'  => ['sort' => '`name` ASC'],
                'list_function'  => 'erLhcoreClassModelGenericBotBot::getList'
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
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute from');?></label>
		<div class="row">				
			<div class="col-md-4">
			    <select name="timefrom_hours" class="form-control form-control-sm">
			        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select hour');?></option>
			        <?php for ($i = 0; $i <= 23; $i++) : ?>
			            <option value="<?php echo $i?>" <?php if (isset($input->timefrom_hours) && $input->timefrom_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
			        <?php endfor;?>
			    </select>
			</div>
			<div class="col-md-4">
			    <select name="timefrom_minutes" class="form-control form-control-sm">
			        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select minute');?></option>
			        <?php for ($i = 0; $i <= 59; $i++) : ?>
			            <option value="<?php echo $i?>" <?php if (isset($input->timefrom_minutes) && $input->timefrom_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
			        <?php endfor;?>
			    </select>
			</div>
            <div class="col-md-4">
                <select name="timefrom_seconds" class="form-control form-control-sm">
                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select seconds');?></option>
                    <?php for ($i = 0; $i <= 59; $i++) : ?>
                        <option value="<?php echo $i?>" <?php if (isset($input->timefrom_seconds) && $input->timefrom_seconds === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> s.</option>
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
		<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute to');?></label>
	    <div class="row">
			<div class="col-md-4">
			    <select name="timeto_hours" class="form-control form-control-sm">
			        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select hour');?></option>
			        <?php for ($i = 0; $i <= 23; $i++) : ?>
			            <option value="<?php echo $i?>" <?php if (isset($input->timeto_hours) && $input->timeto_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
			        <?php endfor;?>
			    </select>
			</div>
			<div class="col-md-4">
			    <select name="timeto_minutes" class="form-control form-control-sm">
			        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select minute');?></option>
			        <?php for ($i = 0; $i <= 59; $i++) : ?>
			            <option value="<?php echo $i?>" <?php if (isset($input->timeto_minutes) && $input->timeto_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
			        <?php endfor;?>
			    </select>
			</div>
            <div class="col-md-4">
                <select name="timeto_seconds" class="form-control form-control-sm">
                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select seconds');?></option>
                    <?php for ($i = 0; $i <= 59; $i++) : ?>
                        <option value="<?php echo $i?>" <?php if (isset($input->timeto_seconds) && $input->timeto_seconds === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> s.</option>
                    <?php endfor;?>
                </select>
            </div>
	    </div>
	  </div>
	</div>

    <div class="col-md-2">
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Group field');?></label>
                    <select class="form-control form-control-sm" name="group_field">
                        <?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/filter/group_field.tpl.php'));?>
                    </select>
                </div>
            </div>
            <div class="col-6">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Group limit');?></label>
                <select class="form-control form-control-sm" name="group_limit">
                    <option value="3" <?php if ($input->group_limit == 3) : ?>selected<?php endif;?> >3</option>
                    <option value="5" <?php if ($input->group_limit == 5) : ?>selected<?php endif;?> >5</option>
                    <option value="10" <?php if ($input->group_limit == 10 || $input->group_limit == '') : ?>selected<?php endif;?> >10</option>
                    <option value="15" <?php if ($input->group_limit == 15) : ?>selected<?php endif;?>>15</option>
                    <option value="20" <?php if ($input->group_limit == 20) : ?>selected<?php endif;?>>20</option>
                    <option value="25" <?php if ($input->group_limit == 25) : ?>selected<?php endif;?>>25</option>
                    <option value="30" <?php if ($input->group_limit == 30) : ?>selected<?php endif;?>>30</option>
                    <option value="40" <?php if ($input->group_limit == 40) : ?>selected<?php endif;?>>40</option>
                    <option value="50" <?php if ($input->group_limit == 50) : ?>selected<?php endif;?>>50</option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Group chart');?></label>
        <select class="form-control form-control-sm" name="group_chart_type">
            <option value="vertical_bar" <?php if ($input->group_chart_type == 'vertical_bar') : ?>selected<?php endif;?> >Vertical Bar Chart</option>
            <option value="stacked_bar" <?php if ($input->group_chart_type == 'stacked_bar') : ?>selected<?php endif;?> >Stacked Bar Chart</option>
        </select>
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
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Wait time');?></label>
            <div class="row">
                <div class="col-6">
                    <select class="form-control form-control-sm" name="wait_time_from">
                        <option>More than</option>
                        <option value="0" <?php $input->wait_time_from === 0 ? print 'selected="selected"' : ''?>>0 seconds</option>
                        <option value="1" <?php $input->wait_time_from === 1 ? print 'selected="selected"' : ''?>>1 seconds</option>
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
                        <option value="1" <?php $input->wait_time_till === 1 ? print 'selected="selected"' : ''?>>1 seconds</option>
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

    <div class="col-md-1">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Country');?></label>
            <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                'input_name'     => 'country_ids[]',
                'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select country'),
                'selected_id'    => $input->country_ids,
                'css_class'      => 'form-control',
                'display_name'   => 'name',
                'list_function_params' => [],
                'list_function'  => 'lhCountries::getCountries'
            )); ?>
        </div>
    </div>

    <div class="col-md-1">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Region');?></label>
            <input type="text" list="regions" class="form-control form-control-sm" name="region" value="<?php echo htmlspecialchars($input->region)?>">
        </div>
        <datalist id="regions">
            <?php foreach (lhCountries::getStates() as $stateCode => $stateName) : ?>
            <option value="<?php echo htmlspecialchars($stateName)?>">
                <?php endforeach; ?>
        </datalist>
    </div>

    <div class="col-md-12">
        <div class="row">
            <div class="col-4"><label><input type="checkbox" name="exclude_offline" value="<?php echo erLhcoreClassModelChat::STATUS_SUB_OFFLINE_REQUEST ?>" <?php $input->exclude_offline == erLhcoreClassModelChat::STATUS_SUB_OFFLINE_REQUEST ? print 'checked="checked"' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Exclude offline requests from charts')?></label></div>
            <div class="col-4"><label><input type="checkbox" name="online_offline" value="<?php echo erLhcoreClassModelChat::STATUS_SUB_OFFLINE_REQUEST ?>" <?php $input->online_offline == erLhcoreClassModelChat::STATUS_SUB_OFFLINE_REQUEST ? print 'checked="checked"' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Show only offline requests')?></label></div>
            <div class="col-4"><label><input type="checkbox" name="no_operator" value="1" <?php $input->no_operator == true ? print 'checked="checked"' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chats without an operator')?></label></div>
            <div class="col-4"><label><input type="checkbox" name="has_operator" value="1" <?php $input->has_operator == true ? print 'checked="checked"' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chats with an operator')?></label></div>
            <div class="col-4"><label><input type="checkbox" name="with_bot" value="1" <?php $input->with_bot == true ? print 'checked="checked"' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chats which had a bot')?></label></div>
            <div class="col-4"><label><input type="checkbox" name="without_bot" value="1" <?php $input->without_bot == true ? print 'checked="checked"' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chats which did not have a bot')?></label></div>
            <div class="col-4"><label><input type="checkbox" name="has_unread_messages" value="1" <?php $input->has_unread_messages == true ? print 'checked="checked"' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Has unread messages from visitor')?></label></div>
            <?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/filter/chat_abandoned_chat.tpl.php'));?>
            <?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/filter/chat_dropped_chat.tpl.php'));?>
            <div class="col-4"><label><input type="checkbox" name="proactive_chat" value="<?php echo erLhcoreClassModelChat::CHAT_INITIATOR_PROACTIVE ?>" <?php $input->proactive_chat == true ? print 'checked="checked"' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Proactive chat')?></label></div>
            <div class="col-4"><label><input type="checkbox" name="not_invitation" value="0" <?php $input->not_invitation === 0 ? print 'checked="checked"' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Not automatic invitation')?></label></div>
            <div class="col-4"><label><input type="checkbox" name="transfer_happened" value="1" <?php $input->transfer_happened == true ? print 'checked="checked"' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Transfer happened')?></label></div>
        </div>
    </div>

    <?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/filter/statistic_chat_filter_multiinclude.tpl.php'));?>

    <div class="col-md-12">
        <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','What charts to display')?></h6>
        <div class="row">
            <div class="col-4"><label><input type="checkbox" name="chart_type[]" value="total_chats" <?php if (in_array('total_chats',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total chats')?></label></div>
            <div class="col-4"><label><input type="checkbox" name="chart_type[]" value="active" <?php if (in_array('active',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Chat numbers by status')?></label></div>
            <div class="col-4"><label><input type="checkbox" name="chart_type[]" value="unanswered" <?php if (in_array('unanswered',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Unanswered chat numbers')?></label></div>
            <div class="col-4"><label><input type="checkbox" name="chart_type[]" value="msgtype" <?php if (in_array('msgtype',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Message types')?></label></div>
            <div class="col-4"><label><input type="checkbox" name="chart_type[]" value="proactivevsdefault" <?php if (in_array('proactivevsdefault',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Proactive chats number vs visitors initiated')?></label></div>
            <div class="col-4"><label><input type="checkbox" name="chart_type[]" value="waitmonth" <?php if (in_array('waitmonth',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?>> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Average wait time in seconds (maximum of 10 minutes)')?></label></div>
            <div class="col-4"><label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Usefull if you prefill usernames always')?>"><input type="checkbox" name="chart_type[]" value="nickgroupingdate" <?php if (in_array('nickgroupingdate',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Unique group field records grouped by date')?></label></div>
            <div class="col-4"><label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Usefull if you prefill usernames always')?>"><input type="checkbox" name="chart_type[]" value="nickgroupingdatenick" <?php if (in_array('nickgroupingdatenick',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Chats number grouped by date and group field')?></label></div>
            <div class="col-4"><label><input type="checkbox" name="chart_type[]" value="by_channel" <?php if (in_array('by_channel',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total chats by channel')?></label></div>
            <div class="col-4"><label><input type="checkbox" name="chart_type[]" value="msgdelop" <?php if (in_array('msgdelop',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Message delivery statistic (operator)')?></label></div>
            <div class="col-4"><label><input type="checkbox" name="chart_type[]" value="msgdelbot" <?php if (in_array('msgdelbot',is_array($input->chart_type) ? $input->chart_type : array())) : ?>checked="checked"<?php endif;?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Message delivery statistic (bot)')?></label></div>
        </div>
    </div>

</div>

    <div class="btn-group me-2" role="group" aria-label="...">
        <button type="submit" name="doSearch" onclick="$('#id-report-type').val('live')" class="btn btn-sm btn-primary" >
            <span class="material-icons">search</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>
        </button>
        <a class="btn btn-outline-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('statistic/statistic')?>/(tab)/chatsstatistic"><span class="material-icons">refresh</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Reset');?></a>
        <?php $tabStatistic = 'chatsstatistic'; ?>
        <?php include(erLhcoreClassDesign::designtpl('lhstatistic/report_button.tpl.php'));?>
    </div>

	<script>
	$(function() {
		$('#id_timefrom,#id_timeto').fdatepicker({
			format: 'yyyy-mm-dd'
		});
        $('.btn-block-department').makeDropdown();
	});
	</script>							
</form>

<?php if (isset($_GET['doSearch'])) : ?>
    <?php $weekDays = array(
        0 => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Sunday'),
        1 => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Monday'),
        2 => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Tuesday'),
        3 => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Wednesday'),
        4 => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Thursday'),
        5 => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Friday'),
        6 => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Saturday'),
    ); ?>

<script type="text/javascript">
	function redrawAllCharts(){
			drawChartPerMonth();
            drawChartByNickMonth();
	};

    // Define a plugin to provide data labels
    Chart.plugins.register({
        afterDatasetsDraw: function(chart, easing) {
            // To only draw at the end of animation, check for easing === 1
            var ctx = chart.ctx;
            chart.data.datasets.forEach(function (dataset, i) {
                var meta = chart.getDatasetMeta(i);
                if (!meta.hidden) {

                    var maxValue = 0;

                    if (chart.options.perc) {
                        meta.data.forEach(function(element, index) {
                            maxValue += dataset.data[index];
                        })
                    }

                    meta.data.forEach(function(element, index) {
                        // Draw the text in black, with the specified font

                        var dataString = dataset.data[index].toString();
                        if (dataString !== '0')
                        {
                            ctx.fillStyle = chart.data.datasets.length > 1 ? 'rgb(255, 255, 255)' : 'rgb(0, 0, 0)';
                            var fontSize = 11;
                            var fontStyle = 'normal';
                            var fontFamily = 'Arial';
                            ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);
                            // Just naively convert to string for now

                            // Make sure alignment settings are correct
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';

                            var padding = 0;

                            if (chart.data.datasets.length > 1) {
                                // Specify the shadow colour.
                                ctx.shadowColor = "black";
                                ctx.shadowOffsetX = 1;
                                ctx.shadowOffsetY = 1;
                                ctx.shadowBlur = 1;
                                if (typeof element.height == 'function') {
                                    padding = -element.height()/2-5;
                                }
                            }

                            var position = element.tooltipPosition();

                            if (chart.options.perc) {
                                ctx.fillText(dataString, position.x, position.y - (fontSize / 2) - padding);
                                ctx.fillText((parseInt(dataString)*100 / maxValue).toFixed(0)+"%", position.x, position.y - (fontSize / 2) - padding - 15);
                            } else {
                                ctx.fillText(dataString, position.x, position.y - (fontSize / 2) - padding);
                            }

                            ctx.shadowColor = "";
                            ctx.shadowOffsetX = 0;
                            ctx.shadowOffsetY = 0;
                            ctx.shadowBlur = 0;
                        }
                    });
                }
            });
        }
    });

    Chart.Legend.prototype.afterFit = function() {
        this.height = this.height + 10;
    };

    function drawBasicChart(data, id) {
        var ctx = document.getElementById(id).getContext("2d");

        var options = {
            responsive: true,
            legend: {
                display : false,
                position: 'top',
            },
            perc : true,
            layout: {
                padding: {
                    top: 20
                }
            },
            scales: {
                xAxes: [{
                    ticks: {
                        fontSize: 11,
                        stepSize: 1,
                        min: 0,
                        autoSkip: false
                    }
                }],
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
            title: {
                display: false
            }
        };

        var myBar = new Chart(ctx, {
            type: 'bar',
            data: data,
            options: options
        });
    }

    function drawChartByNickMonth() {
        <?php if (isset($nickgroupingdate) && !empty($nickgroupingdate)) : ?>
        <?php if (in_array('nickgroupingdate',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
        var barChartData = {
            labels: [<?php $key = 0; foreach ($nickgroupingdate as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),'\''.($monthUnix > 10 ? date($groupby,$monthUnix) : $weekDays[(int)$monthUnix] ).'\'';$key++; endforeach;?>],
            datasets: [
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Unique records');?>',
                    backgroundColor: '#3366cc',
                    borderColor: '#3366cc',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($nickgroupingdate as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['unique']; $key++; endforeach;?>]
                }
            ]
        };

        var ctx = document.getElementById("chart_nickgroupingdate").getContext("2d");
        var myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true,
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                layout: {
                    padding: {
                        top: 20
                    }
                },
                perc: true,
                scales: {
                    xAxes: [{
                        stacked: true,
                        ticks: {
                            fontSize: 11,
                            stepSize: 1,
                            min: 0,
                            autoSkip: false
                        }
                    }],
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                title: {
                    display: false
                }
            }
        });
        <?php endif; ?>
        <?php endif; ?>

        <?php if (isset($nickgroupingdatenick) && !empty($nickgroupingdatenick)) : ?>

        <?php if (in_array('nickgroupingdatenick',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
        var barChartData = {
            labels: [<?php $key = 0; foreach ($nickgroupingdatenick['labels'] as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),'\''.($monthUnix > 10 ? date($groupby,$monthUnix) : $weekDays[(int)$monthUnix] ).'\'';$key++; endforeach;?>],
            datasets: [
                <?php foreach ($nickgroupingdatenick['data'] as $data) : ?>
                {
                    data: [<?php echo implode(',',$data['data'])?>],
                    backgroundColor: [<?php echo implode(',',$data['color'])?>],
                    labels: [<?php echo implode(',',$data['nick'])?>]
                },
                <?php endforeach; ?>
            ]
        };

        var ctx = document.getElementById("chart_nickgroupingdatenick").getContext("2d");
        var myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true,
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var dataset = data.datasets[tooltipItem.datasetIndex];
                            var index = tooltipItem.index;
                            if (dataset.data[index] != 0) {
                                return  dataset.data[index] + ': ' + (dataset.labels[index] == '' ? 'Unknown' : dataset.labels[index]);
                            }
                        }
                    }
                },
                legend: {
                    display: false,
                },
                layout: {
                    padding: {
                        top: 20
                    }
                },
                scales: {
                    xAxes: [{
                        <?php ($input->group_chart_type == 'stacked_bar') ? print 'stacked: true,' : '' ?>
                        ticks: {
                            fontSize: 11,
                            stepSize: 1,
                            min: 0,
                            autoSkip: false
                        }
                    }],
                    yAxes: [{
                        <?php ($input->group_chart_type == 'stacked_bar') ? print 'stacked: true,' : '' ?>
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                title: {
                    display: false
                }
            }
        });
        <?php endif; ?>
        <?php endif; ?>
    }

	function drawChartPerMonth() {


        <?php if (in_array('active',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
        var barChartData = {
            labels: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),'\''. ($monthUnix > 10 ? date($groupby,$monthUnix) : $weekDays[(int)$monthUnix] ).'\'';$key++; endforeach;?>],
            datasets: [
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Active');?>',
                    backgroundColor: '#dc3912',
                    borderColor: '#dc3912',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['active']; $key++; endforeach;?>]
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Operators');?>',
                    backgroundColor: '#ff9900',
                    borderColor: '#ff9900',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['operators']; $key++; endforeach;?>]
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Pending');?>',
                    backgroundColor: '#109618',
                    borderColor: '#109618',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['pending']; $key++; endforeach;?>]
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Closed');?>',
                    backgroundColor: '#3366cc',
                    borderColor: '#3366cc',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['closed']; $key++; endforeach;?>]
                },
            ]
        };

        var ctx = document.getElementById("chart_div_per_month").getContext("2d");
        var myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true,
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                layout: {
                    padding: {
                        top: 20
                    }
                },
                scales: {
                    xAxes: [{
                        stacked: true,
                        ticks: {
                            fontSize: 11,
                            stepSize: 1,
                            min: 0,
                            autoSkip: false
                        }
                    }],
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                title: {
                    display: false
                }
            }
        });
        <?php endif; ?>

        <?php if (in_array('unanswered',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
        var barChartData = {
            labels: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : ; echo ($key > 0 ? ',' : ''),'\''.($monthUnix > 10 ? date($groupby,$monthUnix) : $weekDays[(int)$monthUnix] ).'\'';$key++; endforeach;?>],
            datasets: [{
                backgroundColor: '#36c',
                borderColor: '#36c',
                borderWidth: 1,
                data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['unanswered']; $key++; endforeach;?>]
            }]
        };
        drawBasicChart(barChartData,'chart_div_per_month_unanswered');
        <?php endif; ?>

        <?php if (in_array('waitmonth',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
        var barChartData = {
            labels: [<?php $key = 0; foreach ($numberOfChatsPerWaitTimeMonth as $monthUnix => $data) : ; echo ($key > 0 ? ',' : ''),'\''.($monthUnix > 10 ? date($groupby,$monthUnix) : $weekDays[(int)$monthUnix] ).'\'';$key++; endforeach;?>],
            datasets: [{
                backgroundColor: '#36c',
                borderColor: '#36c',
                borderWidth: 1,
                data: [<?php $key = 0; foreach ($numberOfChatsPerWaitTimeMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data; $key++; endforeach;?>]
            }]
        };
        drawBasicChart(barChartData,'chart_div_per_month_wait_time');
        <?php endif; ?>

        <?php if (in_array('total_chats',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
        var barChartData = {
            labels: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : ; echo ($key > 0 ? ',' : ''),'\''.($monthUnix > 10 ? date($groupby,$monthUnix) : $weekDays[(int)$monthUnix] ).'\'';$key++; endforeach;?>],
            datasets: [{
                backgroundColor: '#36c',
                borderColor: '#36c',
                borderWidth: 1,
                data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['total_chats']; $key++; endforeach;?>]
            }]
        };
        drawBasicChart(barChartData,'chart_div_per_total');
        <?php endif; ?>

        <?php if (in_array('proactivevsdefault',is_array($input->chart_type) ? $input->chart_type : array())) : ?>

        var barChartData = {
            labels: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),'\''.($monthUnix > 10 ? date($groupby,$monthUnix) : $weekDays[(int)$monthUnix] ).'\'';$key++; endforeach;?>],
            datasets: [
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Proactive');?>',
                    backgroundColor: '#dc3912',
                    borderColor: '#dc3912',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['chatinitproact']; $key++; endforeach;?>]
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Manual invitation');?>',
                    backgroundColor: '#cca333',
                    borderColor: '#cca333',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['chatinitmanualinv']; $key++; endforeach;?>]
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Visitors initiated');?>',
                    backgroundColor: '#36c',
                    borderColor: '#36c',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['chatinitdefault']; $key++; endforeach;?>]
                }
            ]
        };
        var ctx = document.getElementById("chart_type_div_per_month").getContext("2d");
        var myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true,
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                layout: {
                    padding: {
                        top: 20
                    }
                },
                scales: {
                    xAxes: [{
                        stacked: true,
                        ticks: {
                            fontSize: 11,
                            stepSize: 1,
                            min: 0,
                            autoSkip: false
                        }
                    }
                    ],
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                title: {
                    display: false
                }
            }
        });
        <?php endif; ?>

        <?php if (in_array('by_channel',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
        var barChartData = {
            labels: [<?php $key = 0; foreach ($by_channel as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),'\''.($monthUnix > 10 ? date($groupby,$monthUnix) : $weekDays[(int)$monthUnix]).'\'';$key++; endforeach;?>],
            datasets: <?php
            $items = [];
            foreach (array_keys(current($by_channel)) as $incomingId) {
                $webHook = erLhcoreClassModelChatIncomingWebhook::fetch($incomingId);
                $label = $webHook instanceof erLhcoreClassModelChatIncomingWebhook ? $webHook->name : $incomingId;
                if (empty($label)) {
                    $label = 'Chat';
                }
                $itemData = [
                    'label' => $label,
                    'backgroundColor' => erLhcoreClassChatStatistic::colorFromString($label),
                    'borderColor' =>  erLhcoreClassChatStatistic::colorFromString($label),
                    'borderWidth' => 1,
                    'data' => []
                ];
                foreach ($by_channel as $dataItem) {
                    $itemData['data'][] = (int)$dataItem[$incomingId];
                }
                $items[] = $itemData;
            }
            echo json_encode($items);
            ?>
        };
        var ctx = document.getElementById("chart_type_div_by_channel").getContext("2d");
        var myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true,
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                layout: {
                    padding: {
                        top: 20
                    }
                },
                scales: {
                    xAxes: [{
                        stacked: true,
                        ticks: {
                            fontSize: 11,
                            stepSize: 1,
                            min: 0,
                            autoSkip: false
                        }
                    }
                    ],
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                title: {
                    display: false
                }
            }
        });
        <?php endif; ?>

        <?php if (in_array('msgdelop',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
        var barChartData = {
            labels: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),'\''.($monthUnix > 10 ? date($groupby,$monthUnix) : $weekDays[(int)$monthUnix]).'\'';$key++; endforeach;?>],
            datasets: [
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Pending');?>',
                    backgroundColor: '#a2a2a2',
                    borderColor: '#a2a2a2',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),(isset($data['msgdelop'][0]) ? $data['msgdelop'][0] : 0) ; $key++; endforeach;?>]
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Sent');?>',
                    backgroundColor: '#084f8b',
                    borderColor: '#084f8b',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),(isset($data['msgdelop'][1]) ? $data['msgdelop'][1] : 0); $key++; endforeach;?>]
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Delivered');?>',
                    backgroundColor: '#ff9900',
                    borderColor: '#ff9900',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),(isset($data['msgdelop'][2]) ? $data['msgdelop'][2] : 0); $key++; endforeach;?>]
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Read');?>',
                    backgroundColor: '#28ad0c',
                    borderColor: '#28ad0c',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),(isset($data['msgdelop'][3]) ? $data['msgdelop'][3] : 0); $key++; endforeach;?>]
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Rejected');?>',
                    backgroundColor: '#ed1148',
                    borderColor: '#ed1148',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),(isset($data['msgdelop'][4]) ? $data['msgdelop'][4] : 0); $key++; endforeach;?>]
                }
            ]
        };

        var ctx = document.getElementById("chart_type_div_msg_del_op").getContext("2d");
        var myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true,
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                layout: {
                    padding: {
                        top: 20
                    }
                },
                scales: {
                    xAxes: [{
                        stacked: true,
                        ticks: {
                            fontSize: 11,
                            stepSize: 1,
                            min: 0,
                            autoSkip: false
                        }
                    }],
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                title: {
                    display: false
                }
            }
        });
        <?php endif; ?>

        <?php if (in_array('msgdelbot',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
        var barChartData = {
            labels: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),'\''.($monthUnix > 10 ? date($groupby,$monthUnix) : $weekDays[(int)$monthUnix]).'\'';$key++; endforeach;?>],
            datasets: [
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Pending');?>',
                    backgroundColor: '#a2a2a2',
                    borderColor: '#a2a2a2',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),(isset($data['msgdelbot'][0]) ? $data['msgdelbot'][0] : 0) ; $key++; endforeach;?>]
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Sent');?>',
                    backgroundColor: '#084f8b',
                    borderColor: '#084f8b',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),(isset($data['msgdelbot'][1]) ? $data['msgdelbot'][1] : 0); $key++; endforeach;?>]
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Delivered');?>',
                    backgroundColor: '#ff9900',
                    borderColor: '#ff9900',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),(isset($data['msgdelbot'][2]) ? $data['msgdelbot'][2] : 0); $key++; endforeach;?>]
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Read');?>',
                    backgroundColor: '#28ad0c',
                    borderColor: '#28ad0c',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),(isset($data['msgdelbot'][3]) ? $data['msgdelbot'][3] : 0); $key++; endforeach;?>]
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Rejected');?>',
                    backgroundColor: '#ed1148',
                    borderColor: '#ed1148',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),(isset($data['msgdelbot'][4]) ? $data['msgdelbot'][4] : 0); $key++; endforeach;?>]
                }
            ]
        };

        var ctx = document.getElementById("chart_type_div_msg_del_bot").getContext("2d");
        var myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true,
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                layout: {
                    padding: {
                        top: 20
                    }
                },
                scales: {
                    xAxes: [{
                        stacked: true,
                        ticks: {
                            fontSize: 11,
                            stepSize: 1,
                            min: 0,
                            autoSkip: false
                        }
                    }],
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                title: {
                    display: false
                }
            }
        });
        <?php endif; ?>


        <?php if (in_array('msgtype',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
        var barChartData = {
            labels: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),'\''.($monthUnix > 10 ? date($groupby,$monthUnix) : $weekDays[(int)$monthUnix]).'\'';$key++; endforeach;?>],
            datasets: [
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Visitors');?>',
                    backgroundColor: '#36c',
                    borderColor: '#36c',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['msg_user']; $key++; endforeach;?>]
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Operators');?>',
                    backgroundColor: '#dc3912',
                    borderColor: '#dc3912',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['msg_operator']; $key++; endforeach;?>]
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','System');?>',
                    backgroundColor: '#ff9900',
                    borderColor: '#ff9900',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['msg_system']; $key++; endforeach;?>]
                },
                {
                    label: '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Bot');?>',
                    backgroundColor: '#85ff79',
                    borderColor: '#85ff79',
                    borderWidth: 1,
                    data: [<?php $key = 0; foreach ($numberOfChatsPerMonth as $monthUnix => $data) : echo ($key > 0 ? ',' : ''),$data['msg_bot']; $key++; endforeach;?>]
                }
            ]
        };
        var ctx = document.getElementById("chart_type_div_msg_type").getContext("2d");
        var myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true,
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                layout: {
                    padding: {
                        top: 20
                    }
                },
                scales: {
                    xAxes: [{
                        stacked: true,
                        ticks: {
                            fontSize: 11,
                            stepSize: 1,
                            min: 0,
                            autoSkip: false
                        }
                    }
                    ],
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                title: {
                    display: false
                }
            }
        });
        <?php endif; ?>
	}
	

	$( document ).ready(function() {
		redrawAllCharts();
	});
				
</script> 


<?php if (in_array('active',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
<hr>
<h5><a class="csv-export" data-scope="cs_active" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Download CSV')?>"><i class="material-icons me-0">file_download</i></a> <?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/chats_number_by_statuses.tpl.php'));?></h5>
<canvas id="chart_div_per_month"></canvas>
<?php endif; ?>

<?php if (in_array('total_chats',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
<hr>
<h5><a class="csv-export" data-scope="cs_total_chats" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Download CSV')?>"><i class="material-icons me-0">file_download</i></a><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/chats_number_by_total.tpl.php'));?></h5>
<canvas id="chart_div_per_total"></canvas>
<?php endif; ?>

<?php if (in_array('nickgroupingdate',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
<hr>
<h5><a class="csv-export" data-scope="cs_nickgroupingdate" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Download CSV')?>"><i class="material-icons me-0">file_download</i></a><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/nickgroupingdate.tpl.php'));?></h5>
<canvas id="chart_nickgroupingdate"></canvas>
<?php endif; ?>

<?php if (in_array('nickgroupingdatenick',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
<hr>
<h5>
<a class="csv-export" data-scope="cs_nickgroupingdatenick" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Download CSV')?>"><i class="material-icons me-0">file_download</i></a><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/nickgroupingdatenick.tpl.php'));?></h5>
<canvas id="chart_nickgroupingdatenick"></canvas>
<?php endif; ?>

<?php if (in_array('proactivevsdefault',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
<hr>
<h5><a class="csv-export" data-scope="cs_proactivevsdefault" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Download CSV')?>"><i class="material-icons me-0">file_download</i></a><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/proactive_chats_number_vs_visitors_initiated.tpl.php'));?></h5>
<canvas id="chart_type_div_per_month"></canvas>
<?php endif; ?>

<?php if (in_array('msgtype',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
<hr>
<h5><a class="csv-export" data-scope="cs_msgtype" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Download CSV')?>"><i class="material-icons me-0">file_download</i></a><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/messages_types.tpl.php'));?></h5>
<canvas id="chart_type_div_msg_type"></canvas>
<?php endif; ?>

<?php if (in_array('msgdelop',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
<hr>
<h5><a class="csv-export" data-scope="msgdelop" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Download CSV')?>"><i class="material-icons me-0">file_download</i></a><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/messages_del_op.tpl.php'));?></h5>
<canvas id="chart_type_div_msg_del_op"></canvas>
<?php endif;?>

<?php if (in_array('msgdelbot',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
<hr>
<h5><a class="csv-export" data-scope="msgdelbot" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Download CSV')?>"><i class="material-icons me-0">file_download</i></a><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/messages_del_bot.tpl.php'));?></h5>
<canvas id="chart_type_div_msg_del_bot"></canvas>
<?php endif;?>

<?php if (in_array('by_channel',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
<hr>
<h5><a class="csv-export" data-scope="cs_by_channel" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Download CSV')?>"><i class="material-icons me-0">file_download</i></a><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/by_channel.tpl.php'));?></h5>
<canvas id="chart_type_div_by_channel"></canvas>
<?php endif; ?>

<?php if (in_array('waitmonth',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
<hr>
<h5><a class="csv-export" data-scope="cs_waitmonth" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Download CSV')?>"><i class="material-icons me-0">file_download</i></a> <?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/avg_wait_time_in_seconds_max_10_mininutes.tpl.php'));?></h5>
<canvas id="chart_div_per_month_wait_time"></canvas>
<?php endif; ?>

<?php if (in_array('unanswered',is_array($input->chart_type) ? $input->chart_type : array())) : ?>
<hr>
<h5><a class="csv-export" data-scope="cs_unanswered" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Download CSV')?>"><i class="material-icons me-0">file_download</i></a><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/unanswered_chats_numbers.tpl.php'));?></h5>
<canvas id="chart_div_per_month_unanswered"></canvas>
<?php endif; ?>

<script>
    $(".csv-export").click(function(event) {
        event.preventDefault();
        $('#id-report-type').val($(this).attr('data-scope'));
        $('#form-statistic-action').submit();
    })
</script>

<?php else : ?>
<br/>
<div class="alert alert-info">
  <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Please choose statistic parameters first!');?>
</div>
<?php endif; ?>