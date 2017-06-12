<?php if ($mchatsTabEnabled == true) : ?> 
<div class="panel-heading" ng-if="my_chats.list.length > 0"><a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(user_id)/<?php echo erLhcoreClassUser::instance()->getUserID()?>"><i class="material-icons chat-active">account_box</i><?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/titles/my_chats.tpl.php'));?> ({{my_chats.list.length}}{{my_chats.list.length == 10 ? '+' : ''}})</a><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleList('my_chats_expanded')" class="fs24 pull-right material-icons exp-cntr">{{my_chats_expanded == true ? 'expand_less' : 'expand_more'}}</a></div>
<div class="panel-body"  id="right-my-chats" ng-show="my_chats.list.length > 0 && my_chats_expanded == true">
		<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_my_chats_list.tpl.php'));?>
</div>                     
<?php endif;?>
