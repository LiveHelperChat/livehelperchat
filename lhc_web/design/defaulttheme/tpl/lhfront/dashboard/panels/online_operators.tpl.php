<div class="panel panel-default panel-dashboard" data-panel-id="online_operators" ng-init="lhc.getToggleWidget('ooperators_widget_exp');lhc.getToggleWidgetSort('onop_sort')">
	<div class="panel-heading">
		<i class="material-icons chat-active">account_box</i><?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/online_operators.tpl.php'));?> ({{online_op.list.length}}{{online_op.list.length == lhc.limito ? '+' : ''}})</a>
		<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('ooperators_widget_exp')" class="fs24 pull-right material-icons exp-cntr">{{lhc.toggleWidgetData['ooperators_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>
	</div>
	<div ng-if="lhc.toggleWidgetData['ooperators_widget_exp'] !== true">  
  
        <?php $optinsPanel = array('panelid' => 'operatord', 'limitid' => 'limito', 'disable_product' => true); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>
 
        <div class="panel-list">
			<table class="table table-condensed mb0 table-small table-fixed">
				<thead>
					<tr>
						<th width="30%">
    						<a ng-click="lhc.toggleWidgetSort('onop_sort','onl_dsc','onl_asc',true)">
    						 <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Operator');?>" class="material-icons">account_box</i>
                			 <i ng-class="{'text-muted' : (lhc.toggleWidgetData['onop_sort'] != 'onl_dsc' && lhc.toggleWidgetData['onop_sort'] != 'onl_asc')}" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by online status')?>" class="material-icons">{{lhc.toggleWidgetData['onop_sort'] == 'onl_dsc' || lhc.toggleWidgetData['onop_sort'] != 'onl_asc' ? 'trending_up' : 'trending_down'}}</i>
                			</a>
						</th>
						<th width="20%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Last activity ago');?>" class="material-icons">access_time</i></th>
						<th width="20%">
    						<a ng-click="lhc.toggleWidgetSort('onop_sort','ac_dsc','ac_asc',true)">
    						 <i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Active chats');?>" class="material-icons chat-active">chat</i>
                			 <i ng-class="{'text-muted' : (lhc.toggleWidgetData['onop_sort'] != 'ac_dsc' && lhc.toggleWidgetData['onop_sort'] != 'ac_asc')}" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Sort by active chats number')?>" class="material-icons">{{lhc.toggleWidgetData['onop_sort'] == 'ac_dsc' || lhc.toggleWidgetData['onop_sort'] != 'ac_asc' ? 'trending_up' : 'trending_down'}}</i>
                			</a>
						</th>
						<th width="30%"><i title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Department');?>" class="material-icons">home</i></th>
					</tr>
				</thead>
				<tr ng-repeat="operator in online_op.list track by operator.id">
					<?php /*ng-class="{'chat-pending' : operator.hide_online,'chat-active' : !operator.hide_online}"*/ ?>
					<td><a ng-show="operator.user_id != <?php echo erLhcoreClassUser::instance()->getUserID();?>" href="#" ng-click="lhc.startChatOperator(operator.user_id)" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Start chat');?>"><i class="material-icons">chat</i></a><i class="material-icons" >{{operator.hide_online == 1 ? 'flash_off' : 'flash_on'}}</i>{{operator.hide_online == 1 ? operator.offline_since : ''}} {{operator.name_official}}</td>
					<td>
						<div class="abbr-list" title="{{operator.lastactivity_ago}}">{{operator.lastactivity_ago}}</div>						
					</td>
					<td>{{operator.active_chats}}</td>
					<td><div class="abbr-list" title="{{operator.departments_names.join(', ')}}">{{operator.departments_names.join(", ")}}</div></td>
					
				</tr>
			</table>
		</div>
		
	</div>
</div>
