<?php if ($currentUser->hasAccessTo('lhchat','use')) : ?>
    <div class="card card-dashboard" data-panel-id="bot_chats" ng-init="lhc.getToggleWidget('botc_widget_exp');lhc.getToggleWidgetSort('bot_chats_sort')">
        <div class="card-header">
            <i class="material-icons mr-0 action-image" onclick="return lhc.revealModal({'url':WWW_DIR_JAVASCRIPT +'genericbot/notifications'})">settings_applications</i>

            <a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(chat_status)/5">
                <i class="material-icons chat-active">chat</i> <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/bot_chats.tpl.php'));?> ({{bot_chats.list.length}}{{bot_chats.list.length == lhc.limitb ? '+' : ''}})</a>
            <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('botc_widget_exp')" class="fs24 float-right material-icons exp-cntr">{{lhc.toggleWidgetData['botc_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>
        </div>

        <?php if (erLhcoreClassModelUserSetting::getSetting('enable_bot_list',1) == 1) : ?>
        <div ng-if="lhc.toggleWidgetData['botc_widget_exp'] !== true">
            <?php $optinsPanel = array('panelid' => 'botd', 'limitid' => 'limitb'); ?>
            <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>

            <div ng-if="bot_chats.list.length > 0" class="panel-list">
                <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/bot.tpl.php'));?>
            </div>

            <div ng-if="bot_chats.list.length == 0" class="m-1 alert alert-info"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Bot chats will appear here.')?>...</div>
        </div>
        <?php else : ?>
            <div class="m-1 alert alert-info"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Please enable bot chats list in your account!')?></div>
        <?php endif; ?>
    </div>
<?php endif; ?>