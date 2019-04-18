<div class="card-header"><a href="<?php echo erLhcoreClassDesign::baseurl('chat/list')?>/(user_id)/<?php echo erLhcoreClassUser::instance()->getUserID()?>"><i class="material-icons chat-active">&#xf004;</i><?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/titles/my_chats.tpl.php'));?> ({{my_chats.list.length}}{{my_chats.list.length == 10 ? '+' : ''}})</a><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleList('my_chats_expanded')" class="fs24 float-right material-icons exp-cntr">{{my_chats_expanded == true ? '&#xf143;' : '&#xf140;'}}</a></div>
<div id="right-my-chats" ng-show="my_chats_expanded == true">
        <?php $optinsPanel = array('panelid' => 'mcd','limitid' => 'limitmc'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/options.tpl.php'));?>

        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_my_chats_list.tpl.php'));?>
</div>
