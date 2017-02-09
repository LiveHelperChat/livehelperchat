<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_online_op_list_tab_pre.tpl.php')); ?>
<?php if ($chat_lists_online_operators_enabled == true) : ?>
<ul class="list-unstyled">
	<li ng-repeat="operator in online_op.list track by operator.id" ><a ng-show="operator.user_id != <?php echo erLhcoreClassUser::instance()->getUserID();?>" href="#" ng-click="lhc.startChatOperator(operator.user_id)" class="small button round"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Start chat');?></a>&nbsp;{{operator.name_official}} | <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Last activity');?>: {{operator.lastactivity_ago}} <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','ago');?>.</li>
</ul>
<?php endif;?>