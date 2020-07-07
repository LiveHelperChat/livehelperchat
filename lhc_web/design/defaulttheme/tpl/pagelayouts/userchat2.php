<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
    <style type="text/css">html,body,div,span,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,abbr,address,cite,code,del,dfn,em,img,ins,kbd,q,samp,small,strong,sub,sup,var,b,i,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,figcaption,figure,footer,header,hgroup,menu,nav,section,summary,time,mark,audio,video{margin:0;padding:0;border:0;outline:0;font-size:100%;vertical-align:baseline;background:transparent}body{line-height:1}article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section{display:block}nav ul{list-style:none}blockquote,q{quotes:none}blockquote:before,blockquote:after,q:before,q:after{content:'';content:none}a{margin:0;padding:0;font-size:100%;vertical-align:baseline;background:transparent}ins{background-color:#ff9;color:#000;text-decoration:none}mark{background-color:#ff9;color:#000;font-style:italic;font-weight:bold}del{text-decoration:line-through}abbr[title],dfn[title]{border-bottom:1px dotted;cursor:help}table{border-collapse:collapse;border-spacing:0}hr{display:block;height:1px;border:0;border-top:1px solid #ccc;margin:1em 0;padding:0}input,select{vertical-align:middle}html,body{height:100%;min-height: 100%}body{display: flex;flex-direction: column;background:transparent;font:13px Helvetica,Arial,sans-serif;position:relative}.clear{clear:both}.clearfix:after{content:'';display:block;height:0;clear:both;visibility:hidden}</style>

    <link rel="stylesheet" type="text/css" href="<?php echo erLhcoreClassDesign::designCSS('css/widgetv2/bootstrap.min.css;css/widgetv2/widget.css;css/widgetv2/widget_popup_override.css')?>" />

    <?php if (isset($Result['mobile']) && $Result['mobile'] == true) : ?>
    <link rel="stylesheet" type="text/css" href="<?php echo erLhcoreClassDesign::designCSS('css/widgetv2/widget_mobile.css;css/widgetv2/widget_mobile_popup_override.css')?>" />
    <?php endif; ?>

    <?php if (isset($Result['theme']) && $Result['theme'] > 0) : ?>
        <link rel="stylesheet" type="text/css" href="<?php echo erLhcoreClassDesign::baseurl('widgetrestapi/theme')?>/<?php echo $Result['theme']?>/(p)/1?v=<?php echo $Result['theme_v']?>" />
    <?php endif; ?>

    <?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_header_widget2_extension_multiinclude.tpl.php'));?>

</head>
<body>
<div id="root" class="container-fluid d-flex flex-column flex-grow-1 overflow-auto"></div>
<?php echo $Result['content']?>
<?php
$detect = new Mobile_Detect();
if ($detect->version('IE') !== false) : ?>
    <script src="<?php echo erLhcoreClassDesign::design('js/widgetv2/react.app.ie.js')?>?t=84"></script>
<?php else : ?>
    <script src="<?php echo erLhcoreClassDesign::design('js/widgetv2/react.app.js')?>?t=84"></script>
<?php endif; ?>

<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_footer_widget2_js_extension_multiinclude.tpl.php'));?>

</body>
</html>