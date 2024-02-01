<form ng-non-bindable action="<?php echo $input->form_action?>" method="get" name="SearchFormRight" class="pb-2" autocomplete="off">

    <input type="hidden" name="doSearch" value="1">

    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Mail');?></label>
                <input type="text" class="form-control form-control-sm" name="mail" value="<?php echo htmlspecialchars($input->mail)?>" />
            </div>
        </div>
        <div class="col-md-2">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Mailbox status');?></label>
            <select name="active" class="form-control form-control-sm">
                <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Any');?></option>
                <option value="1" <?php $input->active === 1 ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Active');?></option>
                <option value="0" <?php $input->active === 0 ? print 'selected="selected"' : ''?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Non-Active');?></option>
            </select>
        </div>
        <div class="col-md-2">
                <label><input type="checkbox" <?php if ($input->failed == true) : ?>checked="checked"<?php endif; ?> name="failed" value="on" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Only failed');?></label>
        </div>
        <div class="col-md-2">
                <label><input type="checkbox" <?php if ($input->sync_status == true) : ?>checked="checked"<?php endif; ?> name="sync_status" value="on" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','In progress');?></label>
        </div>
    </div>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" name="doSearch" class="btn btn-secondary btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />
    </div>

</form>
