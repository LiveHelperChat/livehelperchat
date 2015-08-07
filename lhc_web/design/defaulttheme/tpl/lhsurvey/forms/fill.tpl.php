<?php if ($survey_item->is_filled == false) : ?>
    <?php if ($survey->max_stars > 0) : ?>        
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','Please choose stars evaluation')?></label>
        <?php for ($i = 0; $i < $survey->max_stars; $i++) : ?>
            <div><label><input type="radio" name="StarsValue" value="<?php echo $i?>"> <?php echo $i+1?> <?php if ($i == 0) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','star')?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','stars')?><?php endif;?></label></div>
        <?php endfor;?>
    <?php endif;?>
<?php else : ?>
    <div class="alert alert-success" role="alert"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','Thank you for your feedback...')?></div>
<?php endif; ?>