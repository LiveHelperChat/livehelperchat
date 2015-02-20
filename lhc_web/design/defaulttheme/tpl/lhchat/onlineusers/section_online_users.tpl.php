

<div class="row">
	<div class="col-sm-2 form-group col-xs-6 pr5">
		<label id="online-users-count" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','online users');?>">{{online.onlineusers.length}}</label>
		
		<div class="pull-right">
		  <?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/online_settings.tpl.php')); ?>
	    </div>
	
	</div>
	<div class="col-sm-2 form-group col-xs-6 pl5 pr5">
		<input class="form-control" ng-model="query" type="text" value="" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Type to search')?>">
	</div>
	<div class="col-sm-2 form-group col-xs-6 pl5 pr5">
		<select class="form-control" ng-model="groupByField" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Group list by');?>">
		    	<option value="none"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Group by');?></option>
		    	<option value="user_country_name"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User country');?></option>
		    	<option value="current_page"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Page');?></option>
		    	<option value="page_title"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Page title');?></option>
		    	<option value="referrer"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Referrer');?></option>
		    	<option value="identifier"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Identifier');?></option>
		    	<option value="dep_id"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Department');?></option>
		</select>
	</div>	
	<div class="col-sm-2 form-group col-xs-6 pl5 pr5">
		<?php 		
		echo erLhcoreClassRenderHelper::renderCombobox( array (
	                    'input_name'     => 'department_id',
						'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select department'),
	                    'selected_id'    => 0,	
		                'css_class'      => 'form-control',
						'ng-model'		 => 'online.department',
	                    'list_function'  => 'erLhcoreClassModelDepartament::getList',
						'list_function_params' => $departmentParams
	    )); ?>
	</div>	
	<div class="col-sm-2 form-group col-xs-6 pl5 pr5">
		<select class="form-control" id="updateTimeout" ng-model="online.updateTimeout" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Refresh list every');?>">
		    	<option value="1">1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','second');?></option>		    	
		    	<option value="3">3 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','seconds');?></option>		    	
		    	<option value="5">5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','seconds');?></option>		    	
		    	<option value="10">10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','seconds');?></option>		    	
		</select>
	</div>
	<div class="col-sm-1 form-group col-xs-6 pl5 pr5">
		<select class="form-control" id="userTimeout" ng-model="online.userTimeout" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Show visitors who visited site in the past');?>">
		    	<option value="30">30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','seconds');?></option>
		    	<option value="60">1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','minute');?></option>
		    	<option value="120">2 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','minutes');?></option>
		    	<option value="300">5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','minutes');?></option>
		    	<option value="600">10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','minutes');?></option>
		    	<option value="1200">20 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','minutes');?></option>
		    	<option value="1800">30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','minutes');?></option>
		    	<option value="3600">1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','hour');?></option>
		    	<option value="86400">1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','day');?></option>
		    	<option value="604800">7 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','days');?></option>
		    	<option value="2678400">31 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','day');?></option>
		</select>
	</div>
	<div class="col-sm-1 form-group col-xs-12 pl5">
		<select class="form-control" id="maxRows" ng-model="online.maxRows" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Max records to return');?>">
		    	<option value="50">50</option>
		    	<option value="100">100</option>
		    	<option value="150">150</option>		   
		    	<option value="200">200</option>		   
		    	<option value="250">250</option>
		    	<option value="300">300</option>		  
		    	<option value="500">500</option>
		    	<option value="1000">1000</option>
		</select>
	</div>	
</div>



<table ng-cloak class="table table-condensed online-users-table" cellpadding="0" cellspacing="0" ng-init='trans = <?php echo json_encode(array('third' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User has not seen a message from the operator, or the message window is still open.'), 'second' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User has seen the message from the operator.'),'first' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User does not have any messages from the operator')),JSON_HEX_APOS)?>'>
<thead>
<tr>
    <th width="5%" nowrap><a class="icon-clock" ng-click="online.predicate = 'last_visit'; online.reverse=!online.reverse" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Last activity');?>" ></a><a class="icon-clock" ng-click="online.predicate = 'time_on_site'; online.reverse=!online.reverse" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Time on site');?>"></a><a class="icon-clock" ng-click="online.predicate = 'visitor_tz_time'; online.reverse=!online.reverse" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Visitor local time');?>"></a><?php if (erLhcoreClassModelChatConfig::fetch('track_is_online')->current_value == 1) : ?><a class="icon-clock" ng-click="online.predicate = 'last_check_time'; online.reverse=!online.reverse" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','By user status on site');?>"></a><?php endif;?></th>
    <th width="40%"><a href="" ng-click="online.predicate = 'current_page'; online.reverse=!online.reverse" /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Page');?></a></th>
    <th width="40%"><a href="" ng-click="online.predicate = 'referrer'; online.reverse=!online.reverse" /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Came from');?></a></th>
    <th width="1%" nowrap><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Status');?></th>
    <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Action');?></th>
</tr>
</thead>
<tbody ng-repeat="group in onlineusersGrouped track by group.id">
	<tr ng-show="group.label != ''">
		<td colspan="6"><h5>{{group.label}} ({{group.ou.length}})</h5></td>
	</tr>		
	<tr ng-repeat="ou in group.ou | orderBy:online.predicate:online.reverse | filter:query track by ou.id" ng-class="{recent_visit:(ou.last_visit_seconds_ago < 15)<?php echo $onlineCheck?>}">
    	<td nowrap>    	
    	{{ou.lastactivity_ago}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','ago');?><br/>
    	<span class="fs-11">{{ou.time_on_site_front}}</span><br/>
    	<span class="fs-11" title="{{ou.visitor_tz}}">{{ou.visitor_tz_time}}</span>   
    	</td>       	
    	<td><div class="page-url"><span><a target="_blank" href="{{ou.current_page}}" title="{{ou.current_page}}">{{ou.page_title || ou.current_page}}</a></span></div></td>
        <td><div class="page-url"><span><a target="_blank" href="http:{{ou.referrer}}">{{ou.referrer}}</a></span></div></td>
        <td>       
	        <div style="width:165px">
		        <span ng-if="ou.user_country_code != ''"><img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{ou.user_country_code}}.png" alt="{{ou.user_country_name}}" title="{{ou.user_country_name}} | {{ou.city}}" /></span>
		        <a data-placement="left" onmouseleave="$(this).tooltip('destroy')"  onmouseover="$(this).tooltip({'html':true,'animation':false}).tooltip('show')" title="{{ou.notes_intro}}IP: {{ou.ip}}<br />{{ou.first_visit_front}} - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','first visit');?><br/>{{ou.last_visit_front}} - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','last visit');?><br/><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Pageviews');?> - {{ou.pages_count}} {{ou.identifier != '' ? '<br/>Identifier - '+ou.identifier : ''}}<br/>{{ou.operator_message == '' ? trans.first : ou.message_seen == 1 ? trans.second : trans.third}}<br />{{ou.user_agent}}" ng-click="online.showOnlineUserInfo(ou.id)"><img ng-src="{{ou.operator_message == '' ? '<?php echo erLhcoreClassDesign::design('images/icons/user_inactive.png');?>' : (ou.message_seen == 1 ? '<?php echo erLhcoreClassDesign::design('images/icons/user_green_32.png');?>' : '<?php echo erLhcoreClassDesign::design('images/icons/user.png');?>')}}" /></a>
		        <img ng-show="ou.chat_id > 0" ng-class="{'action-image': ou.can_view_chat}" ng-click="online.previewChat(ou)" src="<?php echo erLhcoreClassDesign::design('images/icons/user_comment.png');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User is chatting');?>" />
		        <img ng-show="ou.chat_id == 0" src="<?php echo erLhcoreClassDesign::design('images/icons/user_comment_inactive.png');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User is not having any chat right now');?>" />
		        <img ng-show="ou.operator_user_send" src="<?php echo erLhcoreClassDesign::design('images/icons/user_suit_32.png');?>" title="{{ou.operator_user_string}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','has sent a message to the user');?>" />
		        <img ng-show="!ou.operator_user_send" src="<?php echo erLhcoreClassDesign::design('images/icons/user_suit_32_inactive.png');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','No one has sent a message to the user yet');?>" />
				<i ng-class="{'returned-user': ou.total_visits > 1}" class="icon-reply return-user" title="{{ou.total_visits}}"></i>
	        </div>
        </td>        
        <td>
        <div style="width:80px">
        
        
	        <div class="btn-group" role="group" aria-label="...">
	            <a ng-click="online.sendMessage(ou.id)" class="btn btn-default btn-sm icon-comment" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Send message');?>"></a>
	            <a ng-click="online.deleteUser(ou,'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/list','Are you sure?')?>');" class="btn btn-danger btn-sm icon-cancel-squared" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Delete');?>, ID - {{ou.id}}"></a>		      
			</div>
			
			
			
			</div>
        </td>
	</tr>	
</tbody>
</table>
