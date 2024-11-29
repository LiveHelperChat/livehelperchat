<?php if ($user instanceof erLhcoreClassModelGenericBotBot) : erLhcoreClassGenericBotWorkflow::setDefaultPhotoNick($chat,$user); $extraMessage = ($theme !== false ? htmlspecialchars($theme->bot_status_text) : ''); ?>
    <?php \LiveHelperChat\Models\Departments\UserDepAlias::getAlias(array('user' => & $user, 'chat' => $chat)); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile_main_pre.tpl.php')); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/operator_profile.tpl.php'));?>
<?php else : ?>
    <h6 class="fs12 status-text"><?php if ($theme !== false  && $theme->bot_status_text != '') : ?>
            <?php echo htmlspecialchars($theme->bot_status_text)?>
        <?php else : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/checkchatstatus_text/bot_chat.tpl.php'));?>
        <?php endif; ?></h6>
<?php endif; ?>