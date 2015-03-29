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