<?php if (isset($Result['theme']) && $Result['theme'] !== false) : ?>
<style>
    <?php if ($Result['theme']->buble_visitor_background != '') : ?>
    div.message-row.response{background-color:#<?php echo htmlspecialchars($Result['theme']->buble_visitor_background)?>;}
    <?php endif;?>
    
    <?php if ($Result['theme']->buble_visitor_text_color != '') : ?>
    div.message-row.response{color:#<?php echo htmlspecialchars($Result['theme']->buble_visitor_text_color)?>;}
    <?php endif;?>
    
    <?php if ($Result['theme']->buble_visitor_title_color != '') : ?>
    .vis-tit{color:#<?php echo htmlspecialchars($Result['theme']->buble_visitor_title_color)?>;}
    <?php endif;?>
    
    <?php if ($Result['theme']->buble_operator_background != '') : ?>
    div.message-admin{background-color:#<?php echo htmlspecialchars($Result['theme']->buble_operator_background)?>;}
    <?php endif;?>
    
    <?php if ($Result['theme']->buble_operator_text_color != '') : ?>
    div.message-admin{color:#<?php echo htmlspecialchars($Result['theme']->buble_operator_text_color)?>;}
    <?php endif;?>
    
    <?php if ($Result['theme']->buble_operator_title_color != '') : ?>
    .op-tit{color:#<?php echo htmlspecialchars($Result['theme']->buble_operator_title_color)?>;}
    <?php endif;?>
</style>
<?php endif;?>