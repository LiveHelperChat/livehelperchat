<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/new','Question');?></label>
<textarea rows="5" cols="50" name="question"><?php echo htmlspecialchars($faq->question)?></textarea>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/new','Answer');?></label>
<textarea rows="5" cols="50" name="answer"><?php echo htmlspecialchars($faq->answer)?></textarea>

<label>URL:</label>
<input type="text" name="URL" value="<?php echo htmlspecialchars($faq->url)?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/new','The URL where this question should appear, leave it empty for all');?>">

<label><input type="checkbox" name="ActiveFAQ" value="on" <?php $faq->active == 1 ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/new','Question is active');?></label>
