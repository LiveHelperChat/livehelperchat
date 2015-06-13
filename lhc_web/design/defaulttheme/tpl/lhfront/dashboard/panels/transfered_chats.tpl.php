<div class="panel panel-default panel-dashboard">
	<div class="panel-heading">
		<i class="icon-chat chat-pending"></i> <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/transfered_chats.tpl.php'));?> ({{transfer_dep_chats.list.length}})</a>
	</div>
	<div>
    	<div class="panel-list">
    		<div role="tabpanel" ng-show="transfer_dep_chats.list.length > 0 || transfer_chats.list.length > 0">
    		
    			<!-- Nav tabs -->
    			<ul class="nav nav-pills p10" role="tablist">
    				<li role="presentation" class="active"><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chats transferred to you directly');?>" href="#transferedperson-widget" aria-controls="transferedperson-widget" role="tab" data-toggle="tab"><i class="icon-user"></i><span class="tru-cnt"></span></a></li>
    				<li role="presentation"><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Transferred to your department');?>" href="#transfereddep-widget" aria-controls="transfereddep-widget" role="tab" data-toggle="tab"><i class="icon-users"></i><span class="trd-cnt"></span></a></li>
    			</ul>
    			
    			<!-- Tab panes -->
    			<div class="tab-content mt0">
    				<div role="tabpanel" class="tab-pane active" id="transferedperson-widget">
    				
            	      		<table class="table table-condensed mb0 table-small table-fixed" ng-if="transfer_chats.list.length > 0">
                        		<thead>
                        			<tr>
                        				<th width="60%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Visitor');?>" class="icon-user"></i></th>
                        				<th width="40%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Created');?>" class="icon-clock"></i></th>
                        			</tr>
                        		</thead>
                        		<tr ng-repeat="chat in transfer_chats.list">
                        			<td>
                        			   <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>" class="icon-popup" ng-click="lhc.startChatNewWindowTransfer(chat.id,chat.nick,chat.transfer_id)"></a> <span ng-if="chat.country_code != undefined"><img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{chat.country_code}}.png" alt="{{chat.country_name}}" title="{{chat.country_name}}" /></span> <a ng-click="lhc.previewChat(chat.id)" class="icon-info"></a> <a ng-click="lhc.startChatTransfer(chat.id,chat.nick,chat.transfer_id)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Accept chat');?>">{{chat.nick}}</a>
                        			</td>	
                        			<td nowrap="nowrap">
                        			   <div class="abbr-list">{{chat.time_front}}</div>
                        			</td>			
                        		</tr>
                        	</table>
                        	
                        	<div ng-if="transfer_chats.list.length == 0" class="m10 alert alert-info"><i class="icon-search"></i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Nothing found')?>...</div>
		
    				</div>
    				<div role="tabpanel" class="tab-pane" id="transfereddep-widget">
            	      		
            	      		<table class="table table-condensed mb0 table-small table-fixed" ng-if="transfer_dep_chats.list.length > 0">
                        		<thead>
                        			<tr>
                        				<th width="60%"><i title="Visitor" class="icon-user"></i></th>
                        				<th width="40%"><i title="Transfer time" class="icon-clock"></i></th>
                        			</tr>
                        		</thead>
                        		<tr ng-repeat="chat in transfer_dep_chats.list">
                        			<td>
                        			   <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Open in a new window');?>" class="icon-popup" ng-click="lhc.startChatNewWindowTransfer(chat.id,chat.nick,chat.transfer_id)"></a> <span ng-if="chat.country_code != undefined"><img ng-src="<?php echo erLhcoreClassDesign::design('images/flags');?>/{{chat.country_code}}.png" alt="{{chat.country_name}}" title="{{chat.country_name}}" /></span> <a ng-click="lhc.previewChat(chat.id)" class="icon-info"></a> <a ng-click="lhc.startChatTransfer(chat.id,chat.nick,chat.transfer_id)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Accept chat');?>">{{chat.nick}}</a>
                        			</td>	
                        			<td nowrap="nowrap">
                        			   <div class="abbr-list">{{chat.time_front}}</div>
                        			</td>			
                        		</tr>
                        	</table>
                        	
                        	<div ng-if="transfer_dep_chats.list.length == 0" class="m10 alert alert-info"><i class="icon-search"></i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Nothing found')?>...</div>
                       
    				</div>
    			</div>
    		</div>
    		
    		<div ng-if="transfer_chats.list.length == 0 && transfer_dep_chats.list.length == 0" class="m10 alert alert-info"><i class="icon-search"></i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Nothing found')?>...</div>
    		
    	</div>
	</div>
</div>


