<?php if ($currentUser->hasAccessTo('lhgroupchat','use')) : ?>
    <div class="card card-dashboard" data-panel-id="group_chats" ng-init="lhc.getToggleWidget('group_chat_widget_exp')">
        <div class="card-header">
            <i class="material-icons chat-active">group</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Group chats');?> ({{group_chats.list.length}}{{group_chats.list.length == lhc.limitgc ? '+' : ''}})
            <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('group_chat_widget_exp')" class="fs24 float-right material-icons exp-cntr">{{lhc.toggleWidgetData['group_chat_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>
        </div>

        <div ng-if="lhc.toggleWidgetData['group_chat_widget_exp'] !== true">

            <?php $optinsPanel = array('panelid' => 'gct','limitid' => 'limitgc'); ?>
            <div class="p-2">
                <div class="row">
                    <div class="col-12">
                        <select class="form-control form-control-sm btn-light" ng-model="lhc.<?php echo $optinsPanel['limitid']?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Number of elements in list');?>">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>

            <div ng-if="!group_chats || group_chats.list.length == 0" class="m-1 alert alert-info"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Group chats will appear here.')?></div>

            <div class="panel-list">
                <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/group_chats.tpl.php'));?>
            </div>

        </div>
    </div>
<?php endif; ?>