<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhuser','changevisibility')) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhuser/account/part/visibility_content.tpl.php'));?>
<?php endif; ?>