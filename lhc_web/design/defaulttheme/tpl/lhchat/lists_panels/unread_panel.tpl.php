<?php if ($unreadTabEnabled == true) : ?>			
<div class="card-header" ng-if="unread_chats.list.length > 0"><a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(hum)/1"><i class="material-icons chat-unread">&#xfb55;</i><?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/titles/unread_chats.tpl.php'));?> ({{unread_chats.list.length}}{{unread_chats.list.length == 10 ? '+' : ''}})</a><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleList('unread_chats_expanded')" class="fs24 float-right material-icons exp-cntr">{{unread_chats_expanded == true ? '&#xf143;' : '&#xf140;'}}</a></div>
<div ng-if="unread_chats_expanded == true" id="right-unread-chats">
	<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_unread_list.tpl.php'));?>
</div>                        
<?php endif;?>