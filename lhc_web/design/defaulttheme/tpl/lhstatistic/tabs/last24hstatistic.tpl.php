<h2><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/last_24h_statistic.tpl.php'));?></h2>

<form action="" method="get" autocomplete="off" class="mb-2">

<div class="form-group">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range from to');?></label>
<div class="row">
	<div class="col-md-4">
		<input class="form-control" type="text" name="timefrom" id="id_timefrom" placeholder="E.g <?php echo date('Y-m-d',time()-24*3600)?>" value="<?php echo htmlspecialchars($input->timefrom == null ? date('Y-m-d',time()-24*3600) : $input->timefrom )?>" />
	</div>
	<div class="col-md-1">
	  <select class="form-control" name="timefrom_hours">
	      <?php for ($i = 0; $i < 24; $i++) : ?>
	          <option value="<?php echo $i?>" <?php if ((isset($input->timefrom_hours) && $input->timefrom_hours == $i) || (!isset($input->timefrom_hours) && $i == date('H',time()-24*3600))) : ?>selected="selected"<?php endif;?> ><?php echo $i?> h.</option>
	      <?php endfor;?>
	  </select>
	</div>
	<div class="col-md-1">
	  <select class="form-control" name="timefrom_minutes">
	      <?php for ($i = 0; $i < 60; $i++) : ?>
	          <option value="<?php echo $i?>" <?php if ((isset($input->timefrom_minutes) && $input->timefrom_minutes == $i) || (!isset($input->timefrom_minutes) && $i == date('i',time()-24*3600))) : ?>selected="selected"<?php endif;?>><?php echo $i?> m.</option>
	      <?php endfor;?>
	  </select>
	</div>
	
	<div class="col-md-4">
		<input class="form-control" type="text" name="timeto" id="id_timeto" placeholder="E.g <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($input->timeto == null ? '' : $input->timeto )?>" />
	</div>
	<div class="col-md-1">
	  <select class="form-control" name="timeto_hours">
	      <?php for ($i = 0; $i < 24; $i++) : ?>
	          <option value="<?php echo $i?>" <?php if (isset($input->timeto_hours) && $input->timeto_hours == $i) : ?>selected="selected"<?php endif;?>><?php echo $i?> h.</option>
	      <?php endfor;?>
	  </select>
	</div>
	<div class="col-md-1">
	  <select class="form-control" name="timeto_minutes">
	      <?php for ($i = 0; $i < 60; $i++) : ?>
	          <option value="<?php echo $i?>" <?php if (isset($input->timeto_minutes) && $input->timeto_minutes == $i) : ?>selected="selected"<?php endif;?>><?php echo $i?> m.</option>
	      <?php endfor;?>
	  </select>
	</div>	
</div>
</div>

<div class="row">
	<div class="col-md-4">
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
    <div class="col-md-4">
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
</div>   

    
<input type="submit" name="doSearch" class="btn btn-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />

<script>
$(function() {
	$('#id_timefrom,#id_timeto').fdatepicker({
		format: 'yyyy-mm-dd'
	});
    $('.btn-block-department').makeDropdown();
});
</script>	
	
</form>

<?php if (isset($last24hstatistic)) : ?>

<table class="table">
	<thead>
		<tr>
			<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Parameter');?></td>
			<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Value');?></td>
		</tr>
	</thead>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_chats.tpl.php'));?></td>
		<td>
            <a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?><?php echo erLhcoreClassSearchHandler::getURLAppendFromInput($input)?>">
                <?php echo $last24hstatistic['totalchats']?>
            </a>
        </td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_pending_chats.tpl.php'));?></td>
		<td>
            <a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?><?php echo erLhcoreClassSearchHandler::getURLAppendFromInput($input)?>/(chat_status_ids)/<?php echo erLhcoreClassModelChat::STATUS_PENDING_CHAT?>">
                <?php echo $last24hstatistic['totalpendingchats']?>
            </a>
        </td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_active_chats.tpl.php'));?></td>
		<td>
            <a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?><?php echo erLhcoreClassSearchHandler::getURLAppendFromInput($input)?>/(chat_status_ids)/<?php echo erLhcoreClassModelChat::STATUS_ACTIVE_CHAT?>">
                <?php echo $last24hstatistic['total_active_chats']?>
            </a>
        </td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_closed_chats.tpl.php'));?></td>
		<td>
            <a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?><?php echo erLhcoreClassSearchHandler::getURLAppendFromInput($input)?>/(chat_status_ids)/<?php echo erLhcoreClassModelChat::STATUS_CLOSED_CHAT?>">
                <?php echo $last24hstatistic['total_closed_chats']?>
            </a>
        </td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/unanswered_chats.tpl.php'));?></td>
		<td>
            <a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?><?php echo erLhcoreClassSearchHandler::getURLAppendFromInput($input)?>/(una)/1">
                <?php echo $last24hstatistic['total_unanswered_chat']?>
            </a>
        </td>
	</tr>
    <tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/abandoned_chats.tpl.php'));?></td>
		<td>
            <a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?><?php echo erLhcoreClassSearchHandler::getURLAppendFromInput($input)?>/(abandoned_chat)/1">
                <?php echo $last24hstatistic['abandoned_chats']?>
            </a>
        </td>
    </tr>
    <tr>
        <td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/dropped_chats.tpl.php'));?></td>
        <td>
            <a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?><?php echo erLhcoreClassSearchHandler::getURLAppendFromInput($input)?>/(dropped_chat)/1">
                <?php echo $last24hstatistic['dropped_chats']?>
            </a>
        </td>
    </tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/chatbox_chats.tpl.php'));?></td>
		<td>
            <a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?><?php echo erLhcoreClassSearchHandler::getURLAppendFromInput($input)?>/(chat_status_ids)/<?php echo erLhcoreClassModelChat::STATUS_CHATBOX_CHAT?>">
                <?php echo $last24hstatistic['chatbox_chats']?>
            </a>
        </td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_messages_including_v_s_o_m.tpl.php'));?></td>
		<td><?php echo $last24hstatistic['ttmall']?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_messages_only_visitors.tpl.php'));?></td>
		<td><?php echo $last24hstatistic['ttmvis']?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_messages_only_system_messages.tpl.php'));?></td>
		<td><?php echo $last24hstatistic['ttmsys']?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_messages_only_operators.tpl.php'));?></td>
		<td><?php echo $last24hstatistic['ttmop']?></td>
	</tr>
</table>

<h2><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/operators_statistic_top_100_by_chats_number.tpl.php'));?></h2>

<table class="table">
	<thead>
		<tr>
			<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','User');?></td>
			<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total chats');?></td>
			<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total messages');?></td>
			<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Votes');?></td>
			<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Last activity');?></td>
		</tr>
	</thead>
	<?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/part/top_24_operators.tpl.php'));?>
</table>

<?php else : ?>
<br/>
<div class="alert alert-info">
<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Please choose statistic parameters first!');?>. <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','If you have thousands of chats generating this report can significantly increase server load!');?>
</div>
<?php endif; ?>
