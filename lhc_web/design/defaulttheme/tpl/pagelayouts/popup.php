<!DOCTYPE html>

<html <?php if (!isset($Result['anonymous']) && (int)erLhcoreClassModelUserSetting::getSetting('dark_mode',0) == 1) : ?>dark="true" data-bs-theme="dark"<?php endif;?> lang="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site','content_language')?>" dir="<?php echo erConfigClassLhConfig::getInstance()->getOverrideValue('site','dir_language')?>">
<head>
<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head.tpl.php'));?>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 pt-1">
            <?php echo $Result['content']; ?>
        </div>
    </div>
</div>

<?php if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
    $debug = ezcDebug::getInstance();
    echo "<div><pre class='bg-light text-dark m-2 p-2 border'>" . json_encode(erLhcoreClassUser::$permissionsChecks, JSON_PRETTY_PRINT) . "</pre></div>";
    echo $debug->generateOutput();
} ?>

</body>
</html>