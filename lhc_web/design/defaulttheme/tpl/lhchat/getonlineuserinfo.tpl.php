<?php if (!isset($popup)) : ?>
<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat', 'Online profile')?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>
<?php endif; ?>

<a href="<?php echo htmlspecialchars(trim($online_user->current_page))?>" class="no-wrap fs12"><?php echo htmlspecialchars(trim($online_user->referrer))?></a>

<div class="online-user-info">
    <div role="tabpanel">
    	<!-- Nav tabs -->
    	<ul class="nav nav-tabs mb-2" role="tablist">
    		<li role="presentation" class="nav-item" ><a class="<?php if (!isset($tab) || empty($tab)) :?>active<?php endif;?> nav-link" href="#panel1" aria-controls="panel1" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Visitor')?></a></li>

            <?php if ($online_user->id > 0) : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/footprint_tab_tab_pre.tpl.php')); ?>

                <?php if ($chat_chat_tabs_footprint_tab_tab_enabled == true && erLhcoreClassModelChatConfig::fetch('track_footprint')->current_value == 1) : ?>
                <li role="presentation" class="nav-item"><a class="nav-link" href="#panel2" aria-controls="panel2" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Footprint')?></a></li>
                <?php endif;?>
    		<?php endif;?>

    		<?php include(erLhcoreClassDesign::designtpl('lhchat/online_user/user_chats_tab.tpl.php')); ?>

            <?php if ($online_user->id > 0) : ?>

                <?php if (isset($chat_id_present) && is_numeric($chat_id_present)) : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/operator_screenshot_pre.tpl.php')); ?>
                <?php if ($operator_screenshot_enabled == true) : ?>
                  <?php include(erLhcoreClassDesign::designtpl('lhchat/online_user/screenshot_tab.tpl.php')); ?>
                <?php endif;?>
                <?php endif;?>

                <?php include(erLhcoreClassDesign::designtpl('lhchat/online_user/notes_tab.tpl.php')); ?>
            <?php endif; ?>

            <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','chatdebug')) : ?>
                <li role="presentation" class="nav-item"><a class="nav-link" href="#chatdebug" aria-controls="chatdebug" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Debug');?></a></li>
            <?php endif; ?>

    	</ul>
    
    	<!-- Tab panes -->
    	<div class="tab-content">
    		<div role="tabpanel" class="tab-pane <?php if (!isset($tab) || empty($tab)) :?>active<?php endif;?>" id="panel1">
               <?php if ($online_user->id > 0) : ?>
                  <?php include(erLhcoreClassDesign::designtpl('lhchat/online_user/online_user_info.tpl.php')); ?>

                  <?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/proactive_pre.tpl.php'));?>

                  <?php if ($system_configuration_proactive_enabled == true) : ?>
                  <input type="button" class="btn btn-secondary btn-sm" onclick="lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('chat/sendnotice')?>/<?php echo $online_user->id?>'});" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Send message');?>"/>
                  <?php endif;?>
                <?php else : ?>
                  <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat', 'No information')?></p>
                <?php endif; ?>
    		</div>
    		
    		<?php if ($online_user->id > 0 && $chat_chat_tabs_footprint_tab_tab_enabled == true && erLhcoreClassModelChatConfig::fetch('track_footprint')->current_value == 1) : ?>
    		<div role="tabpanel" class="tab-pane" id="panel2">
                    <ul class="foot-print-content list-unstyled mb-0" style="max-height: 170px;">
                    <?php foreach (erLhcoreClassModelChatOnlineUserFootprint::getList(array('filter' => array('online_user_id' => $online_user->id))) as $footprintItems) : ?>
                    <li>
                    <a target="_blank" rel="noopener" href="<?php echo htmlspecialchars($footprintItems->page);?>"><?php echo $footprintItems->time_ago?> | <?php echo htmlspecialchars($footprintItems->page);?></a>
                    </li>
                    <?php endforeach;?>
                    </ul>
    		</div>
    		<?php endif;?>

            <?php include(erLhcoreClassDesign::designtpl('lhchat/online_user/user_chats.tpl.php')); ?>

            <?php if ($online_user->id > 0) : ?>
                <?php if (isset($chat_id_present) && is_numeric($chat_id_present) && $operator_screenshot_enabled == true) : ?>
                  <?php include(erLhcoreClassDesign::designtpl('lhchat/online_user/screenshot.tpl.php')); ?>
                <?php endif;?>

                <?php include(erLhcoreClassDesign::designtpl('lhchat/online_user/notes.tpl.php')); ?>
            <?php endif;?>

            <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','chatdebug')) : ?>
                <div role="tabpanel" class="tab-pane" id="chatdebug">
                    <pre class="fs11"><?php echo htmlspecialchars(json_encode($online_user->getState(),JSON_PRETTY_PRINT)); ?></pre>
                </div>
            <?php endif; ?>

    	</div>
    </div>
</div>

<?php if (!isset($popup)) : ?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>
<?php endif; ?>
