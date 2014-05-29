<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/edit','Edit document');?></h1>

<div id="myModal" class="reveal-modal"></div>

<div class="row">
	<div class="columns large-6">	
	<ul class="button-group radius">	
		<li><a href="<?php echo erLhcoreClassDesign::baseurl('docshare/embedcode')?>/<?php echo $docshare->id?>" data-reveal-id="myModal" data-reveal-ajax="true" class="button tiny"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/edit','Embed');?></a></li>
	<?php if ($docshare->pdf_to_img_converted) : ?>
		<li><a href="<?php echo erLhcoreClassDesign::baseurl('docshare/previewimages')?>/<?php echo $docshare->id?>" data-reveal-id="myModal" data-reveal-ajax="true" class="button tiny"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/new','Preview images');?></a></li>
		<li><a href="<?php echo erLhcoreClassDesign::baseurl('docshare/view')?>/<?php echo $docshare->id?>" target="_blank" class="button tiny"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/new','Open in a viewer');?></a></li>
	<?php endif;?>
	</ul>	
	</div>	
	<div class="columns large-6">
		<div class="row collapse">        
	        <div class="small-2 columns">
	          <span class="prefix"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/edit','URL');?></span>
	        </div>
	        <div class="small-10 columns">
	          <input type="text" value="<?php echo erLhcoreClassXMP::getBaseHost(). $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurldirect('docshare/view')?>/<?php echo $docshare->id?>">
	        </div>
	    </div>
	</div>
</div>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/messages','Updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('docshare/edit')?>/<?php echo $docshare->id?>" method="post" enctype="multipart/form-data">

	<?php include(erLhcoreClassDesign::designtpl('lhdocshare/form.tpl.php'));?>

	<br>
	<ul class="button-group radius">
      <li><input type="submit" class="small button" name="Update" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/></li>
      <li><input type="submit" class="small button" name="Cancel" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/></li>
    </ul>

</form>