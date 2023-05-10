<div translate="no" ng-class="{hide: rmtoggle}" class="col-2 chats-column d-flex border-right pe-0 ps-0"">
<?php /*col chats-column*/ ?>
    <div class="w-100 d-flex flex-column flex-grow-1">
        <div class="clearfix bg-light">
            <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/open_active_chat_tab.tpl.php')); ?>

            <div class="text-muted p-2 float-start"><i class="material-icons me-0">list</i><span class="fs13 fw-bold" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Chats I have presently opened'); ?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Chats'); ?></span></div>
            <a class="d-inline-block pt-2 pe-1 float-end text-secondary d-none d-md-block"  onclick="return lhc.revealModal({'url':WWW_DIR_JAVASCRIPT +'chat/dashboardwidgets'})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Configure dashboard')?>"><i class="material-icons me-0">&#xE871;</i></a>

            <?php if (in_array('online_users',$frontTabsOrder)) : ?>
                <a class="d-inline-block pt-2 pe-1 float-end text-secondary d-none d-md-block" onclick="$('#tabs a[href=\'#onlineusers\']').tab('show')"><i class="material-icons md-18">face</i></a>
            <?php endif; ?>

            <?php if (in_array('online_map',$frontTabsOrder)) : ?>
                <a class="d-inline-block pt-2 pe-1 float-end text-secondary d-none d-md-block" onclick="$('#tabs a[href=\'#map\']').tab('show')"><i class="material-icons md-18">place</i></a>
            <?php endif; ?>

            <a class="d-inline-block pt-2 pe-1 float-end text-secondary d-none d-md-block" onclick="$('#tabs a[href=\'#dashboard\']').tab('show')"><i class="material-icons md-18">home</i></a>

            <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/icons/icons_multiinclude.tpl.php')); ?>
        </div>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/basic_chat_enabled.tpl.php'));?>

        <div role="tabpanel" class="border-top">

            <?php if ($left_list_option == 0) : ?>
                <ul class="nav nav-underline nav-small nav-fill mb-0 pb-0 border-bottom" role="tablist" id="sub-tabs">
                    <li role="presentation" class="nav-item">
                        <a class="nav-link active" href="#sub-tabs-open" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Open chats'); ?>" aria-controls="sub-tabs-open" role="tab" data-bs-toggle="tab" aria-selected="true">
                            <i class="material-icons chat-active">question_answer</i>
                        </a>
                    </li>
                    <?php if ($basicChatEnabled == true) : ?>
                        <li role="presentation" class="nav-item">
                            <a class="nav-link" title="<?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/titles/my_chats.tpl.php'));?>" href="#sub-tabs-my-assigned" aria-controls="sub-tabs-my-assigned" role="tab" data-bs-toggle="tab" aria-selected="true">
                                <i class="material-icons chat-active">account_box</i><span class="text-muted fs11 fw-bold">({{my_chats.list.length}}{{my_chats.list.length == 10 ? '+' : ''}})</span>
                            </a>
                        </li>
                        <li role="presentation" class="nav-item">
                            <a class="nav-link" href="#sub-tabs-pending" title="<?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/titles/pending_chats.tpl.php'));?>" aria-controls="sub-tabs-pending" role="tab" data-bs-toggle="tab" aria-selected="true">
                                <i class="material-icons chat-pending">chat</i><span class="text-muted fs11 fw-bold">({{pending_chats.list.length}}{{pending_chats.list.length == 10 ? '+' : ''}})</span>
                            </a>
                        </li>
                        <li role="presentation" class="nav-item">
                            <a class="nav-link" href="#sub-tabs-active" title="<?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/active_chats.tpl.php'));?>" aria-controls="sub-tabs-active" role="tab" data-bs-toggle="tab" aria-selected="true">
                                <i class="material-icons chat-active">chat</i><span class="text-muted fs11 fw-bold">({{active_chats.list.length}}{{active_chats.list.length == 10 ? '+' : ''}})</span>
                            </a>
                        </li>
                        <li role="presentation" class="nav-item">
                            <a class="nav-link" href="#sub-tabs-bot" title="<?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/titles/bot_chats.tpl.php'));?>" aria-controls="sub-tabs-bot" role="tab" data-bs-toggle="tab" aria-selected="true">
                                <i class="material-icons chat-active">android</i><span class="text-muted fs11 fw-bold">({{bot_chats.list.length}}{{bot_chats.list.length == lhc.limitb ? '+' : ''}})</span>
                            </a>
                        </li>
                    <?php endif;?>
                </ul>
            <?php endif; ?>

            <div class="tab-content sub-tabs-content">
                <div role="tabpanel" class="tab-pane active" id="sub-tabs-open">
                    <div id="tabs-dashboard"></div>

                    <?php if ($currentUser->hasAccessTo('lhgroupchat','use')) : ?>
                        <div class="border-top border-bottom bg-light card-header">
                            <div class="text-muted"><i class="material-icons">list</i><span class="fs13 fw-bold" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Group chats')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Grp.')?></span>
                                <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','collapse/expand')?>" ng-click="lhc.toggleWidget('group_chat_widget_exp')" class="fs24 float-end material-icons exp-cntr">{{lhc.toggleWidgetData['group_chat_widget_exp'] == false ? 'expand_less' : 'expand_more'}}</a>
                            </div>
                        </div>

                        <div ng-if="lhc.toggleWidgetData['group_chat_widget_exp'] !== true">
                            <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bodies/group_chats.tpl.php'));?>
                        </div>
                    <?php endif;?>

                </div>

                <?php if ($basicChatEnabled == true && $left_list_option == 0) : ?>
                    <div role="tabpanel" class="tab-pane" id="sub-tabs-my-assigned">
                        <?php $rightPanelMode = true; $hideCardHeader = true; ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/my_chats_panel.tpl.php'));?>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="sub-tabs-pending">
                        <?php $rightPanelMode = true; $hideCardHeader = true; ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/pending_panel.tpl.php'));?>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="sub-tabs-active">
                        <?php $rightPanelMode = true; $hideCardHeader = true; ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/active_panel.tpl.php'));?>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="sub-tabs-bot">
                        <?php $rightPanelMode = true; $hideCardHeader = true; ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/bot_chats.tpl.php'));?>
                        <?php unset($rightPanelMode); unset($hideCardHeader); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($basicChatEnabled == true && $left_list_option == 1) : ?>
            <div class="dashboard-panels d-flex flex-column flex-grow-1" style="position:relative">
                <?php $hideCard = true; ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/lists_panels/right_panel_container.tpl.php'));?>
            </div>
        <?php endif; ?>

    </div>
</div>