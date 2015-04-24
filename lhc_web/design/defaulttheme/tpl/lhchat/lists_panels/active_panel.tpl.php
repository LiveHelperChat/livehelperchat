<?php if ($activeTabEnabled == true) : ?> 
<div class="panel-heading" ng-if="active_chats.list.length > 0"><a href="<?php echo erLhcoreClassDesign::baseurl('chat/activechats')?>"><i class="icon-chat chat-active"></i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Active chats');?> ({{active_chats.list.length}}{{active_chats.list.length == 10 ? '+' : ''}})</a><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleList('active_chats_expanded')" ng-class="active_chats_expanded == true ? 'icon-minus' : 'icon-plus'" class="fs18 pull-right"></a></div>
<div class="panel-body"  id="right-active-chats" ng-show="active_chats.list.length > 0 && active_chats_expanded == true">
		<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_active_list.tpl.php'));?>
</div>                     
<?php endif;?>