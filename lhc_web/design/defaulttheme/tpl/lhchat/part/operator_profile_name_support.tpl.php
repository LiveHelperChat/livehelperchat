<div>
    <span class="fw-bold op-name-widget">
        <?php if (isset($theme) && $theme instanceof erLhAbstractModelWidgetTheme && isset($theme->bot_configuration_array['custom_op_name']) && $theme->bot_configuration_array['custom_op_name'] != '') : ?>
            <?php echo str_replace(['{nick}', '{name}', '{surname}'], [htmlspecialchars((string)$user->name_support), htmlspecialchars((string)$user->name), htmlspecialchars((string)$user->surname)], $theme->bot_configuration_array['custom_op_name']);?>
        <?php else : ?>
            <?php echo htmlspecialchars($user->name_support)?>
        <?php endif; ?>
    </span>
    <?php if (isset($extraMessage)) : ?>

    <?php if ($extraMessage != '') : ?>
            <span class="fst-italic op-job-title"><?php echo $extraMessage;?></span>
    <?php endif; ?>

    <?php elseif ($user->job_title != '' && !(isset($theme) && $theme instanceof erLhAbstractModelWidgetTheme && isset($theme->bot_configuration_array['hide_job_title']) && $theme->bot_configuration_array['hide_job_title'] == true)) : ?>
        <span class="op-job-title-sep">,&nbsp;</span><span class="fst-italic op-job-title"><?php echo htmlspecialchars($user->job_title);?></span>
    <?php endif;?>
</div>