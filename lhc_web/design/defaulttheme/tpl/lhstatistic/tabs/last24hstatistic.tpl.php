<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Last 24h statistic');?></h2>

<form action="" method="get">

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range from to');?></label>
<div class="row form-group">
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
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total chats');?></td>
		<td><?php echo erLhcoreClassChat::getCount($filter24)?></td>
	</tr>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total pending chats');?></td>
		<td><?php echo erLhcoreClassChat::getCount(array_merge_recursive($filter24,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT))))?></td>
	</tr>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total active chats');?></td>
		<td><?php echo erLhcoreClassChat::getCount(array_merge_recursive($filter24,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_ACTIVE_CHAT))))?></td>
	</tr>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total closed chats');?></td>
		<td><?php echo erLhcoreClassChat::getCount(array_merge_recursive($filter24,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT))))?></td>
	</tr>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Chatbox chats');?></td>
		<td><?php echo erLhcoreClassChat::getCount(array_merge_recursive($filter24,array('filter' => array('status' => erLhcoreClassModelChat::STATUS_CHATBOX_CHAT))))?></td>
	</tr>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total messages (including visitors, system and operators messages)');?></td>
		<td><?php $totalMessagesCount = erLhcoreClassChat::getCount($filter24,'lh_msg'); echo $totalMessagesCount?></td>
	</tr>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total messages (only visitors)');?></td>
		<td><?php $totalVisitorsMessagesCount = erLhcoreClassChat::getCount(array_merge_recursive($filter24,array('filter' => array('user_id' => 0))),'lh_msg'); echo $totalVisitorsMessagesCount;?></td>
	</tr>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total messages (only system messages)');?></td>
		<td><?php $systemMessagesCount = erLhcoreClassChat::getCount(array_merge_recursive($filter24,array('filter' => array('user_id' => -1))),'lh_msg'); echo $systemMessagesCount; ?></td>
	</tr>
	<tr>
		<td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Total messages (only operators)');?></td>
		<td><?php echo $totalMessagesCount-$systemMessagesCount-$totalVisitorsMessagesCount?></td>
	</tr>
</table>
						
<h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Operators statistic, top 100 by chats number');?></h2>
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
		
		<i class="icon-thumbs-up up-voted"><?php echo $operator->statistic_upvotes?></i>
		<i class="icon-thumbs-down down-voted"><?php echo $operator->statistic_downvotes?></i>
		
		</td>
		<td><?php echo $operator->lastactivity_ago?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','ago');?></td>
	</tr>
	<?php endforeach;?>
</table>