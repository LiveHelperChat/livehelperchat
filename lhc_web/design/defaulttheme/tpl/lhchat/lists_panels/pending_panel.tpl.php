<?php if ($pendingTabEnabled == true) : ?>
<div class="panel-heading" ng-if="pending_chats.list.length > 0"><a href="<?php echo erLhcoreClassDesign::baseurl('chat/pendingchats')?>"><i class="icon-chat chat-pending"></i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Pending chats');?> ({{pending_chats.list.length}}{{pending_chats.list.length == 10 ? '+' : ''}})</a><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleList('pending_chats_expanded')" ng-class="pending_chats_expanded == true ? 'icon-minus' : 'icon-plus'" class="fs18 pull-right"></a></div>
<div class="panel-body" id="right-pending-chats" ng-if="pending_chats.list.length > 0 && pending_chats_expanded == true">
		<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_pending_list.tpl.php'));?>
</div>
<?php endif;?>