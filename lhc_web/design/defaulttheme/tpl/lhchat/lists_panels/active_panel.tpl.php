<?php if ($activeTabEnabled == true) : ?>

<?php if (!isset($hideCardHeader)) : ?>
        <div class="card-header"><a class="title-card-header" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(chat_status_ids)/1"><i class="material-icons chat-active">chat</i><span class="d-none d-lg-inline"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/titles/active_chats.tpl.php'));?></span> ({{active_chats.list.length}}{{active_chats.list.length == 10 ? '+' : ''}})</a><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleList('active_chats_expanded')" class="fs24 float-end material-icons exp-cntr">{{active_chats_expanded == true ? 'expand_less' : 'expand_more'}}</a></div>
<div id="right-active-chats" ng-show="active_chats_expanded == true">
    <?php endif; ?>

        <?php $optinsPanel = array('hide_tooltip' => true, 'panelid' => 'actived','limitid' => 'limita', 'userid' => 'activeu'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>

        <div class="panel-list">
		  <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_active_list.tpl.php'));?>
		</div>
<?php if (!isset($hideCardHeader)) : ?></div><?php endif;?>
<?php endif;?>