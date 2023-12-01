<?php
$permissionsWidget = [];
if (erLhcoreClassUser::instance()->hasAccessTo('lhstatistic','userstats')){
    $permissionsWidget[] = 'lhstatistic_userstats';
}
if (erLhcoreClassUser::instance()->hasAccessTo('lhuser', 'setopstatus')){
    $permissionsWidget[] = 'lhuser_setopstatus';
}
if (erLhcoreClassUser::instance()->hasAccessTo('lhgroupchat', 'use')){
    $permissionsWidget[] = 'lhgroupchat_use';
}
?>

<lhc-widget icon_class="chat-active" permissions='<?php echo json_encode($permissionsWidget);?>' list_identifier="operators" column_1_width="50%" column_2_width="5%" column_3_width="30%" type="online_op" no_link="true" card_icon="account_box" optionsPanel='<?php echo json_encode(array('panelid' => 'operatord', 'limitid' => 'limito', 'disable_product' => true, 'userid' => 'oopu'))?>' www_dir_flags="<?php echo erLhcoreClassDesign::design('images/flags');?>" expand_identifier="ooperators_widget_exp" sort_identifier = "onop_sort" panel_list_identifier="operatord-panel-list"></lhc-widget>

<?php /*
<div class="card card-dashboard card-operators" data-panel-id="online_operators" ng-init="lhc.getToggleWidget('ooperators_widget_exp');lhc.getToggleWidgetSort('onop_sort')">
	<div class="card-header">
        <i class="material-icons chat-active">account_box</i><span class="d-none d-lg-inline"><?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/online_operators.tpl.php'));?></span> ({{online_op.list.length}}{{online_op.list.length == lhc.limito ? '+' : ''}}) <span class="text-success" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Online');?>">{{online_op.op_on}}</span></a>

		<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('ooperators_widget_exp')" class="fs24 float-end material-icons exp-cntr">{{lhc.toggleWidgetData['ooperators_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>

        <?php $takenTimeAttributes = 'online_op.tt';?>
        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/taken_time.tpl.php'));?>

	</div>
	<div ng-if="lhc.toggleWidgetData['ooperators_widget_exp'] !== true">  
  
        <?php $optinsPanel = array('panelid' => 'operatord', 'limitid' => 'limito', 'disable_product' => true,'userid' => 'oopu'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>

        <div class="panel-list" id="operatord-panel-list" ng-style="{'maxHeight': lhc.operatord_m_h}">
            <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/online_operators.tpl.php'));?>
		</div>
		
	</div>
</div>*/ ?>
