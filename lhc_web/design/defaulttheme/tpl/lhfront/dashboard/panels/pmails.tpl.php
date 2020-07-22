<div class="card card-dashboard" data-panel-id="pmails" ng-init="lhc.getToggleWidget('pmails_widget_exp');">
    <div class="card-header">
        <i class="material-icons chat-pending">mail_outline</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Pending mails')?> ({{pending_mails.list.length}}{{pending_mails.list.length == lhc.limitpm ? '+' : ''}})
        <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('pmails_widget_exp')" class="fs24 float-right material-icons exp-cntr">{{lhc.toggleWidgetData['pmails_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>
    </div>
    <div ng-if="lhc.toggleWidgetData['pmails_widget_exp'] !== true">

        <?php $optinsPanel = array('panelid' => 'pendingmd','limitid' => 'limitpm', 'userid' => 'pendingmu', 'disable_product' => true); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>

        <div class="panel-list" ng-if="pending_mails.list.length > 0">
            <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/pending_mail.tpl.php'));?>
        </div>

        <div ng-if="pending_mails.list.length == 0" class="m-1 alert alert-info"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','All pending mails will appear here.')?></div>

    </div>
</div>