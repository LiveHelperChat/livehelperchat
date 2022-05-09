<?php if (isset($icons_additional) && !empty($icons_additional)) : ?>
    <?php foreach ($icons_additional as $iconAdditional) : $columnIconData = json_decode($iconAdditional->column_icon,true); ?>
        <?php if (isset($chat->{'cc_' . $iconAdditional->id})) : ?>
        <span <?php if ($iconAdditional->has_popup) : ?>onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT + 'chat/icondetailed/' + <?php echo $chat->id?> + '/' + <?php echo $iconAdditional->id?>});"<?php endif;?> class="material-icons<?php if ($iconAdditional->has_popup) : ?> action-image<?php endif; ?>" title="<?php isset($chat->{'cc_' . $iconAdditional->id . '_tt'}) ? print htmlspecialchars($chat->{'cc_' . $iconAdditional->id . '_tt'}) : print htmlspecialchars(isset($chat->{'cc_' . $iconAdditional->id}) ? $chat->{'cc_' . $iconAdditional->id} : '')?>" style="color: <?php echo isset($columnIconData[$chat->{'cc_' . $iconAdditional->id}]['color']) ? htmlspecialchars($columnIconData[$chat->{'cc_' . $iconAdditional->id}]['color']) : '#CECECE'?>">
            <?php $iconAdditional->column_icon != "" && strpos($iconAdditional->column_icon,'"') !== false ? print htmlspecialchars($columnIconData[$chat->{'cc_' . $iconAdditional->id}]['icon']) : print htmlspecialchars($iconAdditional->column_icon); ?>
        </span>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>