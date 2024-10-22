<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/import','Import bot');?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/import','Bot imported'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post" autocomplete="off" enctype="multipart/form-data">
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose Rest API');?></label>
        <?php
        $params = array (
            'input_name'     => 'rest_api',
            'display_name'   => 'name',
            'css_class'      => 'form-control form-control-sm',
            'selected_id'    => 0,
            'optional_field' => 'Select',
            'list_function'  => 'erLhcoreClassModelGenericBotRestAPI::getList',
        );
        echo erLhcoreClassRenderHelper::renderCombobox( $params ); ?>
        <p><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/import','In case you have imported the Rest API already. You can make sure the imported bot uses the chosen Rest API.')?></small></p>
    </div>


    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/import','File')?> (json)</label>
        <input type="file" accept=".json" name="botfile" value="" />
    </div>

    <input type="submit" name="ImportBot" class="btn btn-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/import','Import')?>" />

</form>
