<div class="section-container tabs float-break" data-section="tabs">
  <section <?php if(!isset($edittab)) {echo 'class="active"';} ?>>
    <p class="title" data-section-title><a href="#panel1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','FAQ');?></a></p>
    <div class="content" data-section-content>

      <ul class="accordion-lhc">
	  <?php foreach ($items as $item) : ?>
		  <li>
		  	 <div class="title-lhc">
		     	<h5><?php echo htmlspecialchars($item->question); ?> </h5>
		     </div>
		     <div class="content-lhc">
		     	<p><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($item->answer));?></p>
		    </div>
		  </li>
	  <?php endforeach; ?>
	  </ul>

	  <?php if (isset($pages)) : ?>
		 <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
	  <?php endif;?>
    </div>
  </section>
  <section <?php if(isset($edittab)) {echo 'class="active"';} ?>>
    <p class="title" data-section-title><a href="#panel2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','Ask a question');?></a></p>
    <div class="content" data-section-content>

    	  <div>
	      <?php if (isset($errors)) : ?>
					<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
			<?php endif; ?>

			<?php if(isset($success)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','Your question was submitted!'); ?>
				<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
			<?php endif;?>

		  <form action="<?php echo erLhcoreClassDesign::baseurl('faq/faqwidget')?><?php echo $dynamic_url_append?>" method="post" onsubmit="return lhinst.addCaptcha('<?php echo time()?>',$(this))">
			  <h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','Type your question');?></h2>

			  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','Question')?>:</label>
			  <textarea rows="3" name="question"><?php echo htmlspecialchars($item_new->question);?></textarea>

			  <input type="submit" class="small round button" name="sendAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','Send question');?>"/>
			  <input type="hidden" value="<?php echo htmlspecialchars($referer);?>" name="url" />
			  <input type="hidden" value="1" name="send"/>

		  </form>
		  </div>

    </div>
  </section>
</div>

<script>$(document).foundationAccordion();</script>
