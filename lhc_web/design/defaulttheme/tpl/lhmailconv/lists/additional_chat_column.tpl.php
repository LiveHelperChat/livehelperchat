<?php if (isset($additional_chat_columns) && !empty($additional_chat_columns)) : foreach ($additional_chat_columns as $additionalColumn) : ?>
<th width="1%" style="white-space: nowrap">
    <?php if ($additionalColumn->column_icon != '') : ?><span class="material-icons text-muted"><?php echo $additionalColumn->column_icon?></span><?php endif; ?><?php echo htmlspecialchars($additionalColumn->column_name)?>
</th>
<?php endforeach;endif; ?>