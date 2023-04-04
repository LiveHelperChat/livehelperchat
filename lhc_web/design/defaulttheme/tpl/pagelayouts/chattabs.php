<!DOCTYPE html>

<html lang="<?php echo erConfigClassLhConfig::getInstance()->getDirLanguage('content_language')?>" <?php if (!isset($Result['anonymous']) && (int)erLhcoreClassModelUserSetting::getSetting('dark_mode',0) == 1) : ?>dark="true" data-bs-theme="dark"<?php endif;?> dir="<?php echo erConfigClassLhConfig::getInstance()->getDirLanguage('dir_language')?>" ng-app="lhcApp">
<head>
<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head.tpl.php'));?>
</head>
<body id="admin-body" class="<?php isset($Result['body_class']) ? print $Result['body_class'] : ''?>" ng-controller="LiveHelperChatCtrl as lhc">

<div id="wrapper">
    <div class="container-fluid<?php if (isset($Result['container_class'])) : ?> <?php echo $Result['container_class']?><?php endif; ?>" id="page-content-wrapper">
        <div class="row">
            <div id="middle-column-page" class="col-md-12 pt-1">
                <?php echo $Result['content']; ?>
            </div>
        </div>
    </div>
</div>

<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_footer_js.tpl.php'));?>
<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_footer_js_extension_multiinclude.tpl.php'));?>

<?php if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
    $debug = ezcDebug::getInstance();
    echo "<div><pre class='bg-light text-dark m-2 p-2 border'>" . json_encode(erLhcoreClassUser::$permissionsChecks, JSON_PRETTY_PRINT) . "</pre></div>";
    echo $debug->generateOutput();
} ?>

</body>
</html>