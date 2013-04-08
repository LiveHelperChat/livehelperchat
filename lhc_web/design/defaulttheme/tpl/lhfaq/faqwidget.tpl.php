



<dl class="tabs">
  <dd <?php if(!isset($edittab)) {echo 'class="active"';} ?> ><a href="#simple1">FAQ</a></dd>
  <dd <?php if(isset($edittab)) {echo 'class="active"';} ?>><a href="#simple2">Ask a question</a></dd>

</dl>
<ul class="tabs-content">
  <li <?php if(!isset($edittab)) {echo 'class="active"';} ?> id="simple1Tab">
  
  
  
  <ul class="accordion" id="settings-geo">
  <?php foreach ($items as $item) : ?>
  <li class="">
    <div class="title">
      <h5><?php echo $item->question; ?> </h5>
    </div>
    <div class="content" style="display: none;">

     <p><?php echo $item->answer;?></p>
      
    

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
<?php if(isset($success))  :?>
<div class="alert-box success">
  Your question was sent.
  <a href="" class="close">&times;</a>
</div>
<?php   endif;?>
  <form action="<?php echo erLhcoreClassDesign::baseurl('chat/faqwidget')?>" method="post">
   <h2>Type your question</h2>
   
   Question: 
   <textarea rows="3" name="question"></textarea>

   <input type="hidden" rows="2" name="url" value="<?php echo $_GET['URLReferer']; ?>" />
   
  
   
   	<ul class="button-group radius">
      <li><input type="submit" class="small button" name="send" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/faqwidget','Send question');?>"/></li>

    </ul>
   
   </form>
  
  </li>

</ul>