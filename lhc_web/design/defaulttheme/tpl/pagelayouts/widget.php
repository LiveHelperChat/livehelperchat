<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head.tpl.php'));?>

<link rel="stylesheet" type="text/css" href="<?php echo erLhcoreClassDesign::designCSS('css/widget.css');?>" /> 

</head>
<body>

<div class="content-row" id="widget-layout">
    <div class="row">
        <div class="columns twelve pt10">
            <?php echo $Result['content']; ?>
        </div>
    </div>
</div>

   
</body>
</html>