	<div role="tabpanel">
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="<?php if(!isset($edittab)) {echo 'active';} ?>"><a href="#panel1" aria-controls="panel1" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','FAQ');?></a></li>
			<li role="presentation" class="<?php if(isset($edittab)) {echo 'active';} ?>"><a href="#panel2" aria-controls="panel2" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','Ask a question');?></a></li>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane <?php if(!isset($edittab)) {echo 'active';} ?>" id="panel1">
    			  <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            	  <?php foreach ($items as $item) : ?>
            	  <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-faq-<?php echo $item->id ?>">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse-faq-<?php echo $item->id ?>" aria-expanded="true" aria-controls="collapse-faq-<?php echo $item->id ?>">
                          <?php echo htmlspecialchars($item->question); ?>
                        </a>
                      </h4>
                    </div>
                    <div id="collapse-faq-<?php echo $item->id ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-faq-<?php echo $item->id ?>">
                      <div class="panel-body">
                        <p><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($item->answer));?></p>
                      </div>
                    </div>
                  </div>            	                  		 
            	  <?php endforeach; ?>
            	  </div>
            	              
            	  <?php if (isset($pages)) : ?>
            		 <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
            	  <?php endif;?>                        	  
			</div>
			<div role="tabpanel" class="tab-pane <?php if(isset($edittab)) {echo 'active';} ?>" id="panel2">
			
			<?php if (isset($errors)) : ?>
					<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
			<?php endif; ?>

			<?php if(isset($success)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','Your question was submitted!'); ?>
				<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
			<?php endif;?>

		  <form action="<?php echo erLhcoreClassDesign::baseurl('faq/faqwidget')?><?php isset($dynamic_url_append) ? print $dynamic_url_append : ''?>" method="post" onsubmit="return lhinst.addCaptcha('<?php echo time()?>',$(this))">
			  <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','Type your question');?></h4>
              <div class="form-group">
			     <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','E-mail')?>:<?php if (erLhcoreClassModelChatConfig::fetch('faq_email_required')->current_value == 1) : ?>*<?php endif;?></label>
			     <input type="text" class="form-control" name="email" value="<?= isset($item_new->email) ? htmlspecialchars($item_new->email) : null ?>" />
              </div>

			  <div class="form-group">
			     <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','Question')?>:*</label>
			     <textarea class="form-control" rows="3" name="question"><?= isset($item_new->question) ?  htmlspecialchars($item_new->question) : null ?></textarea>
              </div>

			  <input type="submit" class="btn btn-default btn-sm" name="sendAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','Send your question');?>"/>
              <br/>
              <br/>
			  <input type="hidden" value="<?php echo htmlspecialchars($referer);?>" name="url" />
			  <input type="hidden" value="1" name="send"/>

		  </form>
		  
		  
			</div>
		</div>
</div>
