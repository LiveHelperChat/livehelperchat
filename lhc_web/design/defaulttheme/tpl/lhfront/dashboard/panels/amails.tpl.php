<div class="card card-dashboard" data-panel-id="amails" ng-init="lhc.getToggleWidget('amails_widget_exp');">
    <div class="card-header">
        <i class="material-icons chat-active">mail_outline</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Active mails')?> ({{active_mails.list.length}}{{active_mails.list.length == lhc.limitam ? '+' : ''}})
        <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('amails_widget_exp')" class="fs24 float-right material-icons exp-cntr">{{lhc.toggleWidgetData['amails_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>
    </div>
    <div ng-if="lhc.toggleWidgetData['amails_widget_exp'] !== true">

        <?php $optinsPanel = array('panelid' => 'activemd','limitid' => 'limitam', 'userid' => 'activemu', 'disable_product' => true); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>

        <div class="panel-list" ng-if="active_mails.list.length > 0">
            <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/active_mail.tpl.php'));?>
        </div>

        <div ng-if="active_mails.list.length == 0" class="m-1 alert alert-info"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Active mail conversations will appear here.')?></div>

    </div>
</div>