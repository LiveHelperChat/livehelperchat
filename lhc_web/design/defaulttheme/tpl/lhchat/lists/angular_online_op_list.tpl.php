<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_online_op_list_tab_pre.tpl.php')); ?>
<?php if ($chat_lists_online_operators_enabled == true) : ?>
<ul class="list-unstyled fs12">
	<li ng-repeat="operator in online_op.list track by operator.id" ><a ng-show="operator.user_id != <?php echo erLhcoreClassUser::instance()->getUserID();?>" href="#" ng-click="lhc.startChatOperator(operator.user_id)" class="btn btn-default btn-xs" ng-class="{'btn-warning':online_op.op_sn.indexOf(operator.user_id) !== -1}"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Start chat');?></a>&nbsp;{{operator.name_official}} | <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Last activity');?>: {{operator.lastactivity_ago}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','ago');?>.</li>
</ul>
<?php endif;?>