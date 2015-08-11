<?php if ($survey_item->is_filled == false) : ?>
    <?php if ($survey->max_stars > 0) : ?>        
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','How well did we do?')?></label>
        <?php for ($i = 0; $i < $survey->max_stars; $i++) : ?>
        <div class="radio radio-widget">
          <label>
            <input type="radio" name="StarsValue" value="<?php echo $i+1?>"><?php echo $i+1?>&nbsp;<?php if ($i == 0) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','star')?> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','Poor')?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','stars')?><?php if ($i == $survey->max_stars - 1) : ?> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','Excellent')?><?php endif;endif;?>
          </label>
        </div>
        <?php endfor;?>
    <?php endif;?>
<?php else : ?>
    <div class="alert alert-success" role="alert"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','Thank you for your feedback...')?></div>
<?php endif; ?>