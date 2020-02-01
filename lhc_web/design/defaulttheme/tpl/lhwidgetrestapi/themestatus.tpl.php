#lhc_status_container #status-icon {
    background-color:#<?php print $theme->onl_bcolor?>!important;
    border-color:#<?php print $theme->bor_bcolor?>!important;
}

<?php if ($theme->online_image_url != '') : ?>
    #lhc_status_container #status-icon{
        background-image: url(<?php echo $theme->online_image_url?>)!important;
        background-repeat: no-repeat!important;
        background-position: center center!important;
    }
    #lhc_status_container #status-icon:before{
        content:''!important;
    }
<?php endif; ?>

<?php if ($theme->offline_image_url != '') : ?>
#lhc_status_container #status-icon.offline-status {
    background-image: url(<?php echo $theme->offline_image_url?>)!important;
    background-repeat: no-repeat!important;
    background-position: center center!important;
}
#lhc_status_container #status-icon.offline-status:before{
    content:''!important;
}
<?php endif; ?>

<?php echo $theme->custom_status_css;?>