<div class="card card-dashboard" data-panel-id="malarms" ng-init="lhc.getToggleWidget('malarms_widget_exp');">
    <div class="card-header">
        <i class="material-icons mr-0 action-image" onclick="return lhc.revealModal({'url':WWW_DIR_JAVASCRIPT +'mailconv/notifications'})">settings_applications</i><i class="material-icons chat-closed">mail_outline</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Alarm mails')?> ({{alarm_mails.list.length}}{{alarm_mails.list.length == lhc.limitam ? '+' : ''}})
        <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('malarms_widget_exp')" class="fs24 float-right material-icons exp-cntr">{{lhc.toggleWidgetData['malarms_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>
    </div>
    <div ng-if="lhc.toggleWidgetData['malarms_widget_exp'] !== true">

        <?php $optinsPanel = array('panelid' => 'alarmmd','limitid' => 'limitalm', 'userid' => 'alarmmu', 'disable_product' => true); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>

        <div class="panel-list" ng-if="alarm_mails.list.length > 0">
            <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/alarm_mail.tpl.php'));?>
        </div>

        <div ng-if="alarm_mails.list.length == 0" class="m-1 alert alert-info"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Mail alarms will appear here.')?></div>
    </div>
</div>