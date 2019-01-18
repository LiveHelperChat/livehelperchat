<div class="panel panel-default panel-dashboard" data-panel-id="online_operators" ng-init="lhc.getToggleWidget('ooperators_widget_exp');lhc.getToggleWidgetSort('onop_sort')">
	<div class="panel-heading">
		<i class="material-icons chat-active">account_box</i><?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/online_operators.tpl.php'));?> ({{online_op.list.length}}{{online_op.list.length == lhc.limito ? '+' : ''}})</a>
		<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('ooperators_widget_exp')" class="fs24 pull-right material-icons exp-cntr">{{lhc.toggleWidgetData['ooperators_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>
	</div>
	<div ng-if="lhc.toggleWidgetData['ooperators_widget_exp'] !== true">  
  
        <?php $optinsPanel = array('panelid' => 'operatord', 'limitid' => 'limito', 'disable_product' => true); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>
 
        <div class="panel-list">
            <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/online_operators.tpl.php'));?>
		</div>
		
	</div>
</div>
