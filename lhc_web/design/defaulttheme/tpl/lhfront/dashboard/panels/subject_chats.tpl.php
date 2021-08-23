<?php if ($currentUser->hasAccessTo('lhchat','use')) : ?>
    <div class="card card-dashboard" data-panel-id="subject_chats" ng-init="lhc.getToggleWidget('subjectc_widget_exp');">
        <div class="card-header">

        <?php if ($currentUser->hasAccessTo('lhchat','subject_chats_options')) : ?>
                <i class="material-icons mr-0 action-image" onclick="return lhc.revealModal({'url':WWW_DIR_JAVASCRIPT +'chat/subjectwidget'})">settings_applications</i>
        <?php endif; ?>

            <a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(chat_status_ids)/1"><i class="material-icons chat-active">label</i> <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/subject_chats.tpl.php'));?>
                <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/subject_chats_counter.tpl.php'));?>
            </a>
            <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('subjectc_widget_exp')" class="fs24 float-right material-icons exp-cntr">{{lhc.toggleWidgetData['subjectc_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>
        </div>

        <div ng-if="lhc.toggleWidgetData['subjectc_widget_exp'] !== true">

            <?php $optinsPanel = array('panelid' => 'subjectd', 'limitid' => 'limits', 'userid' => 'subjectu'); ?>
            <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>

            <div ng-if="subject_chats.list.length > 0" class="panel-list">
                <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/subject.tpl.php'));?>
            </div>

            <div ng-if="active_chats.list.length == 0" class="m-1 alert alert-light"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Subject filtered chats will appear here.')?></div>

        </div>
    </div>
<?php endif; ?>