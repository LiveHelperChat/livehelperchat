<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/form_question','Question');?>*</label>
<input type="text" name="Question" value="<?php echo htmlspecialchars($question->question);?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/form_question','Question intro');?></label>
<textarea name="QuestionIntro"><?php echo htmlspecialchars($question->question_intro);?></textarea>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/form_question','Show question for all URL containing this string E.g /shop/basket');?></label>
<input type="text" name="Location" value="<?php echo htmlspecialchars($question->location);?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/form_question','Priority, if multiple questions matches location, question with higher priority will be shown');?></label>
<input type="text" name="Priority" value="<?php echo htmlspecialchars($question->priority);?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('questionary/form_question','Active');?></label>
<input type="checkbox" name="Active" value="1" <?php ($question->active == 1) ? print 'checked="checked"' : '' ?>" />
<br>
<br>
