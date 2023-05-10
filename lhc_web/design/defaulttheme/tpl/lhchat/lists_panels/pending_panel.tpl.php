<?php if ($pendingTabEnabled == true) : ?>

    <?php if (!isset($hideCardHeader)) : ?>
        <div class="card-header card-header-pending" ng-class="{'has-chats':pending_chats.list.length > 0}"><a class="title-card-header" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(chat_status_ids)/0"><i class="material-icons chat-pending">chat</i><span class="d-none d-lg-inline"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/titles/pending_chats.tpl.php'));?></span> ({{pending_chats.list.length}}{{pending_chats.list.length == 10 ? '+' : ''}})</a><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleList('pending_chats_expanded')" class="fs24 float-end material-icons exp-cntr">{{pending_chats_expanded == true ? 'expand_less' : 'expand_more'}}</a></div>
<div id="right-pending-chats" ng-if="pending_chats_expanded == true">
    <?php endif;?>

    <?php $optinsPanel = array('panelid' => 'pendingd','limitid' => 'limitp', 'userid' => 'pendingu'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>

    <div class="panel-list">
		<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_pending_list.tpl.php'));?>
    </div>
<?php if (!isset($hideCardHeader)) : ?>
</div>
<?php endif;?>
<?php endif;?>