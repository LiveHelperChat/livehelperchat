<tr ng-repeat="ou in group.ou | orderBy:online.predicate:online.reverse | filter:query track by ou.id" ng-class="{recent_visit:(ou.last_visit_seconds_ago < 15)<?php echo $onlineCheck?>}">
    	<td nowrap>    	
        	<div>
        	{{ou.lastactivity_ago}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','ago');?><br/>
        	<span class="fs-11">{{ou.time_on_site_front}}</span>
            </div>
    	</td>       	
    	<td>
        	<div class="btn-group" role="group" aria-label="...">        	          	
    			<a class="btn btn-xs btn-default" ng-class="{'icon-user-away': ou.online_status == 1, 'icon-user-online': ou.online_status == 0}" data-popover-content="popover-content-ou" data-popover-title="popover-title-ou" data-chat-id="{{ou.id}}" data-toggle="popover" data-placement="right" ng-click="online.showOnlineUserInfo(ou.id)"><i class="material-icons">info_outline</i><img ng-if="ou.user_country_code != ''" ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{ou.user_country_code}}.png" alt="{{ou.user_country_name}}" /></a><?php include(erLhcoreClassDesign::designtpl('lhchat/onlineusers/custom_online_button_multiinclude.tpl.php')); ?><span ng-click="online.previewChat(ou)" class="btn btn-xs btn-success action-image" ng-show="ou.chat_id > 0"><i class="material-icons">chat</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Chat');?></span><span class="btn btn-xs btn-info" ng-show="ou.total_visits > 1"><i class="material-icons">face</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Returning');?> ({{ou.total_visits}})</span><span class="btn btn-success btn-xs" ng-show="ou.total_visits == 1"><i class="material-icons">face</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','New');?></span> <span title="{{ou.operator_user_string}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','has sent a message to the user');?>" class="btn btn-xs" ng-show="ou.operator_message != ''" ng-class="ou.message_seen == 1 ? 'btn-success' : 'btn-danger'"><i class="material-icons">chat_bubble_outline</i>{{ou.message_seen == 1 ? trans.msg_seen : trans.msg_not_seen}}</span><span class="btn btn-xs btn-primary up-case-first" ng-if="ou.user_country_code != ''">{{ou.user_country_name}}{{ou.city != '' ? ' | '+ou.city : ''}}</span><span class="btn btn-primary btn-xs"><i class="material-icons">access_time</i>{{ou.visitor_tz}} - {{ou.visitor_tz_time}}</span>
    		</div>
        	<div id="popover-title-ou-{{ou.id}}" class="hide" ng-if="ou.user_country_code != ''">
    		   <span class="up-case-first"><i class="material-icons">place</i> {{ou.user_country_name}}{{ou.city != '' ? ' | '+ou.city : ''}}</span>
    		</div>
    		<div id="popover-content-ou-{{ou.id}}" class="hide">
    			<ul class="list-unstyled"> 
    				<li><i class="material-icons">access_time</i>{{ou.visitor_tz}} - {{ou.visitor_tz_time}}</li>
    				<li>{{ou.notes_intro}}
    				<li><i class="material-icons">access_time</i>{{ou.first_visit_front}} - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','first visit');?>
    				<li><i class="material-icons">access_time</i>{{ou.last_visit_front}} - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','last visit');?>
                    <li><i class="material-icons">&#xE8A0;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Pageviews');?> - {{ou.pages_count}} {{ou.identifier != '' ? ' Identifier - '+ou.identifier : ''}} | <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Last activity');?> {{ou.lastactivity_ago}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','ago');?> | <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Time on site');?> <span class="fs-11">{{ou.time_on_site_front}}</span>
                    <li><i class="material-icons">&#xE1B1;</i>{{ou.user_agent}} | IP: {{ou.ip}}
    			</ul>
    		</div>
        	
        	<div class="abbr-list pt5" ng-if="ou.page_title || ou.current_page">
				<i class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Page');?>">&#xE8A0;</i><a target="_blank" href="{{ou.current_page}}" title="{{ou.current_page}}">{{ou.page_title || ou.current_page}}</a>
			</div>
			
			<div class="abbr-list pt5" ng-if="ou.referrer != ''">
				<i class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','From');?>">&#xE8A0;</i><a target="_blank" href="http:{{ou.referrer}}" title="{{ou.referrer}}">{{ou.referrer}}</a>
			</div>
				
        	</td>
                      
            <td>
            <div style="width:90px">
    	        <div class="btn-group" role="group" aria-label="...">
    	        
    	            <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/proactive_pre.tpl.php'));?>
    	            
    	            <?php if ($system_configuration_proactive_enabled == true) : ?>
    	            <a ng-click="online.sendMessage(ou.id)" class="btn btn-default btn-sm material-icons mat-100 mr-0" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Send message');?>">chat</a>
    	            <?php endif;?>
    	            
    	            <a ng-click="online.deleteUser(ou,'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/list','Are you sure?')?>');" class="btn btn-danger btn-sm material-icons mat-100 mr-0" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Delete');?>, ID - {{ou.id}}">delete</a>		      
    			</div>
    		</div>
        </td>
	</tr>