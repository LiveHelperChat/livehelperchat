<?php if (isset($errors) && !empty($errors)) : ?>
        <div data-alert class="alert alert-danger alert-dismissible fade show" ng-non-bindable>
            <?php if (!isset($hideErrorButton)) : ?>
                <button type="button" class="btn-close" aria-label="Close"></button>
            <?php endif;?>
            <ul class="ps-1 mx-2 mb-0">
                <?php foreach ($errors as $err) : ?>
                    <li><?php echo $err?></li>
                <?php endforeach;?>
            </ul>
        </div>
<?php endif; ?>

<?php if (isset($stored)) : ?>
<div>
    <?php if ($survey->feedback_text != '') : ?>
        <?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars(erLhcoreClassGenericBotWorkflow::translateMessage($survey->feedback_text, array('chat' => $chat, 'args' => ['chat' => $chat])))); ?>
    <?php else : ?>
         <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','Thank you for your feedback!')?>
    <?php endif; ?>
</div>
<?php endif; ?>
