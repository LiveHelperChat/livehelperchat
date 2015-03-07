<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

<div class="form-group">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/new','Question');?></label>
<textarea class="form-control" rows="5" cols="50" name="question"><?php echo htmlspecialchars($faq->question)?></textarea>
</div>

<div class="form-group">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/new','Answer');?></label>
<textarea class="form-control" rows="5" cols="50" name="answer"><?php echo htmlspecialchars($faq->answer)?></textarea>
</div>

<div class="form-group">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/new','URL, enter * at the end for the wildcard');?>:</label>
<input type="text" class="form-control" name="URL" value="<?php echo htmlspecialchars($faq->url)?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/new','The URL where this question should appear, leave it empty for all');?>">
</div>

<div class="form-group">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/new','Submitter e-mail');?>:</label>
<input class="form-control" type="text" name="Email" value="<?php echo htmlspecialchars($faq->email)?>">
</div>

<div class="form-group">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/new','Identifier, can be used to filter questions by identifier');?>:</label>
<input class="form-control" type="text" name="Identifier" value="<?php echo htmlspecialchars($faq->identifier)?>">
</div>

<div class="form-group">
<label><input type="checkbox" name="ActiveFAQ" value="on" <?php $faq->active == 1 ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/new','Question is active');?></label>
</div>