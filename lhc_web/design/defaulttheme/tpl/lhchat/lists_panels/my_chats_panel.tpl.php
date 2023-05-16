<?php if ($mchatsTabEnabled == true) : ?>

<?php if (!isset($hideCardHeader)) : ?>
        <div class="card-header card-header-my-chats" ng-class="{'has-chats':my_chats.list.length > 0}"><a class="title-card-header" href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(user_id)/<?php echo erLhcoreClassUser::instance()->getUserID()?>"><i class="material-icons chat-active">account_box</i><span class="d-none d-lg-inline"><?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/titles/my_chats.tpl.php'));?></span> ({{my_chats.list.length}}{{my_chats.list.length == 10 ? '+' : ''}})</a><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleList('my_chats_expanded')" class="fs24 float-end material-icons exp-cntr">{{my_chats_expanded == true ? 'expand_less' : 'expand_more'}}</a></div>
<div id="right-my-chats" ng-show="my_chats_expanded == true">
    <?php endif;?>

        <?php $optinsPanel = array('panelid' => 'mcd','limitid' => 'limitmc'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>

		<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_my_chats_list.tpl.php'));?>
    <?php if (!isset($hideCardHeader)) : ?>
</div>                     
<?php endif;?>
<?php endif;?>
