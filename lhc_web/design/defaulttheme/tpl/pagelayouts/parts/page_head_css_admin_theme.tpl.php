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
        
        if ($adminTheme->header_css != '') {
            echo '<style>',$adminTheme->header_css,'</style>';
        }
    };
}; ?>