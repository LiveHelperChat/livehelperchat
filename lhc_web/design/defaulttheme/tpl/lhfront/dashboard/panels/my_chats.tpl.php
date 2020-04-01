<?php if ($currentUser->hasAccessTo('lhchat','use')) : ?>
    <div class="card card-dashboard" data-panel-id="my_chats" ng-init="lhc.getToggleWidget('my_chats_widget_exp')">
        <div class="card-header">
        <a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(user_id)/<?php echo erLhcoreClassUser::instance()->getUserID()?>"><i class="material-icons chat-active">account_box</i> <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/my_active_chats.tpl.php'));?> ({{my_chats.list.length}}{{my_chats.list.length == lhc.limitmc ? '+' : ''}})</a>
            <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('my_chats_widget_exp')" class="fs24 float-right material-icons exp-cntr">{{lhc.toggleWidgetData['my_chats_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>
        </div>

        <div ng-if="lhc.toggleWidgetData['my_chats_widget_exp'] !== true">

            <?php $optinsPanel = array('panelid' => 'mcd','limitid' => 'limitmc'); ?>
            <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>


            <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/my_chats.tpl.php'));?>

            <div ng-if="!my_chats || my_chats.list.length == 0" class="m-1 alert alert-info"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Chats assigned to you will appear here. List includes pending and active chats.')?></div>

        </div>
    </div>
<?php endif; ?>