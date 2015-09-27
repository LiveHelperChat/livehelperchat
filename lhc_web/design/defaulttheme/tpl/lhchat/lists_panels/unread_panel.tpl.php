<?php if ($unreadTabEnabled == true) : ?>			
<div class="panel-heading" ng-if="unread_chats.list.length > 0"><a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(hum)/1"><i class="material-icons chat-unread">chat</i><?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/titles/unread_chats.tpl.php'));?> ({{unread_chats.list.length}}{{unread_chats.list.length == 10 ? '+' : ''}})</a><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleList('unread_chats_expanded')" class="fs24 pull-right material-icons exp-cntr">{{unread_chats_expanded == true ? 'expand_less' : 'expand_more'}}</a></div>
<div class="panel-body" ng-if="unread_chats.list.length > 0 && unread_chats_expanded == true" id="right-unread-chats">
	<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_unread_list.tpl.php'));?>
</div>                        
<?php endif;?>