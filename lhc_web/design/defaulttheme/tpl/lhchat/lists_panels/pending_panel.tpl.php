<?php if ($pendingTabEnabled == true) : ?>
<div class="panel-heading" ng-if="pending_chats.list.length > 0"><a href="<?php echo erLhcoreClassDesign::baseurl('chat/pendingchats')?>"><i class="material-icons chat-pending">chat</i><?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/titles/pending_chats.tpl.php'));?> ({{pending_chats.list.length}}{{pending_chats.list.length == 10 ? '+' : ''}})</a><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleList('pending_chats_expanded')" class="fs24 pull-right material-icons">{{pending_chats_expanded == true ? 'expand_less' : 'expand_more'}}</a></div>
<div class="panel-body" id="right-pending-chats" ng-if="pending_chats.list.length > 0 && pending_chats_expanded == true">
		<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_pending_list.tpl.php'));?>
</div>
<?php endif;?>