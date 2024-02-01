<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhmailconv', 'use_admin')) : ?>

    <?php $optinsPanel = array('panelid' => 'mmd','limitid' => 'limitmm', 'disable_product' => true);    ?>
    <lhc-widget base_url="mailconv/conversations/(sortby)/statuspriority/(conversation_status_ids)/1/0" column_1_width="60%" column_2_width="20%" no_additional_column="true" <?php if (isset($customCardTitleClass)) : ?>custom_title_class="<?php echo $customCardTitleClass?>"<?php endif; ?> <?php if (isset($customCardNoDuration)) : ?>no_duration="<?php echo $customCardNoDuration?>"<?php endif; ?> column_2_width="25%" card_icon="mail_outline" <?php if (isset($rightPanelMode)) : ?>right_panel_mode="true"<?php endif; ?> <?php if (isset($hideCardHeader)) : ?>hide_header="true"<?php endif;?> icon_class="chat-active" list_identifier="my-mail" type="my_mails" optionsPanel='<?php echo json_encode($optinsPanel)?>' www_dir_flags="<?php echo erLhcoreClassDesign::design('images/flags');?>" expand_identifier="my_mails_widget_exp" status_id="<?php echo erLhcoreClassUser::instance()->getUserID()?>" status_key="user_ids" panel_list_identifier="mmd-panel-list"></lhc-widget>

<?php /*
<div class="<?php if (!isset($rightPanelMode)) : ?>card card-dashboard card-my-mails<?php endif; ?>" ng-class="{'has-chats' : my_mails.list.length > 0}" data-panel-id="my_mails" ng-init="lhc.getToggleWidget('my_mails_widget_exp')">
    <div class="card-header">
        <a class="title-card-header" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/conversations')?>/(sortby)/statuspriority/(conversation_status_ids)/1/0/(user_ids)/<?php echo erLhcoreClassUser::instance()->getUserID()?>"><i class="material-icons chat-active">account_box</i> <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/my_mails.tpl.php'));?> ({{my_mails.list.length}}{{my_mails.list.length == lhc.limitmm ? '+' : ''}})</a>
        <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('my_mails_widget_exp')" class="fs24 float-end material-icons exp-cntr">{{lhc.toggleWidgetData['my_mails_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>
        <?php $takenTimeAttributes = 'my_mails.tt';?>
        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/taken_time.tpl.php'));?>
    </div>

    <div ng-if="lhc.toggleWidgetData['my_mails_widget_exp'] !== true">

        <?php $optinsPanel = array('panelid' => 'mmd','limitid' => 'limitmm', 'disable_product' => true); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>

        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/my_mails.tpl.php'));?>

        <div ng-if="!my_mails || my_mails.list.length == 0" class="m-1 alert alert-light"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Mails assigned to you will appear here. List includes new and active mails.')?></div>

    </div>
</div>*/ ?>

<?php endif; ?>