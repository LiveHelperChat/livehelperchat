<form action="" method="get">
    <div class="row">

        <div class="col-md-12">
            <div class="form-group">
                <label><input type="checkbox" <?php echo $input_form->category == 1 ? print 'checked="checked"' : ''?> value="1" name="category" /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Exclude personal responses');?></label>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <input type="submit" class="btn btn-secondary" value="Search" name="doSearch">
            </div>
        </div>

    </div>
</form>