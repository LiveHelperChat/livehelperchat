<h2><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/last_24h_statistic.tpl.php'));?></h2>

<form action="" method="get">

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
            <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                    'input_name'     => 'group_id',
            		'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select group'),
                    'selected_id'    => $input->group_id,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name',
                    'list_function'  => 'erLhcoreClassModelGroup::getList'
            )); ?>
        </div>
    </div>
    <div class="col-md-4">
	   <div class="form-group">
    	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department group');?></label>
    	<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                    'input_name'     => 'department_group_id',
    				'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department group'),
                    'selected_id'    => $input->department_group_id,	
    	            'css_class'      => 'form-control',			
                    'list_function'  => 'erLhcoreClassModelDepartamentGroup::getList'
            )); ?> 
        </div>   
    </div>
</div>   

    
    
<input type="submit" name="doSearch" class="btn btn-default" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />

<script>
$(function() {
	$('#id_timefrom,#id_timeto').fdatepicker({
		format: 'yyyy-mm-dd'
	});
});
</script>	
	
</form>

<table class="table">
	<thead>
		<tr>
			<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Parameter');?></td>
			<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Value');?></td>
		</tr>
	</thead>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_chats.tpl.php'));?></td>
		<td><?php echo erLhcoreClassChat::getCount($filter24)?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_pending_chats.tpl.php'));?></td>
		<td><?php echo erLhcoreClassChat::getCount(array_merge_recursive($filter24,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT))))?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_active_chats.tpl.php'));?></td>
		<td><?php echo erLhcoreClassChat::getCount(array_merge_recursive($filter24,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT))))?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_closed_chats.tpl.php'));?></td>
		<td><?php echo erLhcoreClassChat::getCount(array_merge_recursive($filter24,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT))))?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/unanswered_chats.tpl.php'));?></td>
		<td><?php echo erLhcoreClassChat::getCount(array_merge_recursive($filter24,array('filter' => array('unanswered_chat' => 1))))?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/chatbox_chats.tpl.php'));?></td>
		<td><?php echo erLhcoreClassChat::getCount(array_merge_recursive($filter24,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_CHATBOX_CHAT))))?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_messages_including_v_s_o_m.tpl.php'));?></td>
		<td><?php 
		
		$filterMsg = array_merge_recursive($filter24,array('innerjoin' => array('lh_chat' => array('lh_msg.chat_id','lh_chat.id'))));
		
		if (isset($filterMsg['filtergte']['time'])) {
		    $filterMsg['filtergte']['lh_msg.time'] = $filterMsg['filtergte']['time'];
		    unset($filterMsg['filtergte']['time']);
		}
		
		if (isset($filterMsg['filterlte']['time'])) {
		    $filterMsg['filterlte']['lh_msg.time'] = $filterMsg['filterlte']['time'];
		    unset($filterMsg['filterlte']['time']);
		}
				
		$totalMessagesCount = erLhcoreClassChat::getCount($filterMsg,'lh_msg','count(lh_msg.id)'); echo $totalMessagesCount?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_messages_only_visitors.tpl.php'));?></td>
		<td><?php $totalVisitorsMessagesCount = erLhcoreClassChat::getCount(array_merge_recursive($filterMsg,array('innerjoin' => array('lh_chat' => array('lh_msg.chat_id','lh_chat.id')),'filter' => array('lh_msg.user_id' => 0))),'lh_msg','count(lh_msg.id)'); echo $totalVisitorsMessagesCount;?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_messages_only_system_messages.tpl.php'));?></td>
		<td><?php $systemMessagesCount = erLhcoreClassChat::getCount(array_merge_recursive($filterMsg,array('innerjoin' => array('lh_chat' => array('lh_msg.chat_id','lh_chat.id')), 'filterin' => array('lh_msg.user_id' => array(-1,-2)))),'lh_msg','count(lh_msg.id)'); echo $systemMessagesCount; ?></td>
	</tr>
	<tr>
		<td><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/total_messages_only_operators.tpl.php'));?></td>
		<td><?php echo $totalMessagesCount-$systemMessagesCount-$totalVisitorsMessagesCount?></td>
	</tr>
</table>
						
<h2><?php include(erLhcoreClassDesign::designtpl('lhstatistic/tabs/titles/operators_statistic_top_100_by_chats_number.tpl.php'));?></h2>
<?php $operators = erLhcoreClassChatStatistic::getTopTodaysOperators(100,0,$filter24); ?>

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
	<?php foreach ($operators as $operator) : ?>
	<tr>
		<td><?php echo htmlspecialchars((string)$operator)?></td>
		<td><?php echo $operator->statistic_total_chats?></td>
		<td><?php echo $operator->statistic_total_messages?></td>
		<td>
		  <span class="up-voted"><i class="material-icons up-voted">thumb_up</i><?php echo $operator->statistic_upvotes?></span>
		  <span class="down-voted"><i class="material-icons down-voted">thumb_down</i><?php echo $operator->statistic_downvotes?></span>
		</td>
		<td><?php echo $operator->lastactivity_ago?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','ago');?></td>
	</tr>
	<?php endforeach;?>
</table>
