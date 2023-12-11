<?php if ($currentUser->hasAccessTo('lhchat','use')) : ?>


    <lhc-widget icon_class="chat-active" list_identifier="subject" type="subject_chats" no_link="true" column_1_Width="27%" column_2_Width="18%" column_3_Width="10%" card_icon="label" <?php if ($currentUser->hasAccessTo('lhchat','subject_chats_options')) : ?>custom_settings_url="chat/subjectwidget"<?php endif; ?> optionsPanel='<?php echo json_encode(array('panelid' => 'subjectd', 'limitid' => 'limits', 'userid' => 'subjectu'))?>' www_dir_flags="<?php echo erLhcoreClassDesign::design('images/flags');?>" expand_identifier="subjectc_widget_exp" panel_list_identifier="subjectd-panel-list"></lhc-widget>
    <?php /*
    <div class="card card-dashboard card-subject" data-panel-id="subject_chats" ng-init="lhc.getToggleWidget('subjectc_widget_exp');">
        <div class="card-header">

        <?php if ($currentUser->hasAccessTo('lhchat','subject_chats_options')) : ?>
                <i class="material-icons me-0 action-image" onclick="return lhc.revealModal({'url':WWW_DIR_JAVASCRIPT +'chat/subjectwidget'})">settings_applications</i>
        <?php endif; ?>

            <i class="material-icons chat-active">label</i><span class="d-none d-lg-inline"><?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/subject_chats.tpl.php'));?></span>&nbsp;<?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/subject_chats_counter.tpl.php'));?>

            <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('subjectc_widget_exp')" class="fs24 float-end material-icons exp-cntr">{{lhc.toggleWidgetData['subjectc_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>

            <?php $takenTimeAttributes = 'subject_chats.tt';?>
            <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/taken_time.tpl.php'));?>
        </div>

        <div ng-if="lhc.toggleWidgetData['subjectc_widget_exp'] !== true">

            <?php $optinsPanel = array('panelid' => 'subjectd', 'limitid' => 'limits', 'userid' => 'subjectu'); ?>
            <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>

            <div ng-if="subject_chats.list.length > 0" class="panel-list">
                <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/subject.tpl.php'));?>
            </div>

            <div ng-if="subject_chats.list.length == 0" class="m-1 alert alert-light"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Subject filtered chats will appear here.')?></div>

        </div>
    </div>*/ ?>


<?php endif; ?>