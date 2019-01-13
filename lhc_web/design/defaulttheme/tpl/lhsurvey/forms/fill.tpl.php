<?php if ($survey_item->is_filled == false) : ?>

<?php include(erLhcoreClassDesign::designtpl('lhsurvey/forms/fields_names.tpl.php'));?>

<div class="form-elements">
    <?php for ($i = 0; $i < 16; $i++) : ?>    
    	<?php foreach ($sortOptions as $keyOption => $sortOption) : ?>    	   		    
    		<?php if ($survey->{$keyOption . '_pos'} == $i && $survey->{$keyOption . '_enabled'} == 1) : ?>    		    		    		    		    				    		
    				<?php if ($sortOption['type'] == 'stars') : ?>
                    <label><?php echo htmlspecialchars($survey->{$sortOption['field'] . '_title'});?><?php if ($survey->{$sortOption['field'] . '_req'} == 1) : ?> *<?php endif;?></label>
                    <?php for ($n = 1; $n <= $survey->{$sortOption['field']}; $n++) : ?>
                    <div class="form-check">
                        <input id="<?php echo $sortOption['field']?>Evaluate_<?php echo $n?>" class="form-check-input" type="radio" name="<?php echo $sortOption['field']?>Evaluate" <?php if ($survey_item->{$sortOption['field']} == $n) : ?>checked="checked"<?php endif;?> value="<?php echo $n?>">
                        <label for="<?php echo $sortOption['field']?>Evaluate_<?php echo $n?>" class="form-check-label"><?php echo $n?>&nbsp;<?php if ($n == 1) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','star')?> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','Poor')?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','stars')?><?php if ($n == $survey->{$sortOption['field']}) : ?> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','Excellent')?><?php endif;endif;?></label>
                    </div>
                    <?php endfor;?>
    				<?php elseif ($sortOption['type'] == 'question') : ?>
    				<div class="form-group">
    					<label><?php echo htmlspecialchars($survey->{$sortOption['field']});?><?php if ($survey->{$sortOption['field'] . '_req'} == 1) : ?> *<?php endif;?></label>
    					<textarea class="form-control" name="<?php echo $sortOption['field'] . 'Question'?>"><?php echo htmlspecialchars($survey_item->{$sortOption['field']})?></textarea>
    				</div>
    				<?php elseif ($sortOption['type'] == 'question_options') : ?>
                    <label><?php echo htmlspecialchars($survey->{$sortOption['field']});?><?php if ($survey->{$sortOption['field'] . '_req'} == 1) : ?> *<?php endif;?></label>
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

<?php else : ?>
<div class="alert alert-success" role="alert">
    <?php if ($survey->feedback_text != '') : ?><?php echo htmlspecialchars($survey->feedback_text)?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/fill','Thank you for your feedback...')?><?php endif; ?>    
</div>
<?php endif; ?>