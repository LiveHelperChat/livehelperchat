<?php $modalHeaderTitle = htmlspecialchars($column->column_name)?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>
<?php echo $column->popup_content?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>
