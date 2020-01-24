#lhc_status_container #status-icon {
    background-color:#<?php print $theme->onl_bcolor?>;
    border-color:#<?php print $theme->bor_bcolor?>;
}

<?php if ($theme->online_image_url != '') : ?>
    #lhc_status_container #status-icon{
        background-image: url(<?php echo $theme->online_image_url?>);
        background-repeat: no-repeat;
        background-position: center center;
    }
    #lhc_status_container #status-icon:before{
        content:'';
    }
<?php endif; ?>

<?php if ($theme->offline_image_url != '') : ?>
#lhc_status_container .offline-status {
    background-image: url(<?php echo $theme->offline_image_url?>)!important;
    background-repeat: no-repeat;
    background-position: center center;
    :before
}
#lhc_status_container .offline-status:before{
    content:'';
}
<?php endif; ?>

<?php echo $theme->custom_status_css;?>