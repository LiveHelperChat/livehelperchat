<!DOCTYPE html>
<html lang="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'content_language')?>" dir="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language')?>">
<head>
    <?php if (isset($Result['fullheight']) && $Result['fullheight']) : ?>
        <style>
            html, body {
                height: 100% !important;
            }
        </style>
    <?php  endif; ?>
<?php $Result['anonymous'] = true; ?>
<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head_user.tpl.php'));?>

    <?php if (isset($Result['no_mobile_css'])) : ?>
        <link rel="stylesheet" type="text/css" href="<?php echo erLhcoreClassDesign::designCSS('css/no_mobile_widget.css;css/no_mobile_widget_override.css');?>" />
    <?php else : ?>
        <link rel="stylesheet" type="text/css" href="<?php echo erLhcoreClassDesign::designCSS('css/widget.css;css/widget_override.css');?>" />
    <?php endif; ?>

<?php if (isset($Result['theme']) && $Result['theme']->custom_widget_css != '') : ?>
<style type="text/css">
<?php echo $Result['theme']->custom_widget_css?>
</style>
<?php endif;?>

</head>
<body<?php isset($Result['pagelayout_css_append']) ? print ' class="'.$Result['pagelayout_css_append'].'" ' : ''?>>

<div id="widget-layout" class="row<?php (isset($Result['chat'])) ? print ' has-chat' : '';?>">
	<div class="col-12">
        <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/widget/before_widget_content.tpl.php'));?>
            <div id="widget-content-body"><?php echo $Result['content']; ?></div>
        <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/widget/after_widget_content.tpl.php'));?>
     </div>
</div>

<div id="widget-layout-js">
<?php if (isset($Result['dynamic_height'])) : ?>
<script>
var wasFocused = false;
lhinst.isWidgetMode = true;

$('input[type="text"]').first().click(function(){if (wasFocused == false){wasFocused=true;$(this).select().focus();}});
$('textarea').first().click(function(){if (wasFocused == false){wasFocused=true;$(this).select();}});
if (!!window.postMessage) {
    <?php if (!isset($Result['fullheight']) || (isset($Result['fullheight']) && !$Result['fullheight'])) : ?>
        var heightContent = 0;
        var heightElement = $('#widget-layout');
        setInterval(function(){
            var currentHeight = heightElement.height();
            if (heightContent != currentHeight){
                heightContent = currentHeight;
                try {
                    parent.postMessage('<?php echo $Result['dynamic_height_message']?>:'+(parseInt(heightContent)+<?php (isset($Result['dynamic_height_append'])) ? print $Result['dynamic_height_append'] : print 15?>), '*');
                } catch(e) {

                };
            };
        },200);

    <?php endif; ?>
	<?php if (isset($Result['chat']) && is_numeric($Result['chat']->id)) : ?>
	parent.postMessage("lhc_ch:hash:<?php echo $Result['chat']->id,'_',$Result['chat']->hash?>", '*');
	parent.postMessage("lhc_ch:hash_resume:<?php echo $Result['chat']->id,'_',$Result['chat']->hash?>", '*');
	<?php endif; ?>
	<?php if (isset($Result['additional_post_message'])) : ?>
	parent.postMessage("<?php echo $Result['additional_post_message']?>", '*');
	<?php endif;?>
	<?php if (isset($Result['parent_messages'])) : 
	foreach ($Result['parent_messages'] as $msgPArent) : ?>
	parent.postMessage("<?php echo $msgPArent?>", '*');
	<?php endforeach;endif;?>

    function handleCrossMessage(e) {
        if (typeof e.data !== 'string') { return; }
        var action = e.data.split(':')[0];
        if (action == 'lhc_load_ext') {
            const parts = e.data.replace('lhc_load_ext:','').split('::');
            lhinst.executeExtension(parts[0],JSON.parse(parts[1]));
        }
    }

    if ( window.addEventListener ){
        // FF
        window.addEventListener("message", handleCrossMessage, false);
    } else if ( window.attachEvent ) {
        // IE
        window.attachEvent("onmessage", handleCrossMessage);
    } else if ( document.attachEvent ) {
        // IE
        document.attachEvent("onmessage", handleCrossMessage);
    };

    $(window).on('load',function() {
        <?php if (!isset($Result['fullheight']) || (isset($Result['fullheight']) && !$Result['fullheight'])) : ?>
        var currentHeight = heightElement.height();
        if (heightContent != currentHeight){
            heightContent = currentHeight;
            try {
                parent.postMessage('<?php echo $Result['dynamic_height_message']?>:'+(parseInt(heightContent)+<?php (isset($Result['dynamic_height_append'])) ? print $Result['dynamic_height_append'] : print 15?>), '*');
            } catch(e) {

            };
        };
        <?php endif; ?>
        setTimeout(function () {
            parent.postMessage("lhc_widget_loaded", '*');
        },300);
    });

};
</script>
<?php endif;?>
<?php if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
		$debug = ezcDebug::getInstance();
		echo $debug->generateOutput();
} ?>
</div>

</body>
</html>