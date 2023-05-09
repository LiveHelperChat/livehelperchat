<!DOCTYPE html>

<html <?php if (($detect = new Mobile_Detect()) && ($detect->isMobile() || $detect->isTablet())) : ?>data-mobile="true"<?php endif; ?> <?php if (!isset($Result['anonymous']) && (int)erLhcoreClassModelUserSetting::getSetting('dark_mode',0) == 1) : ?>dark="true" data-bs-theme="dark"<?php endif;?> lang="<?php echo erConfigClassLhConfig::getInstance()->getDirLanguage('content_language')?>" dir="<?php echo erConfigClassLhConfig::getInstance()->getDirLanguage('dir_language')?>" ng-app="lhcApp">
	<head>
		<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head.tpl.php'));?>
	</head>
<body id="admin-body" class="pe-0 h-100 dashboard-height <?php isset($Result['body_class']) ? print $Result['body_class'] : ''?>" ng-cloak ng-controller="LiveHelperChatCtrl as lhc">
<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_top_content_multiinclude.tpl.php'));?>
<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_head_multiinclude.tpl.php'));?>

<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/top_menu.tpl.php'));?>

<div id="wrapper" ng-cloak ng-class="{toggled: lmtoggle}">

    <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/sidemenu/sidemenu.tpl.php'));?>

    <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/sidemenu/sidemenu_chats.tpl.php'));?>

    <div id="page-content-wrapper">

        <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/can_use_chat.tpl.php'));?>
    
        <div class="row">
            <div id="middle-column-page" class="col-xl-12 pb-1">

                <?php if (isset($Result['path'])) : ?>
                    <div><div>
                    <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/path.tpl.php'));?>
                <?php endif; ?>

                    <?php echo $Result['content']; ?>

                <?php if (isset($Result['path'])) : ?>
                    </div></div>
                <?php endif; ?>

            </div>
        </div>
    
    </div>

</div>


<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_footer.tpl.php'));?>

<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_bottom_content_multiinclude.tpl.php'));?>

<?php if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
		$debug = ezcDebug::getInstance();
        echo "<div><pre class='bg-light text-dark m-2 p-2 border'>" . json_encode(erLhcoreClassUser::$permissionsChecks, JSON_PRETTY_PRINT) . "</pre></div>";
		echo $debug->generateOutput();
} ?>

</body>
</html>