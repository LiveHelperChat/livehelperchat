<tr ng-repeat="ou in group.ou | orderBy:online.predicate:online.reverse | filter:query track by ou.id" ng-class="{recent_visit:(ou.last_visit_seconds_ago < 15)<?php echo $onlineCheck?>}">
    	<td nowrap>    	
    	
    	<div style="height:45px;">
    	{{ou.lastactivity_ago}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','ago');?><br/>
    	<span class="fs-11">{{ou.time_on_site_front}}</span>
        </div>
    	    	
    	</td>       	
    	<td>
    	
    	<div class="btn-group" role="group" aria-label="...">
			<a class="btn btn-xs btn-default icon-info" data-placement="right" onmouseleave="$(this).tooltip('destroy')"  onmouseover="$(this).tooltip({'html':true,'animation':false}).tooltip('show')" title="{{ou.notes_intro}}IP: {{ou.ip}}<br />{{ou.first_visit_front}} - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','first visit');?><br/>{{ou.last_visit_front}} - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','last visit');?><br/><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Pageviews');?> - {{ou.pages_count}} {{ou.identifier != '' ? '<br/>Identifier - '+ou.identifier : ''}}<br/>{{ou.operator_message == '' ? trans.first : ou.message_seen == 1 ? trans.second : trans.third}}<br />{{ou.user_agent}}" ng-click="online.showOnlineUserInfo(ou.id)"><img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{ou.user_country_code}}.png" alt="{{ou.user_country_name}}" /></a><?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/custom_online_button_multiinclude.tpl.php')); ?><span ng-click="online.previewChat(ou)" class="btn btn-xs btn-success action-image" ng-show="ou.chat_id > 0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Chat');?></span><span class="btn btn-xs btn-info icon-user" ng-show="ou.total_visits > 1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Returning');?> ({{ou.total_visits}})</span><span class="btn btn-success btn-xs icon-user" ng-show="ou.total_visits == 1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','New');?></span> <span title="{{ou.operator_user_string}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','has sent a message to the user');?>" class="btn btn-xs icon-comment" ng-show="ou.operator_message != ''" ng-class="ou.message_seen == 1 ? 'btn-success' : 'btn-danger'">{{ou.message_seen == 1 ? trans.msg_seen : trans.msg_not_seen}}</span><span class="btn btn-xs btn-primary" ng-if="ou.user_country_code != ''">&nbsp;{{ou.user_country_name}}{{ou.city != '' ? ' | '+ou.city : ''}}</span><span class="btn btn-primary btn-xs icon-clock">{{ou.visitor_tz}} - {{ou.visitor_tz_time}}</span>
		</div>
    	
    	
    	        	 
    	        	 
    	        	 
    	<div class="page-url"><span><a target="_blank" href="{{ou.current_page}}" title="{{ou.current_page}}">{{ou.page_title || ou.current_page}}</a></span></div></td>
        <td><div class="page-url"><span><a target="_blank" href="http:{{ou.referrer}}">{{ou.referrer}}</a></span></div></td>                
        <td>
        <div style="width:80px">
        
        
	        <div class="btn-group" role="group" aria-label="...">
	            <a ng-click="online.sendMessage(ou.id)" class="btn btn-default btn-sm icon-comment" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Send message');?>"></a>
	            <a ng-click="online.deleteUser(ou,'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/list','Are you sure?')?>');" class="btn btn-danger btn-sm icon-cancel-squared" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Delete');?>, ID - {{ou.id}}"></a>		      
			</div>
			
			
			
			</div>
        </td>
	</tr>