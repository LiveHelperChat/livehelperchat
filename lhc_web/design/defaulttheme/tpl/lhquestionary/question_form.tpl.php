
<div class="form-group">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/form_question','Question');?>*</label>
<input class="form-control" type="text" name="Question" value="<?php echo htmlspecialchars($question->question);?>" />
</div>

<div class="form-group">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/form_question','Question intro');?></label>
<textarea class="form-control" name="QuestionIntro"><?php echo htmlspecialchars($question->question_intro);?></textarea>
</div>

<div class="form-group">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/form_question','Show questions for all the URLs containing this string E.g /shop/basket');?></label>
<input type="text" class="form-control" name="Location" value="<?php echo htmlspecialchars($question->location);?>" />
</div>

<div class="form-group">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/form_question','Priority, if multiple questions match a location, the question with the higher priority will be shown');?></label>
<input type="text" class="form-control" name="Priority" value="<?php echo htmlspecialchars($question->priority);?>" />
</div>

<div class="form-group">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/form_question','Revote time (hours), before can revote. Default 0 - never. Higher value allow revote after seconds expire since last vote');?></label>
<input type="text" class="form-control" name="Revote" value="<?php echo $question->revote;?>" />
</div>

<div class="form-group">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/form_question','Active');?></label>
<input type="checkbox" name="Active" value="1" <?php ($question->active == 1) ? print 'checked="checked"' : '' ?>" />
</div>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>