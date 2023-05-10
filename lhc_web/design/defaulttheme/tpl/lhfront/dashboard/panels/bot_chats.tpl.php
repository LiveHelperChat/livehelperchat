<?php if ($currentUser->hasAccessTo('lhchat','use')) : ?>

    <?php if (!isset($hideCardHeader)) : ?>
    <div ng-if="bot_chats" class="<?php if (!isset($rightPanelMode)) : ?>card card-dashboard card-bot-chats<?php endif; ?>" ng-class="{'has-chats' : bot_chats.list.length > 0}" data-panel-id="bot_chats" ng-init="lhc.getToggleWidget('botc_widget_exp');lhc.getToggleWidgetSort('bot_chats_sort')"><div class="card-header">
            <i class="material-icons me-0 action-image" onclick="return lhc.revealModal({'url':WWW_DIR_JAVASCRIPT +'genericbot/notifications'})">settings_applications</i>

            <a class="title-card-header" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(chat_status_ids)/5">
                <i class="material-icons chat-active">android</i> <span class="d-none d-lg-inline"><?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/bot_chats.tpl.php'));?></span> ({{bot_chats.list.length}}{{bot_chats.list.length == lhc.limitb ? '+' : ''}})</a>

                <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('botc_widget_exp')" class="fs24 float-end material-icons exp-cntr">{{lhc.toggleWidgetData['botc_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>

                <?php $takenTimeAttributes = 'bot_chats.tt';?>
                <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/taken_time.tpl.php'));?>
        </div>
    <?php endif; ?>

    <?php if (erLhcoreClassModelUserSetting::getSetting('enable_bot_list',1) == 1) : ?>
        <div ng-if="lhc.toggleWidgetData['botc_widget_exp'] !== true">
            <?php $optinsPanel = array('panelid' => 'botd', 'limitid' => 'limitb'); ?>
            <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>

            <div ng-if="bot_chats.list.length > 0" class="panel-list">
                <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/bot.tpl.php'));?>
            </div>

            <div ng-if="bot_chats.list.length == 0" class="m-1 alert alert-light"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Bot chats will appear here.')?>...</div>
        </div>
        <?php else : ?>
            <div class="m-1 alert alert-info"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Please enable bot chats list in your account!')?></div>
        <?php endif; ?>

    <?php if (!isset($hideCardHeader)) : ?>
    </div>
    <?php endif; ?>

<?php endif; ?>