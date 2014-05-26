<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/new','Name');?></label>
<input type="text" name="name" value="<?php echo htmlspecialchars($docshare->name)?>">

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/new','Description');?></label>
<textarea rows="5" cols="50" name="desc"><?php echo htmlspecialchars($docshare->desc)?></textarea>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/new','Document');?>, (ppt,pptx,doc,odp,mobi,docx,xlsx,txt,xls,xlsx,pdf,rtf,odt)</label>
<input type="file" name="qqfile" value="" />

<?php if ($docshare->has_file) : ?>

<div class="row">
<div class="columns small-6">

<strong><em><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/new','File converted');?> - <?php if ($docshare->converted == 1) : ?>Yes<?php else : ?>No<?php endif;?></em></strong>

<ul>
	<li><a href="<?php echo erLhcoreClassDesign::baseurl('docshare/download')?>/<?php echo $docshare->id?>"><?php echo htmlspecialchars($docshare->file_name_upload)?></a>
	<?php if ($docshare->converted == 1) : ?>
	<li><a href="<?php echo erLhcoreClassDesign::baseurl('docshare/downloadpdf')?>/<?php echo $docshare->id?>"><?php echo htmlspecialchars($docshare->file_name_upload_pdf)?></a>
	<?php endif;?>
</ul>

</div>

<div class="columns small-6">

<?php if ($docshare->pdf_to_img_converted) : ?>
<a href="<?php echo erLhcoreClassDesign::baseurl('docshare/previewimages')?>/<?php echo $docshare->id?>" data-reveal-id="myModal" data-reveal-ajax="true" class="button small radius">Preview converted images</a>

<a href="<?php echo erLhcoreClassDesign::baseurl('docshare/view')?>/<?php echo $docshare->id?>" target="_blank" class="button small radius">Open in a viewer</a>

<?php endif;?>


</div>


</div>


<?php endif;?>