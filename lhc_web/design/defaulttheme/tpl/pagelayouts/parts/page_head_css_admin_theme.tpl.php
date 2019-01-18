<?php
if (erLhcoreClassUser::instance()->isLogged() && (int)erLhcoreClassModelUserSetting::getSetting('admin_theme_enabled',0) == 1 && ($personalTheme = erLhAbstractModelAdminTheme::findOne(array('filter' => array('user_id' => erLhcoreClassUser::instance()->getUserID())))) instanceof erLhAbstractModelAdminTheme) : ?>
    <link rel="stylesheet" type="text/css" href="<?php echo erLhcoreClassDesign::baseurl('theme/admincss');?>/<?php echo $personalTheme->id?>" />
<?php else :
$adminThemeId = erLhcoreClassModelChatConfig::fetch('default_admin_theme_id')->current_value;
if ($adminThemeId  > 0 ) {
    $adminTheme = erLhAbstractModelAdminTheme::fetch($adminThemeId);
    if ($adminTheme instanceof erLhAbstractModelAdminTheme) { echo $adminTheme->header_content_front; ?>
        <link rel="stylesheet" type="text/css" href="<?php echo erLhcoreClassDesign::baseurl('theme/admincss');?>/<?php echo $adminTheme->id?>" />
    <?php };
}; endif; ?>