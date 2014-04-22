<?php $currentUser = erLhcoreClassUser::instance(); ?>

<ul class="button-group round geo-settings">
      <?php if ($currentUser->hasAccessTo('lhchat','administrateconfig')) : ?>
      <li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/geoconfiguration')?>" class="button small"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','GEO detection configuration');?></a></li>
      <?php endif; ?>

      <?php if ($currentUser->hasAccessTo('lhchat','allowclearonlinelist')) : ?>
      <li><a class="small button alert csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('chat/onlineusers')?>/(clear_list)/1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Clear list');?></a></li>
      <?php endif; ?>

</ul>

<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Online visitors');?></h1>

<?php if($tracking_enabled == false) : ?>
<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User tracking is disabled, enable it at');?>&nbsp;-&nbsp;<a href="<?php echo erLhcoreClassDesign::baseurl('chat/editchatconfig')?>/track_online_visitors"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Chat configuration');?></a></p>
<?php endif; ?>

<div class="section-container auto" data-section="auto" ng-controller="OnlineCtrl as online">
	  <section>
	    <p class="title" data-section-title><a href="#panel1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','List');?></a></p>
	    <div class="content" data-section-content>

<div class="row">
	<div class="columns small-1">
		<label class="inline" id="online-users-count" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','online users');?>">{{online.onlineusers.length}}</label>
	</div>
	<div class="columns small-3">
		<input ng-model="query" type="text" value="" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Type to search')?>">
	</div>
	<div class="columns small-2">
		<select ng-model="groupByField" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Group list by');?>">
		    	<option value="none"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Group by');?></option>
		    	<option value="user_country_name"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User country');?></option>
		    	<option value="current_page"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Page');?></option>
		    	<option value="page_title"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Page title');?></option>
		    	<option value="referrer"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Referrer');?></option>
		    	<option value="identifier"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Identifier');?></option>
		    	<option value="dep_id"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Department');?></option>
		</select>
	</div>	
	<div class="columns small-2">
		<?php 		
		echo erLhcoreClassRenderHelper::renderCombobox( array (
	                    'input_name'     => 'department_id',
						'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select department'),
	                    'selected_id'    => 0,	
						'ng-model'		 => 'online.department',
	                    'list_function'  => 'erLhcoreClassModelDepartament::getList',
						'list_function_params' => $departmentParams
	    )); ?>
	</div>	
	<div class="columns small-2">
		<select id="updateTimeout" ng-model="online.updateTimeout" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Refresh list every');?>">
		    	<option value="1">1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','second');?></option>		    	
		    	<option value="3">3 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','seconds');?></option>		    	
		    	<option value="5">5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','seconds');?></option>		    	
		    	<option value="10">10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','seconds');?></option>		    	
		</select>
	</div>
	<div class="columns small-2">
		<select id="userTimeout" ng-model="online.userTimeout" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Show visitors who visited site in the past');?>">
		    	<option value="30">30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','seconds');?></option>
		    	<option value="60">1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','minit');?></option>
		    	<option value="120">2 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','minits');?></option>
		    	<option value="300">5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','minits');?></option>
		    	<option value="600">10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','minits');?></option>
		    	<option value="1200">20 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','minits');?></option>
		    	<option value="1800">30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','minits');?></option>
		    	<option value="3600">1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','hour');?></option>
		    	<option value="86400">1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','day');?></option>
		    	<option value="604800">7 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','days');?></option>
		    	<option value="2678400">31 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','day');?></option>
		</select>
	</div>
</div>

<table class="twelve online-users-table" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th width="5%" nowrap><a href="" ng-click="online.predicate = 'last_visit'; online.reverse=!online.reverse"><img  src="<?php echo erLhcoreClassDesign::design('images/icons/clock.png');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Last activity');?>" /></a></th>
    <th width="5%" nowrap><a href="" ng-click="online.predicate = 'time_on_site'; online.reverse=!online.reverse"><img  src="<?php echo erLhcoreClassDesign::design('images/icons/clock.png');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Time on site');?>" /></a></th>
    <th width="40%"><a href="" ng-click="online.predicate = 'current_page'; online.reverse=!online.reverse" /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Page');?></a></th>
    <th width="40%"><a href="" ng-click="online.predicate = 'referrer'; online.reverse=!online.reverse" /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Came from');?></a></th>
    <th width="1%" nowrap><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Status');?></th>
    <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Action');?></th>
</tr>
</thead>
<tbody ng-repeat="group in onlineusersGrouped">
	<tr ng-show="group.label != ''">
		<td colspan="5"><h5>{{group.label}} ({{group.ou.length}})</h5></td>
	</tr>		
	<tr ng-repeat="ou in group.ou | orderBy:online.predicate:online.reverse | filter:query track by ou.id">
    	<td nowrap>{{ou.lastactivity_ago}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','ago');?></td>
    	<td>{{ou.time_on_site_front}}</td>    	
    	<td><div class="page-url"><span><a target="_blank" href="{{ou.current_page}}" title="{{ou.current_page}}">{{ou.page_title || ou.current_page}}</a></span></div></td>
        <td><div class="page-url"><span><a target="_blank" href="{{ou.referrer}}">{{ou.referrer}}</a></span></div></td>
        <td>       
	        <div style="width:270px">
		        <span ng-if="ou.user_country_code != ''"><img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{ou.user_country_code}}.png" alt="{{ou.user_country_name}}" title="{{ou.user_country_name}} | {{ou.city}}" /></span>
		        <a ng-click="online.showOnlineUserInfo(ou.id)"><img ng-src="{{ou.operator_message == '' ? '<?php echo erLhcoreClassDesign::design('images/icons/user_inactive.png');?>' : (ou.message_seen == 1 ? '<?php echo erLhcoreClassDesign::design('images/icons/user_green_32.png');?>' : '<?php echo erLhcoreClassDesign::design('images/icons/user.png');?>')}}" ng-attr-title="{{ou.operator_message == '' ? '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User does not have any messages from the operator');?>' : ou.message_seen == 1 ? '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User has seen the message from the operator.');?>' : '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User has not seen a message from the operator, or the message window is still open.');?>'}}" /></a>
		        <img src="<?php echo erLhcoreClassDesign::design('images/icons/browsers.png');?>" title="{{ou.user_agent}}" />
		        <img ng-show="ou.chat_id > 0" ng-class="{'action-image': ou.can_view_chat}" ng-click="online.previewChat(ou)" src="<?php echo erLhcoreClassDesign::design('images/icons/user_comment.png');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User is chatting');?>" />
		        <img ng-show="ou.chat_id == 0" src="<?php echo erLhcoreClassDesign::design('images/icons/user_comment_inactive.png');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User is not having any chat right now');?>" />
		        <img ng-show="ou.operator_user_send" src="<?php echo erLhcoreClassDesign::design('images/icons/user_suit_32.png');?>" title="{{ou.operator_user_string}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','has sent a message to the user');?>" />
		        <img ng-show="!ou.operator_user_send" src="<?php echo erLhcoreClassDesign::design('images/icons/user_suit_32_inactive.png');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','No one has sent a message to the user yet');?>" />
		        <img src="<?php echo erLhcoreClassDesign::design('images/icons/ip.png');?>" title="{{ou.ip}}" />
		        <img src="<?php echo erLhcoreClassDesign::design('images/icons/information.png');?>" title="{{ou.first_visit_front}} - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','first visit');?><?php echo "\n";?>{{ou.last_visit_front}} - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','last visit');?><?php echo "\n"?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Pageviews');?> - {{ou.pages_count}} {{ou.identifier != '' ? '\nIdentifier - '+ou.identifier : ''}}" />
				<i ng-class="{'returned-user': ou.total_visits > 1}" class="icon-reply return-user" title="{{ou.total_visits}}"></i>
	        </div>
        </td>        
        <td>
        <div style="width:80px">
	        <ul class="button-group round">
	            <li><a ng-click="online.sendMessage(ou.id)" class="button small icon-comment" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Send message');?>"></a></li>		      
	            <li><a ng-click="online.deleteUser(ou,'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/list','Are you sure?')?>');" class="small alert button icon-cancel-squared" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Delete');?>, ID - {{ou.id}}"></a></li>		      
			</ul>
			</div>
        </td>
	</tr>	
</tbody>
</table>

</div>
	  </section>
	  <section>
	    <p class="title" data-section-title><a id="map-activator" href="#panel2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Map');?></a></p>
	    <div class="content" data-section-content>

				<div class="row">
					<div class="columns large-6">
						<img data-tooltip class="tip-right" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User is chatting');?>" src="<?php echo erLhcoreClassDesign::design('images/icons/home-chat.png')?>" />
	    				<img data-tooltip class="tip-right" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User does not have any message from operator');?>" src="<?php echo erLhcoreClassDesign::design('images/icons/home-unsend.png')?>" />
	    				<img data-tooltip class="tip-right" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','User has message from operator');?>" src="<?php echo erLhcoreClassDesign::design('images/icons/home-send.png')?>" />
					</div>
					<div class="columns large-3">
					<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
				                    'input_name'     => 'department_map_id',
									'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select department'),
				                    'selected_id'    => 0,
				                    'list_function'  => 'erLhcoreClassModelDepartament::getList'
				    )); ?>
				    </div>
					<div class="columns large-3">
						<select id="markerTimeout" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Marker timeout before it dissapears from map');?>">
			    			<option value="30">30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','seconds');?></option>
			    			<option value="60">1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','minit');?></option>
			    			<option value="120">2 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','minits');?></option>
			    			<option value="300">5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','minits');?></option>
			    			<option value="600">10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','minits');?></option>
		    			</select>
	    			</div>
				</div>

			<div id="map_canvas" style="height:600px;width:100%;"></div>
			<script type="text/javascript">
			var GeoLocationData = {zoom:<?php echo $geo_location_data['zoom']?>,lat:<?php echo $geo_location_data['lat']?>,lng:<?php echo $geo_location_data['lng']?>};
			</script>
		    <script src="https://maps-api-ssl.google.com/maps/api/js?v=3&sensor=false&callback=gMapsCallback"></script>

	    </div>
	  </section>
</div>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>