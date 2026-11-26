<?php $extHandled = false; ?>

<?php include(erLhcoreClassDesign::designtpl('lhwidgetrestapi/jitsi_executejs.tpl.php')); ?>

<?php include(erLhcoreClassDesign::designtpl('lhwidgetrestapi/executejs_multiinclude.tpl.php')); ?>

<?php if ($extHandled === false) : ?>
    console.log("Unhandled extension JS call");
    console.log(<?php echo json_encode($ext);?>);
<?php endif; ?>


