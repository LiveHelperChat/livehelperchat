<div>
    <span class="font-weight-bold op-name-widget"><?php echo htmlspecialchars($user->name_support)?></span>
    <?php if (isset($extraMessage)) : ?>
        <span class="font-italic op-extra-message"><?php echo $extraMessage;?></span>
    <?php elseif ($user->job_title != '') : ?>
        <?php if (isset($theme) && $theme instanceof erLhAbstractModelWidgetTheme && isset($theme->bot_configuration_array['job_new_row']) && $theme->bot_configuration_array['job_new_row'] == true) : ?>
            <span class="font-italic d-block op-job-title"><?php echo htmlspecialchars($user->job_title);?></span>
        <?php else : ?>
            ,&nbsp;<span class="font-italic op-job-title"><?php echo htmlspecialchars($user->job_title);?></span>
        <?php endif; ?>
    <?php endif;?>
</div>