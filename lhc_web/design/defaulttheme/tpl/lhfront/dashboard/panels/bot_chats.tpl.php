<?php if ($currentUser->hasAccessTo('lhchat','use')) : ?>
    <div class="panel panel-default panel-dashboard" data-panel-id="bot_chats" ng-init="lhc.getToggleWidget('botc_widget_exp');lhc.getToggleWidgetSort('bot_chats_sort')">
        <div class="panel-heading">
            <a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(chat_status)/5"><i class="material-icons chat-active">chat</i> <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/bot_chats.tpl.php'));?> ({{bot_chats.list.length}}{{bot_chats.list.length == lhc.limitb ? '+' : ''}})</a>
            <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('botc_widget_exp')" class="fs24 pull-right material-icons exp-cntr">{{lhc.toggleWidgetData['botc_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>
        </div>

        <div ng-if="lhc.toggleWidgetData['botc_widget_exp'] !== true">

            <?php $optinsPanel = array('panelid' => 'botd', 'limitid' => 'limitb'); ?>
            <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>

            <div ng-if="bot_chats.list.length > 0" class="panel-list">
                <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/bot.tpl.php'));?>
            </div>

            <div ng-if="bot_chats.list.length == 0" class="m10 alert alert-info"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Nothing found')?>...</div>

        </div>
    </div>
<?php endif; ?>