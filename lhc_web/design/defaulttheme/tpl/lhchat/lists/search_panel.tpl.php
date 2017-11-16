<form action="<?php echo $input->form_action?>" method="get" name="SearchFormRight">

	<input type="hidden" name="doSearch" value="1">

	<div class="row">
		<div class="col-md-3">
		   <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Nick');?></label>
			<input type="text" class="form-control" name="nick" value="<?php echo htmlspecialchars($input->nick)?>" />
		   </div>
		</div>
		<div class="col-md-3">
		  <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','E-mail');?></label>
			<input type="text" class="form-control" name="email" value="<?php echo htmlspecialchars($input->email)?>" />
		  </div>
		</div>
		
		<div class="col-md-3">
		  <div class="form-group">
				<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department');?></label>
				<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
	                    'input_name'     => 'department_id',
						'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select department'),
	                    'selected_id'    => $input->department_id,
				        'css_class'      => 'form-control',				
	                    'list_function'  => 'erLhcoreClassModelDepartament::getList'
	            )); ?>            	
		  </div>
		</div>
		
		<div class="col-md-3">
		   <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','User');?></label>
			<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
	                    'input_name'     => 'user_id',
						'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select user'),
	                    'selected_id'    => $input->user_id,
			            'css_class'      => 'form-control',
						'display_name' => 'name_official',
	                    'list_function'  => 'erLhcoreClassModelUser::getUserList'
	            )); ?>            	
		  </div>
		</div>
		
		<div class="col-md-2">
		  <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range from');?></label>
    			<div class="row">
    				<div class="col-md-12">
    					<input type="text" class="form-control" name="timefrom" id="id_timefrom" placeholder="E.g <?php echo date('Y-m-d',time()-7*24*3600)?>" value="<?php echo htmlspecialchars($input->timefrom)?>" />
    				</div>							
    			</div>
			</div>
		</div>	

		<div class="col-md-3">
		  <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute from');?></label>
			<div class="row">				
				<div class="col-md-6">
				    <select name="timefrom_hours" class="form-control">
				        <option value="">Select hour</option>
				        <?php for ($i = 0; $i <= 23; $i++) : ?>
				            <option value="<?php echo $i?>" <?php if (isset($input->timefrom_hours) && $input->timefrom_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
				        <?php endfor;?>
				    </select>
				</div>
				<div class="col-md-6">
				    <select name="timefrom_minutes" class="form-control">
				        <option value="">Select minute</option>
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
    					<input type="text" class="form-control" name="timeto" id="id_timeto" placeholder="E.g <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($input->timeto)?>" />
    				</div>							
    			</div>
			</div>
		</div>
		
		<div class="col-md-3">
		  <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute to');?></label>
		    <div class="row">				
				<div class="col-md-6">
				    <select name="timeto_hours" class="form-control">
				        <option value="">Select hour</option>
				        <?php for ($i = 0; $i <= 23; $i++) : ?>
				            <option value="<?php echo $i?>" <?php if (isset($input->timeto_hours) && $input->timeto_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
				        <?php endfor;?>
				    </select>
				</div>
				<div class="col-md-6">
				    <select name="timeto_minutes" class="form-control">
				        <option value="">Select minute</option>
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
                      <div class="col-xs-6">
                          <select class="form-control" name="wait_time_from">
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
                      <div class="col-xs-6">
                          <select class="form-control" name="wait_time_till">
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
			<select name="fbst" class="form-control">
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
			<select name="chat_status" class="form-control">
				<option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Any');?></option>
				<option value="0" <?php if ($input->chat_status === erLhcoreClassModelChat::STATUS_PENDING_CHAT) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Pending chats');?></option>
				<option value="1" <?php if ($input->chat_status === erLhcoreClassModelChat::STATUS_ACTIVE_CHAT) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Active chats');?></option>
				<option value="2" <?php if ($input->chat_status === erLhcoreClassModelChat::STATUS_CLOSED_CHAT) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Closed chats');?></option>
				<option value="3" <?php if ($input->chat_status === erLhcoreClassModelChat::STATUS_CHATBOX_CHAT) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chatbox chats');?></option>
				<option value="4" <?php if ($input->chat_status === erLhcoreClassModelChat::STATUS_OPERATORS_CHAT) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Operators chats');?></option>
			</select>
		  </div>
		</div>	
		<div class="col-md-3">
		    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Product');?></label>
			<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
	                    'input_name'     => 'product_id',
						'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select product'),
	                    'selected_id'    => $input->product_id,
			            'css_class'      => 'form-control',
	                    'list_function'  => 'erLhAbstractModelProduct::getList'
	         )); ?>
		</div>
		<div class="col-md-2">
			<div class="form-group">
		    	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chat ID');?></label>
				<input type="text" class="form-control" name="chat_id" value="<?php echo htmlspecialchars($input->chat_id)?>" />
			</div>
		</div>
        <div class="col-md-3">
			<div class="form-group">
		    	<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chat duration');?></label>
                <div class="row">
                    <div class="col-xs-6">
                        <select class="form-control" name="chat_duration_from" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chat duration from');?>">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','From');?></option>
                            <?php for ($i = 1; $i < 10; $i++) : ?>
                                <option value="<?php echo $i*60?>" <?php $i*60 === $input->chat_duration_from ? print 'selected="selected"' : ''?> ><?php echo $i?> m.</option>
                            <?php endfor; ?>

                            <?php for ($i = 2; $i < 19; $i++) : ?>
                                <option value="<?php echo $i*60*5?>" <?php $i*60*5 === $input->chat_duration_from ? print 'selected="selected"' : ''?> ><?php echo $i*5?> m.</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-xs-6">
                        <select class="form-control" name="chat_duration_till" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chat duration till');?>">
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
		<div class="col-md-3">
    		<div class="form-group">
        	   <label class="control-label"><input type="checkbox" name="hum" <?php $input->hum == 1 ? print ' checked="checked" ' : ''?> value="on" /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Has unread messages')?></label>
        	</div>
		</div>
		
    </div>

	<div class="btn-group" role="group" aria-label="...">
		<input type="submit" name="doSearch" class="btn btn-default" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />
		<?php if ($pages->items_total > 0) : ?>
		<a target="_blank" class="btn btn-default" href="<?php echo $pages->serverURL?>/(print)/1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Print');?></a>
		<a target="_blank" class="btn btn-default" href="<?php echo $pages->serverURL?>/(xls)/1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','XLS');?></a>
		<a target="_blank" class="btn btn-default" href="<?php echo $pages->serverURL?>/(xls)/2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','XLS (with content)');?></a>
		<?php endif; ?>
	</div>
	
</form>

<script>
$(function() {
	$('#id_timefrom,#id_timeto').fdatepicker({
		format: 'yyyy-mm-dd'
	});
});
</script>