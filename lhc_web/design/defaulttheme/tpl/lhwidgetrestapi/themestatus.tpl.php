#lhc_status_container #status-icon {
    background-color:#<?php print $theme->onl_bcolor?>!important;
    border-color:#<?php print $theme->bor_bcolor?>!important;
}

<?php if (isset($theme->bot_configuration_array['offl_bcolor']) && $theme->bot_configuration_array['offl_bcolor'] != '') : ?>
#lhc_status_container #status-icon.offline-status {
    background-color:#<?php print $theme->bot_configuration_array['offl_bcolor']?>!important;
}
<?php endif; ?>

<?php if (isset($theme->bot_configuration_array['offlbor_bcolor']) && $theme->bot_configuration_array['offlbor_bcolor'] != '') : ?>
#lhc_status_container #status-icon.offline-status {
    border-color:#<?php print $theme->bot_configuration_array['offlbor_bcolor']?>!important;
}
<?php endif; ?>

<?php if ($theme->text_color != '') : ?>
#lhc_status_container #status-icon {
    color:#<?php print $theme->text_color?>!important;
}
<?php endif; ?>

<?php if (isset($theme->bot_configuration_array['offltxt_color']) && $theme->bot_configuration_array['offltxt_color'] != '') : ?>
#lhc_status_container #status-icon.offline-status {
    color:#<?php print $theme->bot_configuration_array['offltxt_color']?>!important;
}
<?php endif; ?>

<?php if ($theme->online_image_url != '') : ?>
    #lhc_status_container #status-icon:not(.close-status){
        background-image: url(<?php echo $theme->online_image_url?>)!important;
        background-repeat: no-repeat!important;
        background-position: center center!important;
    }
    #lhc_status_container #status-icon:not(.close-status):before{
        content:''!important;
    }
<?php endif; ?>

<?php if ($theme->offline_image_url != '') : ?>
#lhc_status_container #status-icon.offline-status:not(.close-status) {
    background-image: url(<?php echo $theme->offline_image_url?>)!important;
    background-repeat: no-repeat!important;
    background-position: center center!important;
}
#lhc_status_container #status-icon.offline-status:not(.close-status):before{
    content:''!important;
}
<?php endif; ?>

<?php if ($theme->close_image_url != '') : ?>
    #lhc_status_container #status-icon.close-status {
    background-image: url(<?php echo $theme->close_image_url?>)!important;
    background-repeat: no-repeat!important;
    background-position: center center!important;
    }
    #lhc_status_container #status-icon.close-status:before{
    content:''!important;
    }
<?php endif; ?>

<?php echo $theme->custom_status_css;?>