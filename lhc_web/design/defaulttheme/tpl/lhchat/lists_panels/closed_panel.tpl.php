<?php if ($closedTabEnabled == true) : ?>        	
<div class="card-header" ng-if="closed_chats.list.length > 0"><a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(chat_status)/2"><i class="material-icons chat-closed">&#xfb55;</i><?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/titles/closed_chats.tpl.php'));?> ({{closed_chats.list.length}}{{closed_chats.list.length == 10 ? '+' : ''}})</a><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleList('closed_chats_expanded')" class="fs24 float-right material-icons exp-cntr">{{closed_chats_expanded == true ? '&#xf143;' : '&#xf140;'}}</a></div>
<div class="card-body" id="right-closed-chats" ng-if="closed_chats.list.length > 0 && closed_chats_expanded == true">
		<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_closed_list.tpl.php'));?>  
</div>            
<?php endif;?>   