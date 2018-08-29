<?php if ($pendingTabEnabled == true) : ?>
<div class="panel-heading" ng-if="pending_chats.list.length > 0"><a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(chat_status)/0"><i class="material-icons chat-pending">chat</i><?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/titles/pending_chats.tpl.php'));?> ({{pending_chats.list.length}}{{pending_chats.list.length == 10 ? '+' : ''}})</a><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleList('pending_chats_expanded')" class="fs24 pull-right material-icons exp-cntr">{{pending_chats_expanded == true ? 'expand_less' : 'expand_more'}}</a></div>
<div id="right-pending-chats" ng-if="pending_chats.list.length > 0 && pending_chats_expanded == true">

    <?php $optinsPanel = array('panelid' => 'pendingd','limitid' => 'limitp', 'userid' => 'pendingu'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>

    <div class="panel-list">
		<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_pending_list.tpl.php'));?>
    </div>
</div>
<?php endif;?>