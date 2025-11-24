	<div role="tabpanel">
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist" data-remember="true">
			<li role="presentation" class="nav-item"><a class="nav-link fs13 <?php if(!isset($edittab)) {echo 'active';} ?>" href="#panel1" aria-controls="panel1" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','FAQ');?></a></li>
			<li role="presentation" class="nav-item"><a class="nav-link fs13 <?php if(isset($edittab)) {echo 'active';} ?>" href="#panel2" aria-controls="panel2" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','Ask a question');?></a></li>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content ">
			<div role="tabpanel" class="tab-pane <?php if(!isset($edittab)) {echo 'active';} ?>" id="panel1">

                <form method="get" class="mt-2" action="">
                    <div class="row">
                        <div class="col-8">
                            <input type="text" value="<?php echo htmlspecialchars($keyword)?>" name="search" class="form-control form-control-sm mb-2 me-sm-2" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','Keyword');?>">

                        </div>
                        <div class="col-4">
                            <button type="submit" name="doSearch" class="btn d-block w-100 btn-sm btn-primary mb-2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','Search');?></button>
                        </div>
                    </div>
                </form>

                <?php if (empty($items)) : ?>
                <p class="text-danger"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','No items were found');?></p>
                <?php endif; ?>

                <div class="mb-3">
    			  <div class="accordion mt-2" id="accordion" role="tablist" aria-multiselectable="true">
            	  <?php foreach ($items as $item) : ?>
            	  <div class="card">
                    <div class="card-header px-2 py-1" role="tab" id="heading-faq-<?php echo $item->id ?>">
                        <a data-bs-toggle="collapse" class="d-block" data-parent="#accordion" href="#collapse-faq-<?php echo $item->id ?>" aria-expanded="true" aria-controls="collapse-faq-<?php echo $item->id ?>">
                          <?php echo htmlspecialchars($item->question); ?>
                        </a>
                    </div>
                    <div id="collapse-faq-<?php echo $item->id ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-faq-<?php echo $item->id ?>">
                      <div class="card-body p-2">
                        <p class="m-0"><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($item->answer));?></p>
                      </div>
                    </div>
                  </div>            	                  		 
            	  <?php endforeach; ?>
            	  </div>

            	  <?php if (isset($pages)) : ?>
            		 <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
            	  <?php endif;?>
                </div>
			</div>
			<div role="tabpanel" class="tab-pane <?php if(isset($edittab)) {echo 'active';} ?>" id="panel2">
			
			<?php if (isset($errors)) : ?>
					<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
			<?php endif; ?>

			<?php if(isset($success)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','Your question was submitted!'); ?>
				<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
			<?php endif;?>

		  <form action="<?php echo erLhcoreClassDesign::baseurl('faq/faqwidget')?><?php isset($dynamic_url_append) ? print $dynamic_url_append : ''?>" method="post" onsubmit="return lhinst.addCaptcha('<?php echo time()?>',$(this))">

              <h6 class="py-2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','Type your question');?></h6>

              <div class="form-group">
			     <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','E-mail')?>:<?php if (erLhcoreClassModelChatConfig::fetch('faq_email_required')->current_value == 1) : ?>*<?php endif;?></label>
			     <input type="text" class="form-control form-control-sm" name="email" value="<?= isset($item_new->email) ? htmlspecialchars($item_new->email) : null ?>" />
              </div>

			  <div class="form-group">
			     <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','Question')?>:*</label>
			     <textarea class="form-control form-control-sm" rows="3" name="question"><?= isset($item_new->question) ?  htmlspecialchars($item_new->question) : null ?></textarea>
              </div>

			  <input type="submit" class="btn btn-secondary btn-sm" name="sendAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','Send your question');?>"/>
              <br/>
              <br/>
			  <input type="hidden" value="<?php echo htmlspecialchars($referer);?>" name="url" />
			  <input type="hidden" value="1" name="send"/>

		  </form>
		  
		  
			</div>
		</div>
</div>
