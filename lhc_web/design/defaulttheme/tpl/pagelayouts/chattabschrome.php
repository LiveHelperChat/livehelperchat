<!DOCTYPE html>

<html <?php if (($detect = new Mobile_Detect()) && ($detect->isMobile() || $detect->isTablet())) : ?>data-mobile="true"<?php endif; ?> lang="<?php echo erConfigClassLhConfig::getInstance()->getDirLanguage('content_language')?>" dir="<?php echo erConfigClassLhConfig::getInstance()->getDirLanguage('dir_language')?>" ng-app="lhcApp">
<head>
<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head.tpl.php'));?>
</head>
<body id="admin-body" ng-controller="LiveHelperChatCtrl as lhc">

<div class="container-fluid">
    <div class="row">
    <div class="col-12">
        <?php echo $Result['content']; ?>
    </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo erLhcoreClassDesign::designJS('js/angular.min.js;js/checklist-model.min.js;js/angular.lhc.min.js');?>"></script>

<?php if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
    $debug = ezcDebug::getInstance();
    echo "<div><pre class='bg-light text-dark m-2 p-2 border'>" . json_encode(erLhcoreClassUser::$permissionsChecks, JSON_PRETTY_PRINT) . "</pre></div>";
    echo $debug->generateOutput();
} ?>

</body>
</html>