<dl class="tabs">
  <dd <?php if(!isset($edittab)) {echo 'class="active"';} ?> ><a href="#simple1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','FAQ');?></a></dd>
  <dd <?php if(isset($edittab)) {echo 'class="active"';} ?>><a href="#simple2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','Ask a question');?></a></dd>
</dl>

<ul class="tabs-content">
	<li <?php if(!isset($edittab)) {echo 'class="active"';} ?> id="simple1Tab">
	  <ul class="accordion" >
	  <?php foreach ($items as $item) : ?>
		  <li>
		    <div class="title">
		      <h5><?php echo htmlspecialchars($item->question); ?> </h5>
		    </div>
		    <div class="content" style="display: none;">
		     	<p><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($item->answer));?></p>
		    </div>
		  </li>
	  <?php endforeach; ?>
	  </ul>
	  <?php if (isset($pages)) : ?>
		 <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
	  <?php endif;?>
	</li>
	<li <?php if(isset($edittab)) {echo 'class="active"';} ?> id="simple2Tab">

		<?php if (isset($errors)) : ?>
				<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
		<?php endif; ?>

		<?php if(isset($success)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','Your question was sent!'); ?>
			<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
		<?php endif;?>

	  <form action="<?php echo erLhcoreClassDesign::baseurl('faq/faqwidget')?><?php echo $dynamic_url_append?>" method="post">
		  <h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','Type your question');?></h2>

		  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','Question');?>:</label>
		  <textarea rows="3" name="question"><?php echo htmlspecialchars($item_new->question);?></textarea>

		  <input type="submit" class="small button radius" name="send" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','Send question');?>"/>
		  <input type="hidden" value="<?php echo htmlspecialchars($referer);?>" name="url" />
	  </form>
	</li>
</ul>