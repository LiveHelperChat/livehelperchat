<?php 
/**
 * Admin theme
 */
?>
<?php 
$adminThemeId = erLhcoreClassModelChatConfig::fetch('default_admin_theme_id')->current_value;
if ($adminThemeId  > 0 ) {
    $adminTheme = erLhAbstractModelAdminTheme::fetch($adminThemeId);
    if ($adminTheme instanceof erLhAbstractModelAdminTheme) { echo $adminTheme->header_content_front; ?>
        <link rel="stylesheet" type="text/css" href="<?php echo erLhcoreClassDesign::baseurl('theme/admincss');?>/<?php echo $adminTheme->id?>" />
    <?php };
}; ?>

