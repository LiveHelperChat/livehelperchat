<?php if ($chat->product !== false) : ?>
<tr>
    <td colspan="2">
        <h6 class="fw-bold"><i class="material-icons">shopping_cart</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Product')?></h6>
        <div class="text-muted pb-1">
            <?php echo htmlspecialchars($chat->product);?>
        </div>
    </td>
</tr>
<?php endif;?>