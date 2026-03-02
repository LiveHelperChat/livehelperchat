<form action="<?php echo $input->form_action?>" method="get" name="SearchFormRight" ng-non-bindable autocomplete="off">

	<input type="hidden" name="doSearch" value="1">

	<div class="row">
        <div class="col-md-1">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chat ID');?></label>
                <input type="text" class="form-control form-control-sm" placeholder="<?php echo htmlspecialchars("<id>[,<id>]");?>" name="chat_id" value="<?php echo htmlspecialchars($input->chat_id)?>" />
            </div>
        </div>
		<div class="col-md-1">
		   <div class="form-group">
			<label><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/parts/nick_title.tpl.php')); ?></label>
			<input type="text" class="form-control form-control-sm" name="nick" value="<?php echo htmlspecialchars($input->nick)?>" />
		   </div>
		</div>
		<div class="col-md-1">
		  <div class="form-group">
			<label><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/parts/email_title.tpl.php')); ?></label>
			<input type="text" class="form-control form-control-sm" name="email" value="<?php echo htmlspecialchars($input->email)?>" />
		  </div>
		</div>
        <div class="col-md-1">
		  <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Phone');?></label>
			<input type="text" class="form-control form-control-sm" name="phone" value="<?php echo htmlspecialchars((string)$input->phone)?>" />
		  </div>
		</div>
		
		<div class="col-md-2">
		  <div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                  'input_name'     => 'department_ids[]',
                  'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department'),
                  'selected_id'    => $input->department_ids,
                  'ajax'           => 'deps',
                  'css_class'      => 'form-control',
                  'display_name'   => 'name',
                  'list_function_params' => array_merge(['sort' => '`name` ASC', 'limit' => 50],erLhcoreClassUserDep::conditionalDepartmentFilter()),
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
                    'list_function_params' => array_merge(['sort' => '`name` ASC', 'limit' => false],erLhcoreClassUserDep::conditionalDepartmentGroupFilter()),
                    'list_function'  => 'erLhcoreClassModelDepartamentGroup::getList'
                )); ?>
            </div>
        </div>

        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/parts/user_title.tpl.php')); ?>
        <div class="col-md-2">
		   <div class="form-group">
			<label><?php echo $userTitle['user'];?></label>
               <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                   'input_name'     => 'user_ids[]',
                   'optional_field' => $userTitle['user_select'],
                   'selected_id'    => $input->user_ids,
                   'css_class'      => 'form-control',
                   'display_name'   => 'name_official',
                   'ajax'           => 'users',
                   'list_function_params' => array_merge(erLhcoreClassGroupUser::getConditionalUserFilter(),array('sort' => '`name` ASC', 'limit' => 50)),
                   'list_function'  => 'erLhcoreClassModelUser::getUserList',
               )); ?>
		  </div>
		</div>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/parts/user_group_title.tpl.php')); ?>
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo $userGroupTitle['user_group'];?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'group_ids[]',
                    'optional_field' => $userGroupTitle['user_group_select'],
                    'selected_id'    => $input->group_ids,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name',
                    'list_function_params' => array_merge(array('sort' => '`name` ASC'),erLhcoreClassGroupUser::getConditionalUserFilter(false, true)),
                    'list_function'  => 'erLhcoreClassModelGroup::getList'
                )); ?>
            </div>
        </div>

        </div>

    <div class="row">
        <div class="col-12 pb-2">
            <a href="#" onclick="$('#advanced-search').toggle()"><span class="material-icons">search</span>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Advanced search');?></a>
        </div>
        <div class="col-12" id="advanced-search" style="display: none">
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range from');?>
                            <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/parts/date_picker_range.tpl.php')); ?>
                        </label>
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
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute to');?> <small>[<?php echo date('H:i:s')?>]</small></label>
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
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Wait time');?></label>
                        <div class="row">
                            <div class="col-6">
                                <select class="form-control form-control-sm" name="wait_time_from">
                                    <option><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','More than');?></option>
                                    <option value="0" <?php $input->wait_time_from === 0 ? print 'selected="selected"' : ''?>>0 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="5" <?php $input->wait_time_from === 5 ? print 'selected="selected"' : ''?>>5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="10" <?php $input->wait_time_from === 10 ? print 'selected="selected"' : ''?>>10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="15" <?php $input->wait_time_from === 15 ? print 'selected="selected"' : ''?>>15 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="20" <?php $input->wait_time_from === 20 ? print 'selected="selected"' : ''?>>20 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="30" <?php $input->wait_time_from === 30 ? print 'selected="selected"' : ''?>>30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="40" <?php $input->wait_time_from === 40 ? print 'selected="selected"' : ''?>>40 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="50" <?php $input->wait_time_from === 50 ? print 'selected="selected"' : ''?>>50 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="60" <?php $input->wait_time_from === 60 ? print 'selected="selected"' : ''?>>60 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="90" <?php $input->wait_time_from === 90 ? print 'selected="selected"' : ''?>>90 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>

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
                                    <option><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Less than');?></option>
                                    <option value="0" <?php $input->wait_time_till === 0 ? print 'selected="selected"' : ''?>>0 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="5" <?php $input->wait_time_till === 5 ? print 'selected="selected"' : ''?>>5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="10" <?php $input->wait_time_till === 10 ? print 'selected="selected"' : ''?>>10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="15" <?php $input->wait_time_till === 15 ? print 'selected="selected"' : ''?>>15 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="20" <?php $input->wait_time_till === 20 ? print 'selected="selected"' : ''?>>20 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="30" <?php $input->wait_time_till === 30 ? print 'selected="selected"' : ''?>>30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="40" <?php $input->wait_time_till === 40 ? print 'selected="selected"' : ''?>>40 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="50" <?php $input->wait_time_till === 50 ? print 'selected="selected"' : ''?>>50 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="60" <?php $input->wait_time_till === 60 ? print 'selected="selected"' : ''?>>60 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="90" <?php $input->wait_time_till === 90 ? print 'selected="selected"' : ''?>>90 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>

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
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Visitor ID');?></label>
                        <input type="text" class="form-control form-control-sm" placeholder="<?php echo htmlspecialchars("<id>[,<id>]");?>" name="visitor_id" value="<?php echo htmlspecialchars((string)$input->visitor_id)?>" />
                    </div>
                </div>
                <div class="col-md-2">
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
                <div class="col-md-1">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Country');?></label>
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
            </div>
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/parts/subject_title.tpl.php')); ?></label>
                        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/parts/subject_filter.tpl.php')); ?>
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
                            'list_function_params'  => ['sort' => '`name` ASC'],
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
                            'list_function_params'  => ['sort' => '`name` ASC'],
                            'list_function'  => 'erLhcoreClassModelGenericBotBot::getList'
                        )); ?>
                    </div>
                </div>

                <div class="col-md-1">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Channel');?></label>
                        <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                            'input_name'     => 'iwh_ids[]',
                            'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose a channel'),
                            'selected_id'    => $input->iwh_ids,
                            'css_class'      => 'form-control',
                            'display_name'   => 'name',
                            'list_function'  => 'erLhcoreClassModelChatIncomingWebhook::getList'
                        )); ?>
                    </div>
                </div>

                <div class="col-md-1">
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

                <div class="col-md-1">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Has unread operator messages');?></label>
                    <div class="form-group">
                        <select name="has_unread_op_messages" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Any');?></option>
                            <option value="1" <?php $input->has_unread_op_messages === 1 ? print 'selected="selected"' : '' ?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Yes');?></option>
                            <option value="0" <?php $input->has_unread_op_messages === 0 ? print 'selected="selected"' : '' ?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','No');?></option>
                        </select>
                    </div>
                </div>

                <div class="col-md-1">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Theme');?></label>
                        <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                            'input_name'     => 'theme_ids[]',
                            'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose a theme'),
                            'selected_id'    => $input->theme_ids,
                            'css_class'      => 'form-control',
                            'display_name'   => 'name',
                            'list_function'  => 'erLhAbstractModelWidgetTheme::getList'
                        )); ?>
                    </div>
                </div>

                <div class="col-md-1">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','IP');?></label>
                        <input type="text" class="form-control form-control-sm" name="ip" value="<?php echo htmlspecialchars($input->ip)?>" />
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','First response time (agent)');?> <a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/chat_frt'});" class="material-icons text-muted">help</a></label>
                        <div class="row">
                            <div class="col-6">
                                <select class="form-control form-control-sm" name="frt_from">
                                    <option><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','More than');?></option>
                                    <option value="0" <?php $input->frt_from === 0 ? print 'selected="selected"' : ''?>>0 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="5" <?php $input->frt_from === 5 ? print 'selected="selected"' : ''?>>5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="10" <?php $input->frt_from === 10 ? print 'selected="selected"' : ''?>>10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="15" <?php $input->frt_from === 15 ? print 'selected="selected"' : ''?>>15 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="20" <?php $input->frt_from === 20 ? print 'selected="selected"' : ''?>>20 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="30" <?php $input->frt_from === 30 ? print 'selected="selected"' : ''?>>30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="40" <?php $input->frt_from === 40 ? print 'selected="selected"' : ''?>>40 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="50" <?php $input->frt_from === 50 ? print 'selected="selected"' : ''?>>50 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="60" <?php $input->frt_from === 60 ? print 'selected="selected"' : ''?>>60 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="90" <?php $input->frt_from === 90 ? print 'selected="selected"' : ''?>>90 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>

                                    <?php for ($i = 2; $i < 5; $i++) : ?>
                                        <option value="<?php echo $i*60?>" <?php $input->frt_from === $i*60 ? print 'selected="selected"' : ''?>><?php echo  $i?> m.</option>
                                    <?php endfor ?>

                                    <?php for ($i = 2; $i < 13; $i++) : ?>
                                        <option value="<?php echo $i*5*60?>" <?php $i*60*5 === $input->frt_from ? print 'selected="selected"' : ''?>><?php echo $i*5?> m.</option>
                                    <?php endfor ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <select class="form-control form-control-sm" name="frt_till">
                                    <option><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Less than');?></option>
                                    <option value="0" <?php $input->frt_till === 0 ? print 'selected="selected"' : ''?>>0 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="5" <?php $input->frt_till === 5 ? print 'selected="selected"' : ''?>>5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="10" <?php $input->frt_till === 10 ? print 'selected="selected"' : ''?>>10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="15" <?php $input->frt_till === 15 ? print 'selected="selected"' : ''?>>15 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="20" <?php $input->frt_till === 20 ? print 'selected="selected"' : ''?>>20 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="30" <?php $input->frt_till === 30 ? print 'selected="selected"' : ''?>>30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="40" <?php $input->frt_till === 40 ? print 'selected="selected"' : ''?>>40 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="50" <?php $input->frt_till === 50 ? print 'selected="selected"' : ''?>>50 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="60" <?php $input->frt_till === 60 ? print 'selected="selected"' : ''?>>60 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="90" <?php $input->frt_till === 90 ? print 'selected="selected"' : ''?>>90 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>

                                    <?php for ($i = 2; $i < 5; $i++) : ?>
                                        <option value="<?php echo $i*60?>" <?php $input->frt_till === $i*60 ? print 'selected="selected"' : ''?>><?php echo  $i?> m.</option>
                                    <?php endfor ?>

                                    <?php for ($i = 2; $i < 13; $i++) : ?>
                                        <option value="<?php echo $i*60*5?>" <?php $i*60*5 === $input->frt_till ? print 'selected="selected"' : ''?> ><?php echo $i*5?> m.</option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Max response time (agent)');?> <a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/chat_mart'});" class="material-icons text-muted">help</a></label>
                        <div class="row">
                            <div class="col-6">
                                <select class="form-control form-control-sm" name="mart_from">
                                    <option><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','More than');?></option>
                                    <option value="0" <?php $input->mart_from === 0 ? print 'selected="selected"' : ''?>>0 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="5" <?php $input->mart_from === 5 ? print 'selected="selected"' : ''?>>5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="10" <?php $input->mart_from === 10 ? print 'selected="selected"' : ''?>>10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="15" <?php $input->mart_from === 15 ? print 'selected="selected"' : ''?>>15 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="20" <?php $input->mart_from === 20 ? print 'selected="selected"' : ''?>>20 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="30" <?php $input->mart_from === 30 ? print 'selected="selected"' : ''?>>30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="40" <?php $input->mart_from === 40 ? print 'selected="selected"' : ''?>>40 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="50" <?php $input->mart_from === 50 ? print 'selected="selected"' : ''?>>50 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="60" <?php $input->mart_from === 60 ? print 'selected="selected"' : ''?>>60 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="90" <?php $input->mart_from === 90 ? print 'selected="selected"' : ''?>>90 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>

                                    <?php for ($i = 2; $i < 5; $i++) : ?>
                                        <option value="<?php echo $i*60?>" <?php $input->mart_from === $i*60 ? print 'selected="selected"' : ''?>><?php echo  $i?> m.</option>
                                    <?php endfor ?>

                                    <?php for ($i = 2; $i < 13; $i++) : ?>
                                        <option value="<?php echo $i*5*60?>" <?php $i*60*5 === $input->mart_from ? print 'selected="selected"' : ''?>><?php echo $i*5?> m.</option>
                                    <?php endfor ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <select class="form-control form-control-sm" name="mart_till">
                                    <option><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Less than');?></option>
                                    <option value="0" <?php $input->mart_till === 0 ? print 'selected="selected"' : ''?>>0 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="5" <?php $input->mart_till === 5 ? print 'selected="selected"' : ''?>>5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="10" <?php $input->mart_till === 10 ? print 'selected="selected"' : ''?>>10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="15" <?php $input->mart_till === 15 ? print 'selected="selected"' : ''?>>15 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="20" <?php $input->mart_till === 20 ? print 'selected="selected"' : ''?>>20 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="30" <?php $input->mart_till === 30 ? print 'selected="selected"' : ''?>>30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="40" <?php $input->mart_till === 40 ? print 'selected="selected"' : ''?>>40 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="50" <?php $input->mart_till === 50 ? print 'selected="selected"' : ''?>>50 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="60" <?php $input->mart_till === 60 ? print 'selected="selected"' : ''?>>60 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="90" <?php $input->mart_till === 90 ? print 'selected="selected"' : ''?>>90 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>

                                    <?php for ($i = 2; $i < 5; $i++) : ?>
                                        <option value="<?php echo $i*60?>" <?php $input->mart_till === $i*60 ? print 'selected="selected"' : ''?>><?php echo  $i?> m.</option>
                                    <?php endfor ?>

                                    <?php for ($i = 2; $i < 13; $i++) : ?>
                                        <option value="<?php echo $i*60*5?>" <?php $i*60*5 === $input->mart_till ? print 'selected="selected"' : ''?> ><?php echo $i*5?> m.</option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Average response time (agent)');?> <a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/chat_aart'});" class="material-icons text-muted">help</a></label>
                        <div class="row">
                            <div class="col-6">
                                <select class="form-control form-control-sm" name="aart_from">
                                    <option><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','More than');?></option>
                                    <option value="0" <?php $input->aart_from === 0 ? print 'selected="selected"' : ''?>>0 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="5" <?php $input->aart_from === 5 ? print 'selected="selected"' : ''?>>5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="10" <?php $input->aart_from === 10 ? print 'selected="selected"' : ''?>>10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="15" <?php $input->aart_from === 15 ? print 'selected="selected"' : ''?>>15 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="20" <?php $input->aart_from === 20 ? print 'selected="selected"' : ''?>>20 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="30" <?php $input->aart_from === 30 ? print 'selected="selected"' : ''?>>30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="40" <?php $input->aart_from === 40 ? print 'selected="selected"' : ''?>>40 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="50" <?php $input->aart_from === 50 ? print 'selected="selected"' : ''?>>50 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="60" <?php $input->aart_from === 60 ? print 'selected="selected"' : ''?>>60 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="90" <?php $input->aart_from === 90 ? print 'selected="selected"' : ''?>>90 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>

                                    <?php for ($i = 2; $i < 5; $i++) : ?>
                                        <option value="<?php echo $i*60?>" <?php $input->aart_from === $i*60 ? print 'selected="selected"' : ''?>><?php echo  $i?> m.</option>
                                    <?php endfor ?>

                                    <?php for ($i = 2; $i < 13; $i++) : ?>
                                        <option value="<?php echo $i*5*60?>" <?php $i*60*5 === $input->aart_from ? print 'selected="selected"' : ''?>><?php echo $i*5?> m.</option>
                                    <?php endfor ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <select class="form-control form-control-sm" name="aart_till">
                                    <option><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Less than');?></option>
                                    <option value="0" <?php $input->aart_till === 0 ? print 'selected="selected"' : ''?>>0 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="5" <?php $input->aart_till === 5 ? print 'selected="selected"' : ''?>>5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="10" <?php $input->aart_till === 10 ? print 'selected="selected"' : ''?>>10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="15" <?php $input->aart_till === 15 ? print 'selected="selected"' : ''?>>15 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="20" <?php $input->aart_till === 20 ? print 'selected="selected"' : ''?>>20 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="30" <?php $input->aart_till === 30 ? print 'selected="selected"' : ''?>>30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="40" <?php $input->aart_till === 40 ? print 'selected="selected"' : ''?>>40 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="50" <?php $input->aart_till === 50 ? print 'selected="selected"' : ''?>>50 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="60" <?php $input->aart_till === 60 ? print 'selected="selected"' : ''?>>60 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>
                                    <option value="90" <?php $input->aart_till === 90 ? print 'selected="selected"' : ''?>>90 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','seconds');?></option>

                                    <?php for ($i = 2; $i < 5; $i++) : ?>
                                        <option value="<?php echo $i*60?>" <?php $input->aart_till === $i*60 ? print 'selected="selected"' : ''?>><?php echo  $i?> m.</option>
                                    <?php endfor ?>

                                    <?php for ($i = 2; $i < 13; $i++) : ?>
                                        <option value="<?php echo $i*60*5?>" <?php $i*60*5 === $input->aart_till ? print 'selected="selected"' : ''?> ><?php echo $i*5?> m.</option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chat Priority');?></label>
                        <div class="row">
                            <div class="col-6">
                                <input type="number" class="form-control form-control-sm" name="priority_from" value="<?php echo htmlspecialchars((string)$input->priority_from)?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','From');?>" step="1" />
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control form-control-sm"  name="priority_till"  value="<?php echo htmlspecialchars((string)$input->priority_till)?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','To');?>" step="1" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Message counts, Operators, Visitors, Bot');?></label>
                        <div class="row">
                            <div class="col-4">
                                <input type="number" min="0" step="1" class="form-control form-control-sm" name="op_msg_count" value="<?php echo htmlspecialchars((string)$input->op_msg_count)?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Min operator');?> &gt;=" />
                            </div>
                            <div class="col-4">
                                <input type="number" min="0" step="1" class="form-control form-control-sm" name="vi_msg_count" value="<?php echo htmlspecialchars((string)$input->vi_msg_count)?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Min visitor');?> &gt;=" />
                            </div>
                            <div class="col-4">
                                <input type="number" min="0" step="1" class="form-control form-control-sm" name="bot_msg_count" value="<?php echo htmlspecialchars((string)$input->bot_msg_count)?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Min bot');?> &gt;=" />
                            </div>                   
                        </div>
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

                <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/parts/abandoned_chat.tpl.php'));?>

                <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/parts/dropped_chat.tpl.php'));?>

                <div class="col-2"><label class="col-form-label"><input type="checkbox" name="proactive_chat" value="<?php echo erLhcoreClassModelChat::CHAT_INITIATOR_PROACTIVE ?>" <?php $input->proactive_chat == true ? print 'checked="checked"' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Proactive chat')?></label></div>
                <div class="col-2"><label class="col-form-label"><input type="checkbox" name="not_invitation" value="0" <?php $input->not_invitation === 0 ? print 'checked="checked"' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Not automatic invitation')?></label></div>
                <div class="col-2"><label class="col-form-label"><input type="checkbox" name="transfer_happened" value="1" <?php $input->transfer_happened == true ? print 'checked="checked"' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Transfer happened')?></label></div>
                <div class="col-2"><label class="col-form-label"><input type="checkbox" name="cls_time" value="1" <?php $input->cls_time == true ? print 'checked="checked"' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search by close time')?></label></div>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/search_panel_multiinclude.tpl.php'));?>
            </div>
        </div>
	</div>

    <div class="row mb-2">
        <div class="col-12">

            <div class="btn-group me-2" role="group" aria-label="...">
                <button type="submit" class="btn btn-primary btn-sm no-wrap" name="doSearch"><span class="material-icons">search</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?></button>
                <select class="form-control form-control-sm border-secondary rounded-0 border-end-0" name="sortby" onchange="this.form.submit()">
                    <option <?php if ($input->sortby == 'id_desc'|| $input->sortby == '') : ?>selected="selected"<?php endif; ?> value="id_desc"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Newest first (default)');?></option>
                    <option <?php if ($input->sortby == 'id_asc') : ?>selected="selected"<?php endif; ?> value="id_asc"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Oldest first');?></option>
                    <option <?php if ($input->sortby == 'lmt_dsc') : ?>selected="selected"<?php endif; ?> value="lmt_dsc"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Last interactions first');?></option>
                    <option <?php if ($input->sortby == 'lmt_asc') : ?>selected="selected"<?php endif; ?> value="lmt_asc"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Last interactions last');?></option>
                </select>

                <select name="ipp" class="form-control form-control-sm rounded-0 border-secondary rounded-end" onchange="this.form.submit()">
                    <option value="20" <?php if ($pages->items_per_page == 20) : ?>selected="selected"<?php endif; ?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','20 items per page');?></option>
                    <option value="40" <?php if ($pages->items_per_page == 40) : ?>selected="selected"<?php endif; ?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','40 items per page');?></option>
                    <option value="60" <?php if ($pages->items_per_page == 60) : ?>selected="selected"<?php endif; ?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','60 items per page');?></option>
                    <option value="80" <?php if ($pages->items_per_page == 80) : ?>selected="selected"<?php endif; ?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','80 items per page');?></option>
                    <option value="100" <?php if ($pages->items_per_page == 100) : ?>selected="selected"<?php endif; ?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','100 items per page');?></option>
                    <option value="150" <?php if ($pages->items_per_page == 150) : ?>selected="selected"<?php endif; ?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','150 items per page');?></option>
                    <option value="200" <?php if ($pages->items_per_page == 200) : ?>selected="selected"<?php endif; ?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','200 items per page');?></option>
                </select>

            </div>

            <div class="btn-group" role="group" aria-label="...">

                <?php $appendPrintExportURL = ''; if ($pages->items_total > 0) : ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/search_panel_append_print_multiinclude.tpl.php'));?>
                    <a target="_blank" class="btn btn-outline-secondary btn-sm" href="<?php echo $pages->serverURL?>/(print)/1?<?php echo $appendPrintExportURL?>"><span class="material-icons">print</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Print');?></a>

                <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','export_chats')) : ?>
                    <button type="button" onclick="return lhc.revealModal({'title' : 'Export', 'height':350, backdrop:true, 'url':'<?php echo $pages->serverURL?>/(export)/1?<?php echo $appendPrintExportURL?>'})" class="btn btn-outline-secondary btn-sm"><span class="material-icons">file_download</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Export')?> (<?php echo $pages->items_total?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','chats');?>)</button>
                <?php endif; ?>

                <?php endif; ?>


                <?php if ($pages->items_total > 0 || isset($_GET['doSearch'])) : ?>

                <?php if ($input->view > 0) : ?>
                    <input type="hidden" name="view" value="<?php echo $input->view?>" />
                <?php endif; ?>

                <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhviews','use_chat')) : ?>
                    <button type="button" onclick="return lhc.revealModal({'title' : 'Export', 'height':350, backdrop:true, 'url':'<?php echo $pages->serverURL?>/(export)/2?<?php echo $appendPrintExportURL?>'})" class="btn btn-outline-secondary btn-sm">
                        <span class="material-icons">saved_search</span>
                        <?php if ($input->view > 0) : ?>
                            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Update view')?>
                        <?php else : ?>
                            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Save as view')?>
                        <?php endif; ?>
                    </button>
                <?php endif; ?>

                 <a class="btn btn-outline-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>"><span class="material-icons">refresh</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Reset');?></a>

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