<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhmailconv', 'use_admin')) : ?>

    <?php $optinsPanel = array('panelid' => 'pendingmd','limitid' => 'limitpm', 'userid' => 'pendingmu', 'disable_product' => true); ?>
    <lhc-widget data_panel_id="pmails" no_link="true" column_1_width="60%" column_2_width="20%" column_3_width="20%" no_additional_column="true" <?php if (isset($customCardTitleClass)) : ?>custom_title_class="<?php echo $customCardTitleClass?>"<?php endif; ?> <?php if (isset($customCardNoDuration)) : ?>no_duration="<?php echo $customCardNoDuration?>"<?php endif; ?> column_2_width="25%" card_icon="mail_outline" <?php if (isset($rightPanelMode)) : ?>right_panel_mode="true"<?php endif; ?> <?php if (isset($hideCardHeader)) : ?>hide_header="true"<?php endif;?> icon_class="chat-pending" list_identifier="pending-mails" type="pending_mails" optionsPanel='<?php echo json_encode($optinsPanel)?>' www_dir_flags="<?php echo erLhcoreClassDesign::design('images/flags');?>" expand_identifier="pmails_widget_exp" panel_list_identifier="pendingmd-panel-list"></lhc-widget>

<?php /*
<div class="<?php if (!isset($rightPanelMode)) : ?>card card-dashboard card-pending-mails<?php endif; ?>" ng-class="{'has-chats' : pending_mails.list.length > 0}" data-panel-id="pmails" ng-init="lhc.getToggleWidget('pmails_widget_exp');">

    <div class="card-header">
        <i class="material-icons chat-pending">mail_outline</i><span class="title-card-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','New mails')?> ({{pending_mails.list.length}}{{pending_mails.list.length == lhc.limitpm ? '+' : ''}})</span>
        <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('pmails_widget_exp')" class="fs24 float-end material-icons exp-cntr">{{lhc.toggleWidgetData['pmails_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>
        <?php $takenTimeAttributes = 'pending_mails.tt';?>
        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/taken_time.tpl.php'));?>
    </div>
    <div ng-if="lhc.toggleWidgetData['pmails_widget_exp'] !== true">

        <?php $optinsPanel = array('panelid' => 'pendingmd','limitid' => 'limitpm', 'userid' => 'pendingmu', 'disable_product' => true); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>

        <div class="panel-list" ng-if="pending_mails.list.length > 0">
            <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/pending_mail.tpl.php'));?>
        </div>

        <div ng-if="pending_mails.list.length == 0" class="m-1 alert alert-info"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','All new mails will appear here.')?></div>

    </div>
</div>*/ ?>

<?php endif; ?>