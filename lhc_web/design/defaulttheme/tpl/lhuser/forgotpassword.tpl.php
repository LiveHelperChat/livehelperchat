<div class="header-list">
<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/forgotpassword','Password remind');?></h1>
</div>
<div class="attribute-short">
<div id="messages">
	<?php if (isset($error)) : ?><h2 class="error-h2"><?php echo $error;?></h2><? endif;?>
</div>
<br />

<form method="post" action="<?php echo erLhcoreClassDesign::baseurl('user/forgotpassword')?>">
<div class="in-blk">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/forgotpassword','E-mail')?>:</label>
<input type="text" class="inputfield" name="Email" value="" />
</div>


<input type="submit" class="default-button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/forgotpassword','Restore password')?>" name="Forgotpassword" />


</form>
</div>