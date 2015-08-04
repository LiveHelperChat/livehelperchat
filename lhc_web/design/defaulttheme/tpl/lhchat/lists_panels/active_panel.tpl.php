<?php if ($activeTabEnabled == true) : ?> 
<div class="panel-heading" ng-if="active_chats.list.length > 0"><a href="<?php echo erLhcoreClassDesign::baseurl('chat/activechats')?>"><i class="material-icons chat-active">chat</i><?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/titles/active_chats.tpl.php'));?> ({{active_chats.list.length}}{{active_chats.list.length == 10 ? '+' : ''}})</a><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleList('active_chats_expanded')" class="fs24 pull-right material-icons">{{active_chats_expanded == true ? 'expand_less' : 'expand_more'}}</a></div>
<div class="panel-body"  id="right-active-chats" ng-show="active_chats.list.length > 0 && active_chats_expanded == true">
		<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_active_list.tpl.php'));?>
</div>                     
<?php endif;?>