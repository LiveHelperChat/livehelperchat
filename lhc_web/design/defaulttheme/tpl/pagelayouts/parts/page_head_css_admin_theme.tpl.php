<?php 
/**
 * Admin theme
 */
?>
<?php 
$adminThemeId = erLhcoreClassModelChatConfig::fetch('default_admin_theme_id')->current_value;
if ($adminThemeId  > 0 ) {
    $adminTheme = erLhAbstractModelAdminTheme::fetch($adminThemeId);

    if ($adminTheme instanceof erLhAbstractModelAdminTheme) {
        echo $adminTheme->header_content_front;
    };
}; ?>
<?php if ($adminTheme->header_css != '') : ?>
<style>
    <?php echo htmlspecialchars($adminTheme->header_css)?>
</style>
<?php endif;?>