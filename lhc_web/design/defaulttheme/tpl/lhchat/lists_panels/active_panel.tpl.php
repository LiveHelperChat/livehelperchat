<?php if ($activeTabEnabled == true) : ?> 
<div class="panel-heading"><a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(chat_status)/1"><i class="material-icons chat-active">chat</i><?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/titles/active_chats.tpl.php'));?> ({{active_chats.list.length}}{{active_chats.list.length == 10 ? '+' : ''}})</a><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleList('active_chats_expanded')" class="fs24 pull-right material-icons exp-cntr">{{active_chats_expanded == true ? 'expand_less' : 'expand_more'}}</a></div>

<div id="right-active-chats" ng-show="active_chats_expanded == true">
        <?php $optinsPanel = array('hide_tooltip' => true, 'panelid' => 'actived','limitid' => 'limita', 'userid' => 'activeu'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>

        <div class="panel-list">
		  <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_active_list.tpl.php'));?>
		</div>
</div>                     
<?php endif;?>