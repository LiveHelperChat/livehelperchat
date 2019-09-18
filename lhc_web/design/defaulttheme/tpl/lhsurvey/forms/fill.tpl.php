<?php if ($survey_item->is_filled == false) : ?>

<?php include(erLhcoreClassDesign::designtpl('lhsurvey/forms/fields_names.tpl.php'));?>

<div class="form-elements">
    <?php for ($i = 0; $i < 16; $i++) : ?>    
    	<?php foreach ($sortOptions as $keyOption => $sortOption) : ?>    	   		    
    		<?php if ($survey->{$keyOption . '_pos'} == $i && $survey->{$keyOption . '_enabled'} == 1) : ?>    		    		    		    		    				    		
    				<?php if ($sortOption['type'] == 'stars') : ?>
                    <label class="survey-stars-label"><?php echo htmlspecialchars($survey->{$sortOption['field'] . '_title'});?><?php if ($survey->{$sortOption['field'] . '_req'} == 1) : ?> *<?php endif;?></label>

                    <div class="survey-stars-row">
                        <?php for ($n = 1; $n <= $survey->{$sortOption['field']}; $n++) : ?>
                            <label class="survey-star-item <?php $n == 1 ? print 'selected-star' : '' ?>" title="<?php echo $n?>&nbsp;<?php if ($n == 1) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','star')?> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','Poor')?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','stars')?><?php if ($n == $survey->{$sortOption['field']}) : ?> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','Excellent')?><?php endif;endif;?>">
                                <svg width="28" height="28" style="height:28px;width:28px;" fill="currentColor" color="#000000" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1728 647q0 22-26 48l-363 354 86 500q1 7 1 20 0 21-10.5 35.5t-30.5 14.5q-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z"></path></svg>
                                <input id="<?php echo $sortOption['field']?>Evaluate_<?php echo $n?>" class="form-check-input hide" type="radio" name="<?php echo $sortOption['field']?>Evaluate" <?php if ($survey_item->{$sortOption['field']} == $n) : ?>checked="checked"<?php endif;?> value="<?php echo $n?>">
                            </label>
                        <?php endfor; ?>
                    </div>

    				<?php elseif ($sortOption['type'] == 'question') : ?>
    				<div class="form-group">
    					<label class="survey-question-label"><?php echo htmlspecialchars($survey->{$sortOption['field']});?><?php if ($survey->{$sortOption['field'] . '_req'} == 1) : ?> *<?php endif;?></label>
    					<textarea class="form-control" name="<?php echo $sortOption['field'] . 'Question'?>"><?php echo htmlspecialchars($survey_item->{$sortOption['field']})?></textarea>
    				</div>
    				<?php elseif ($sortOption['type'] == 'question_options') : ?>
                    <label class="survey-question-option-label"><?php echo htmlspecialchars($survey->{$sortOption['field']});?><?php if ($survey->{$sortOption['field'] . '_req'} == 1) : ?> *<?php endif;?></label>
    				<div class="form-group">
    					<?php foreach ($survey->{$sortOption['field'] . '_items_front'} as $key => $item) : ?>
                            <?php if (mb_strpos($item['option'],"\n") !== false && mb_strpos($item['option'],"\n") === 1 || mb_strpos($item['option'],"\n") == mb_strlen($item['option'])-1) : ?>
                                <div class="form-check">
                                    <input class="form-check-input" id="<?php echo $sortOption['field']?>EvaluateOption_<?php echo $key+1?>" type="radio" name="<?php echo $sortOption['field']?>EvaluateOption" value="<?php echo $key+1?>" <?php if ((int)$survey_item->{$sortOption['field']} === $key+1) : ?>checked="checked"<?php endif;?>/>
                                    <label class="form-check-label" for="<?php echo $sortOption['field']?>EvaluateOption_<?php echo $key+1?>"><?php echo erLhcoreClassSurveyValidator::parseAnswer($item['option']) ?></label>
                                </div>
                            <?php else : ?>
                                <label>
                                    <?php echo erLhcoreClassSurveyValidator::parseAnswer($item['option']) ?>
                                    <div align="center">
                                        <input type="radio" name="<?php echo $sortOption['field']?>EvaluateOption" value="<?php echo $key+1?>" <?php if ((int)$survey_item->{$sortOption['field']} === $key+1) : ?>checked="checked"<?php endif;?>/>
                                    </div>
                                </label>
                            <?php endif?>
    					<?php endforeach;?>
    				</div>
    				<?php endif;?>
    		<?php endif; ?>
    	<?php endforeach;?>
    <?php endfor;?>
</div>

<script>
    $.fn.makeStarsInteractive = function() {
        var _this = this;
        _this.each(function () {
            var _thisCurrent = this;
            $(this).on('click','>label', function(param) {
                $(this).find('input').prop('checked',true);
                _this.find('label').removeClass('hover');
                $(_thisCurrent).find('label:lt('+$(_thisCurrent).find('label > input:checked').val()+')').addClass('hover');
            });
            $(this).find('label').hover(function(param) {
                _this.find('label').removeClass('hover');
                if (param.type == 'mouseenter') {
                    $(_thisCurrent).find('label').removeClass('survey-selected-item');
                    $(this).addClass('hover');
                    $(_thisCurrent).find('label:lt('+$(this).find('input').val()+')').addClass('hover');
                } else {
                    $(_thisCurrent).find('label').removeClass('hover');
                    $(_thisCurrent).find('label:lt('+$(_thisCurrent).find('label > input:checked').val()+')').addClass('survey-selected-item');
                }
            });
        });
    };

    $('.survey-stars-row').makeStarsInteractive();
</script>

<?php else : ?>
<div class="alert alert-success" role="alert">
    <?php if ($survey->feedback_text != '') : ?><?php echo htmlspecialchars($survey->feedback_text)?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','Thank you for your feedback...')?><?php endif; ?>    
</div>
<?php endif; ?>