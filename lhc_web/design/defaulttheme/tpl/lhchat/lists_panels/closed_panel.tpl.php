<?php if ($closedTabEnabled == true) : ?>        	
<div class="panel-heading" ng-if="closed_chats.list.length > 0"><a href="<?php echo erLhcoreClassDesign::baseurl('chat/closedchats')?>"><i class="icon-cancel-circled chat-closed"></i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Closed chats');?> ({{closed_chats.list.length}}{{closed_chats.list.length == 10 ? '+' : ''}})</a><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleList('closed_chats_expanded')" ng-class="closed_chats_expanded == true ? 'icon-minus' : 'icon-plus'" class="fs18 pull-right"></a></div>
<div class="panel-body" id="right-closed-chats" ng-if="closed_chats.list.length > 0 && closed_chats_expanded == true">
		<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_closed_list.tpl.php'));?>  
</div>            
<?php endif;?>   