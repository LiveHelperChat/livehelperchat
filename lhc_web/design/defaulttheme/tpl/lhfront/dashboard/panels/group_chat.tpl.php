<?php if ($currentUser->hasAccessTo('lhgroupchat','use')) : ?>

    <lhc-widget card_icon="group" <?php if (isset($customCardClass)) : ?>custom_card_class="<?php echo $customCardClass?>"<?php endif; ?>  <?php if (isset($rightPanelMode)) : ?>right_panel_mode="true"<?php endif; ?> <?php if (isset($hideCardHeader)) : ?>hide_header="true"<?php endif;?> icon_class="chat-active" list_identifier="group-chat" type="group_chats" optionsPanel='<?php echo json_encode(array('panelid' => 'gct','limitid' => 'limitgc','hide_department_filter' => true,'limits_width' => 12))?>' www_dir_flags="<?php echo erLhcoreClassDesign::design('images/flags');?>" expand_identifier="group_chat_widget_exp" panel_list_identifier="gct-panel-list"></lhc-widget>

    <?php /*<div class="card card-dashboard card-group-chat" data-panel-id="group_chats" ng-init="lhc.getToggleWidget('group_chat_widget_exp')">
        <div class="card-header">
            <i class="material-icons chat-active">group</i> <span class="d-none d-lg-inline"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Group chats');?></span> ({{group_chats.list.length}}{{group_chats.list.length == lhc.limitgc ? '+' : ''}})
            <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('group_chat_widget_exp')" class="fs24 float-end material-icons exp-cntr">{{lhc.toggleWidgetData['group_chat_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>

            <?php $takenTimeAttributes = 'group_chats.tt';?>
            <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/parts/taken_time.tpl.php'));?>
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

            <div ng-if="!group_chats || group_chats.list.length == 0" class="m-1 alert alert-light"><i class="material-icons">search</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Group chats will appear here.')?></div>

            <div class="panel-list">
                <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/group_chats.tpl.php'));?>
            </div>

        </div>
    </div>*/ ?>

<?php endif; ?>