<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/new','Name');?>*</label>
<input type="text" name="name" value="<?php echo htmlspecialchars($docshare->name)?>">

<label><input type="checkbox" name="Active" value="1" <?php if ($docshare->active == 1) : ?>checked="checked"<?php endif;?>  /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/new','Active');?></label>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/new','Description');?></label>
<textarea rows="5" cols="50" name="desc"><?php echo htmlspecialchars($docshare->desc)?></textarea>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/new','Document');?>, (<?php echo htmlspecialchars($share_data['supported_extension'])?>)</label>
<input type="file" name="qqfile" value="" />

<?php if ($docshare->has_file) : ?>
<div class="panel secondary">
<div class="row">
	<div class="columns small-6">
		<h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/new','Files');?></h6>
		<ul>
			<li><a href="<?php echo erLhcoreClassDesign::baseurl('docshare/download')?>/<?php echo $docshare->id?>"><?php echo htmlspecialchars($docshare->file_name_upload)?></a>
			<?php if ($docshare->converted == 1) : ?>
			<li><a href="<?php echo erLhcoreClassDesign::baseurl('docshare/downloadpdf')?>/<?php echo $docshare->id?>"><?php echo htmlspecialchars($docshare->file_name_upload_pdf)?></a>
			<?php endif;?>
		</ul>
	</div>
	<div class="columns small-6">
		<div class="alert-box secondary">
		<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/new','File converted');?> - <?php if ($docshare->converted == 1) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/new','Yes');?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/new','No');?><?php endif;?>	
		</div>	
	</div>
</div>
</div>
<?php endif;?>