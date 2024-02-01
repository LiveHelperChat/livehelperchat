<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhmailconv', 'use_admin')) : ?>

    <?php $optinsPanel = array('panelid' => 'activemd','limitid' => 'limitam', 'userid' => 'activemu', 'disable_product' => true); ?>
    <lhc-widget data_panel_id="amails" no_link="true" column_1_width="60%" column_2_width="20%" column_3_width="20%" no_additional_column="true" <?php if (isset($customCardTitleClass)) : ?>custom_title_class="<?php echo $customCardTitleClass?>"<?php endif; ?> <?php if (isset($customCardNoDuration)) : ?>no_duration="<?php echo $customCardNoDuration?>"<?php endif; ?> column_2_width="25%" card_icon="mail_outline" <?php if (isset($rightPanelMode)) : ?>right_panel_mode="true"<?php endif; ?> <?php if (isset($hideCardHeader)) : ?>hide_header="true"<?php endif;?> icon_class="chat-active" list_identifier="active-mails" type="active_mails" optionsPanel='<?php echo json_encode($optinsPanel)?>' www_dir_flags="<?php echo erLhcoreClassDesign::design('images/flags');?>" expand_identifier="amails_widget_exp" panel_list_identifier="activemd-panel-list"></lhc-widget>


<?php /*
<div class="<?php if (!isset($rightPanelMode)) : ?>card card-dashboard card-active-mails<?php endif; ?>" data-panel-id="amails" ng-init="lhc.getToggleWidget('amails_widget_exp');">
    <div class="card-header">
        <i class="material-icons chat-active">mail_outline</i>
        <span class="title-card-header">
        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Active mails')?> ({{active_mails.list.length}}{{active_mails.list.length == lhc.limitam ? '+' : ''}})
        </span>
        <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('amails_widget_exp')" class="fs24 float-end material-icons exp-cntr">{{lhc.toggleWidgetData['amails_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>
        <?php $takenTimeAttributes = 'active_mails.tt';?>
        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/taken_time.tpl.php'));?>
    </div>
    <div ng-if="lhc.toggleWidgetData['amails_widget_exp'] !== true">

        <?php $optinsPanel = array('panelid' => 'activemd','limitid' => 'limitam', 'userid' => 'activemu', 'disable_product' => true); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>

        <div class="panel-list" ng-if="active_mails.list.length > 0">
            <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/active_mail.tpl.php'));?>
        </div>

        <div ng-if="active_mails.list.length == 0" class="m-1 alert alert-info"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Active mail conversations will appear here.')?></div>

    </div>
</div>*/ ?>

<?php endif; ?>