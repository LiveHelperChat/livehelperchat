<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/form_question','Question');?>*</label>
<input type="text" name="Question" value="<?php echo htmlspecialchars($question->question);?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/form_question','Question intro');?></label>
<textarea name="QuestionIntro"><?php echo htmlspecialchars($question->question_intro);?></textarea>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/form_question','Show questions for all the URLs containing this string E.g /shop/basket');?></label>
<input type="text" name="Location" value="<?php echo htmlspecialchars($question->location);?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/form_question','Priority, if multiple questions match a location, the question with the higher priority will be shown');?></label>
<input type="text" name="Priority" value="<?php echo htmlspecialchars($question->priority);?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/form_question','Active');?></label>
<input type="checkbox" name="Active" value="1" <?php ($question->active == 1) ? print 'checked="checked"' : '' ?>" />
<br>
<br>
