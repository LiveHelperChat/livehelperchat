
<div class="fill-survey-container">
    <div class="fill-survey-form">
        <inlinesurvey>
            <?php if (isset($errors)) : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
            <?php endif; ?>

            <?php include(erLhcoreClassDesign::designtpl('lhsurvey/forms/fill_inline.tpl.php'));?>
        </inlinesurvey>
    </div>
</div>