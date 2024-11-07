<nav class="float-end">
    <ul class="nav">
        <li class="nav-item dropend">
            <a class="nav-link dropdown-toggle text-secondary ps-2 pe-2" id="menu-chat-options" data-bs-toggle="dropdown" role="button" aria-expanded="false"><i class="material-icons me-0">settings_applications</i></a>
            <div class="dropdown-menu" role="menu">
                <a class="dropdown-item" href="#" onclick="ee.emitEvent('svelteAppendActiveChats');" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Open last 10 my active chats')?>"><i class="material-icons chat-active">chat</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Open my active chats'); ?></a>
                <a class="dropdown-item" href="#" id="track_open_chats" onclick="ee.emitEvent('svelteToggleWidget',['track_open_chats']);" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Last 10 your active chats will be always visible')?>"><i class="material-icons">done</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Keep my active chats'); ?></a>
                <a class="dropdown-item" href="#" id="group_offline_chats" onclick="ee.emitEvent('svelteToggleWidget',['group_offline_chats']);" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Hide nicknames for offline chats')?>"><i class="material-icons" id="group-chats-status">done</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Hide nicknames for offline chats'); ?></a>
                <a class="dropdown-item csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('front/settings')?>/(action)/reset" ><i class="material-icons">search_off</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Reset widget filters'); ?></a>

                <?php if ($currentUser->hasAccessTo('lhfront','switch_dashboard')) : ?>
                <a class="dropdown-item csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('front/switchdashboard')?>" >
                        <i class="material-icons">home</i>
                        <?php if ((int)erLhcoreClassModelUserSetting::getSetting('new_dashboard',1) == 1) : ?>
                            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Old dashboard'); ?>
                        <?php else : ?>
                            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'New dashboard'); ?>
                        <?php endif; ?>
                </a>
                <?php endif; ?>

                <?php if ((int)erLhcoreClassModelUserSetting::getSetting('new_dashboard',1) == 1) : ?>
                    <a id="chats-order-mode" data-mode="<?php if ((int)erLhcoreClassModelUserSetting::getSetting('static_order', 0) == 1) : ?>static<?php else : ?>dynamic<?php endif; ?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Click to switch to static/dynamic')?>" class="dropdown-item csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('front/switchdashboard')?>/(action)/static_order"><i class="material-icons">sort</i>
                        <?php if ((int)erLhcoreClassModelUserSetting::getSetting('static_order', 0) == 1) : ?>
                            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'In static chats order mode'); ?></a>
                        <?php else : ?>
                            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'In dynamic chats order mode'); ?></a>
                        <?php endif; ?>


                <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Switch between old and new editor')?>" class="dropdown-item csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('front/switchdashboard')?>/(action)/new_editor"><i class="material-icons">draw</i>
                    <?php if ((int)erLhcoreClassModelUserSetting::getSetting('new_editor', 0) == 1) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Switch to old editor'); ?></a>
                    <?php else : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Switch to new editor'); ?></a>
                    <?php endif; ?>


                <?php endif; ?>

                <?php if ((int)erLhcoreClassModelUserSetting::getSetting('new_dashboard',1) == 1) : ?>
                <a onclick="<?php if ((int)erLhcoreClassModelUserSetting::getSetting('column_chats', 0) == 1) : ?>ee.emitEvent('svelteRemoveLocalSetting',['lhc_rch'])<?php else : ?>ee.emitEvent('svelteStoreLocalSetting',['lhc_rch',1])<?php endif;?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Click to switch modes')?>" class="dropdown-item csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('front/switchdashboard')?>/(action)/column_chats">
                    <?php if ((int)erLhcoreClassModelUserSetting::getSetting('column_chats', 0) == 1) : ?>
                        <i class="material-icons">view_column</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Multiple chats view'); ?></a>
                    <?php else : ?>
                        <i class="material-icons">view_sidebar</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('front/default', 'Single chat view'); ?></a>
                    <?php endif; ?>
                <?php endif; ?>

                <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/options/new_dashboard_options.tpl.php'));?>

                <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/open_active_chat_tab_multiinclude.tpl.php'));?>

                <lhc-open-chat></lhc-open-chat>


            </div>
        </li>
    </ul>
</nav>