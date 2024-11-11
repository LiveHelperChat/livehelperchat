<form ng-non-bindable action="<?php echo $input->form_action?>" method="get" name="SearchFormRight" autocomplete="off">
    <input type="hidden" name="doSearch" value="1">
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Name');?></label>
                <input type="text" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($input->name)?>" />
            </div>
        </div>
        <div class="col-md-4 pb-2 pt-4">
            <div class="btn-group" role="group" aria-label="...">
                <input type="submit" name="doSearch" class="btn btn-secondary btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />
            </div>
        </div>
    </div>
</form>
