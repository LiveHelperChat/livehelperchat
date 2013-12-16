<!DOCTYPE html>

<html lang="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site','content_language')?>" dir="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site','dir_language')?>">
<head>
<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head.tpl.php'));?>
</head>
<body>


    <div class="row">
        <div class="columns large-12 pt10">
            <?php echo $Result['content']; ?>
        </div>
    </div>


<script type="text/javascript" language="javascript" src="<?php echo erLhcoreClassDesign::designJS('js/app.js');?>"></script>

<?php if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
		$debug = ezcDebug::getInstance();
		echo $debug->generateOutput();
} ?>

</body>
</html>