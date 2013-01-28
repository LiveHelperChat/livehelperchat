<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head.tpl.php'));?>
<link rel="stylesheet" type="text/css" href="<?php echo erLhcoreClassDesign::designCSS('css/chat.css');?>" /> 
</head>
<body>

<div class="content-row">

<div class="row">
    <div class="columns twelve">
        <h1><a href="http://livehelperchat.com" target="_blank" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Live helper chat homepage')?>"><img src="<?php echo erLhcoreClassDesign::design('images/general/logo.png');?>" alt="<?php echo erConfigClassLhConfig::getInstance()->getSetting( 'site', 'title' )?>" title="<?php echo erConfigClassLhConfig::getInstance()->getSetting( 'site', 'title' )?>"></a></h1>
    </div>
</div>

<div class="row">
    <div class="columns twelve">
    <?php echo $Result['content']; ?>
    </div>
</div>

<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_footer_user.tpl.php'));?>
</div>    
	
</body>
</html>